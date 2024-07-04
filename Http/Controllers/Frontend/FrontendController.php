<?php

namespace Modules\SPTransfer\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Airport;
use App\Models\User;
use App\Support\Money;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\SPTransfer\Models\Enums\Status;
use Modules\SPTransfer\Models\DB_SPTransfer;
use Modules\SPTransfer\Models\DB_SPSettings;

class FrontendController extends Controller
{
    // Open up the index page and provide information
    public function index()
    {
        $statusLabel = null;
        $user = User::with('home_airport', 'journal')->find(Auth::id()); 
        $hubs = Airport::where('hub', 1)->orderby('name')->count();
        $settings = DB_SPSettings::first();  
        $lasttransfer = DB_SPTransfer::where('user_id', $user->id)->last();

        if (!$hubs) {
            flash()->error('No HUBs found.');
            return redirect(route('frontend.dashboard.index'));
        }    

        if ($settings->price > 0) {
            $spfinance = true;
            $spbalance = substr($user->journal->balance->getAmount(), 0, -2);
            $spcost = $settings->price;        

            if ($spfinance && $spbalance < $spcost) {             
                flash()->error('Not enough balance to perform a HUB transfer. You need ' . Money::createFromAmount($spcost) . ' to proceed.');
                return redirect(route('frontend.dashboard.index'));
            }

            $spvalue = Money::createFromAmount($settings->price);
        } else {
            $spfinance = false;
            $spcost = 0;
            $spvalue = 0;
        }        
            
        if ($lasttransfer) {
            if (array_key_exists($lasttransfer->state, Status::$labels)) {
                $statusLabel = Status::$labels[$lasttransfer->state];
            }            

            if ($settings) {
                $daysLimit = $settings->limit;
                $comparisonDate = Carbon::now()->subDays($daysLimit);
            }

            if ($lasttransfer->created_at > $comparisonDate) {
                $limit = 1;
            } else {
                $limit = 0;
            }
        }

        return view('sptransfer::index', [
            'hubs'              => $hubs,
            'current_hub'       => $user->home_airport_id,
            'current_hub_name'  => optional($user->home_airport)->name,
            'lasttransfer'      => $lasttransfer,
            'status'            => $statusLabel,
            'state'             => $lasttransfer->state ?? null,
            'limit'             => $limit ?? null,
            'daysLimit'         => $daysLimit ?? null,
            'spfinance'         => $spfinance,
            'spcost'            => $spcost,
            'spvalue'           => $spvalue,
        ]);
    }

    // User request a HUB Transfer
    public function store(Request $request)
    {
        $user = User::find(Auth::id());
        $settings = DB_SPSettings::first();  

        $request->validate([
            'hub_request' => 'required|string',
            'reason' => 'required|string',
        ]);

        if ($user->home_airport_id === $request->hub_request) {
            flash()->error('You are already assigned to this HUB.');
            return redirect(route('sptransfer.index'));
        }

        $sptransfer = new DB_SPTransfer();
        $sptransfer->hub_initial = $user->home_airport_id;
        $sptransfer->hub_request = $request->hub_request;
        $sptransfer->reason = $request->reason;
        $sptransfer->user_id = Auth::id();
        $sptransfer->state = 0;
        $sptransfer->save();

        if ($settings->price > 0) {
            $user = User::with('airline', 'journal')->find(Auth::id());
            $memo = 'HUB Transfer request to ' . $sptransfer->hub_request;
            $amount = Money::createFromAmount($settings->price);
            $this->ChargeForFreeFlight($user, $amount, $memo);
        }

        Log::debug('SPTransfer | Transfer from ' . strtoupper($sptransfer->hub_initial) . ' to ' . strtoupper($sptransfer->hub_request) . ' requested by ' . Auth::user()->name_private);
        flash()->success('Transfer request submitted.');

        return redirect(route('sptransfer.index'));
    }

    // Charge user for a transfer
    public function ChargeForFreeFlight($user, $amount, $memo)
    {
        $financeSvc = app(FinanceService::class);

        // Charge User
        $financeSvc->debitFromJournal(
            $user->journal,
            $amount,
            $user,
            $memo,
            'HUB Transfer Fees',
            'sptransfer',
            Carbon::now()->format('Y-m-d')
        );

        // Credit Airline
        $financeSvc->creditToJournal(
            $user->airline->journal,
            $amount,
            $user,
            $memo . ' UserID:' . $user->id,
            'HUB Transfer Fees',
            'sptransfer',
            Carbon::now()->format('Y-m-d')
        );

        // Note Transaction
        Log::debug('SPTransfer | UserID: ' . $user->id . ' Name: ' . $user->name_private . ' charged for ' . $memo);
    }
}



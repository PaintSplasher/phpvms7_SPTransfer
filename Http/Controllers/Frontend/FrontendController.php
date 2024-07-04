<?php

namespace Modules\SPTransfer\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Airport;
use App\Models\User;
use App\Services\FinanceService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\SPTransfer\Models\DB_SPTransfer;
use Modules\SPTransfer\Models\DB_SPSettings;
use Modules\SPTransfer\Models\Enums\Status;

class FrontendController extends Controller
{
    // Open up the index page and provide information
    public function index()
    {
        $user = User::with('home_airport', 'journal')->find(Auth::id()); 
        $hubs = Airport::where('hub', 1)->orderby('name')->count();
        $settings = DB_SPSettings::first();  
        $lasttransfer = DB_SPTransfer::where('user_id', $user->id)->latest();

        if (!$hubs) {
            flash()->error('No HUBs found.');
            return redirect(route('frontend.dashboard.index'));
        }    

        if ($settings->price > 0) {
            $spfinance = true;
            $spcost = $settings->price;
            $spvalue = Money::createFromAmount($settings->price);

            if ($user->journal->balance < $spvalue) {             
                flash()->error('Not enough balance to perform a HUB transfer. You need ' . $spvalue . ' to proceed.');
                return redirect(route('frontend.dashboard.index'));
            }
        }        
            
        if ($lasttransfer) {        
            $statusLabel = (array_key_exists($lasttransfer->state, Status::$labels)) ? Status::$labels[$lasttransfer->state] : null;
            $daysLimit = filled($settings->limit) ? $settings->limit : 0;
            $limit = ($lasttransfer->created_at > Carbon::now()->subDays($daysLimit)) ? 1 : 0;
        }

        return view('sptransfer::index', [
            'hubs'              => $hubs,
            'current_hub'       => $user->home_airport_id,
            'current_hub_name'  => optional($user->home_airport)->name,
            'lasttransfer'      => $lasttransfer,
            'status'            => isset($statusLabel) ? $statusLabel : null,
            'state'             => $lasttransfer->state ?? null,
            'limit'             => $limit ?? null,
            'daysLimit'         => $daysLimit ?? null,
            'spfinance'         => isset($spfinance) ? $spfinance : false,
            'spcost'            => isset($spcost) ? $spcost : 0,
            'spvalue'           => isset($spvalue) ? $spvalue : 0,
        ]);
    }

    // User request a HUB Transfer
    public function store(Request $request)
    {
        $user = User::with('airline.journal', 'journal')->find(Auth::id());
        $settings = DB_SPSettings::first();  

        $request->validate([
            'hub_request_id' => 'required|string',
            'reason'         => 'required|string',
        ]);

        if ($user->home_airport_id === $request->hub_request_id) {
            flash()->error('You are already assigned to this HUB.');
            return redirect(route('sptransfer.index'));
        }

        $sptransfer = DB_SPTransfer::create([
            'hub_initial_id' => $user->home_airport_id,
            'hub_request_id' => $request->hub_request_id,
            'reason'         => $request->reason,
            'user_id'        => $user->id,
            'state'          => 0,
        ]);

        if ($settings->price > 0) {
            $memo = 'HUB Transfer request to ' . $sptransfer->hub_request_id;
            $amount = Money::createFromAmount($settings->price);
            $this->ChargeForFreeFlight($user, $amount, $memo);
        }

        Log::debug('SPTransfer | Transfer from ' . strtoupper($sptransfer->hub_initial_id) . ' to ' . strtoupper($sptransfer->hub_request_id) . ' requested by ' . $user->name_private);
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



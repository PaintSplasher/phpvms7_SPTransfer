<?php

namespace Modules\SPTransfer\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Airline;
use App\Models\User;
use App\Services\FinanceService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\SPTransfer\Events\AirlineChangeRequest;
use Modules\SPTransfer\Models\DB_SPSettings;
use Modules\SPTransfer\Models\DB_SPTransfer;
use Modules\SPTransfer\Models\Enums\Status;

class AirlineFrontendController extends Controller
{
    // Open up the index page and provide information
    public function index()
    {
        $user = User::with('home_airport', 'journal')->find(Auth::id());
        $airlines = Airline::where('active', 1)->orderBy('icao')->select('id', 'icao', 'name')->get();
        $settings = DB_SPSettings::where('id', 2)->first();
        $last_transfer = DB_SPTransfer::where('user_id', $user->id)->where('airline', 1)->latest()->first();

        if (!$airlines) {
            flash()->error('No airlines found.');

            return redirect(route('frontend.dashboard.index'));
        }

        if ($settings->price > 0) {
            $spfinance = true;
            $spcost = $settings->price;
            $spvalue = Money::createFromAmount($settings->price);

            if ($user->journal->balance < $spvalue) {
                flash()->error('Not enough balance to perform an airline transfer. You need '.$spvalue.' to proceed.');

                return redirect(route('frontend.dashboard.index'));
            }
        }

        if ($last_transfer) {
            $statusLabel = filled($last_transfer->state) ? Status::label($last_transfer->state) : null;
            $daysLimit = filled($settings->limit) ? $settings->limit : 0;
            $limit = ($last_transfer->created_at > Carbon::now()->subDays($daysLimit)) ? 1 : 0;
            $airlineNameRequest = $airlines->firstWhere('id', $last_transfer->hub_request_id);
        }

        return view('sptransfer::airline', [
            'airlines'              => $airlines,
            'current_airline'       => $user->airline->icao,
            'current_airline_name'  => $user->airline->name,
            'request_airline'       => isset($airlineNameRequest) ? $airlineNameRequest : null,
            'lasttransfer'          => isset($last_transfer) ? $last_transfer : null,
            'status'                => isset($statusLabel) ? $statusLabel : null,
            'state'                 => $last_transfer->state ?? null,
            'limit'                 => $limit ?? null,
            'daysLimit'             => $daysLimit ?? null,
            'spfinance'             => isset($spfinance) ? $spfinance : false,
            'spcost'                => isset($spcost) ? $spcost : 0,
            'spvalue'               => isset($spvalue) ? $spvalue : 0,
            'reject_reason'         => isset($last_transfer->reject_reason),
            'charge_type'           => $settings->charge_type,
        ]);
    }

    // User request a airline Transfer
    public function store(Request $request)
    {
        $user = User::with('airline.journal', 'journal')->find(Auth::id());
        $airlines = Airline::where('active', 1)->orderBy('icao')->select('id', 'icao', 'name')->get();
        $settings = DB_SPSettings::where('id', 2)->first();

        $request->validate([
            'airline_request_id' => 'required|string',
            'reason'             => 'required|string',
        ]);

        $airlineNameinitial = $airlines->firstWhere('id', $user->airline->id);
        $airlineNameRequest = $airlines->firstWhere('id', $request->airline_request_id);

        if ((int) $user->airline->id === (int) $request->airline_request_id) {
            flash()->error('You are already assigned to this airline.');

            return redirect(route('sptransfer.airline.index'));
        }

        $sptransfer = DB_SPTransfer::create([
            'hub_initial_id'   => $user->airline->id,
            'hub_request_id'   => $request->airline_request_id,
            'reason'           => $request->reason,
            'user_id'          => $user->id,
            'state'            => 0,
            'airline'          => 1,
        ]);

        if ($settings->price > 0) {
            if ($settings->charge_type === 0) {
                $memo = 'Airline transfer request to '.$airlineNameRequest->name;
                $amount = Money::createFromAmount($settings->price);
                $this->ChargeForFreeFlight($user, $amount, $memo);
            } else {
                $memo = 'Airline transfer request to '.$airlineNameRequest->name;
            }
        }

        Log::debug('SPTransfer(Airline) | Transfer from '.strtoupper($airlineNameinitial->icao).' to '.strtoupper($airlineNameRequest->icao).' requested by '.$user->name_private);

        event(new AirlineChangeRequest($sptransfer));

        flash()->success('Airline Transfer request submitted.');

        return redirect(route('sptransfer.airline.index'));
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
            'Airline Transfer Fees',
            'sptransfer',
            Carbon::now()->format('Y-m-d')
        );

        // Credit Airline
        $financeSvc->creditToJournal(
            $user->airline->journal,
            $amount,
            $user,
            $memo.' UserID:'.$user->id,
            'Airline Transfer Fees',
            'sptransfer',
            Carbon::now()->format('Y-m-d')
        );

        // Note Transaction
        Log::debug('SPTransfer(Airline) | UserID: '.$user->id.' Name: '.$user->name_private.' charged for '.$memo.' on request.');
    }
}

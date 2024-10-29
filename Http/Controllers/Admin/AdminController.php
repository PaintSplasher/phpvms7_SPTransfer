<?php

namespace Modules\SPTransfer\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\User;
use App\Services\FinanceService;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\SPTransfer\Models\DB_SPTransfer;
use Modules\SPTransfer\Models\DB_SPSettings;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    // Open up the admin page and show the transfers
    public function index()
    {
        $requests = DB_SPTransfer::with('user')->sortable(['created_at' => 'desc'])->paginate(10);
        $settings = DB_SPSettings::first();

        return view('sptransfer::admin.index', [
            'requests' => $requests,
            'settings' => $settings,
        ]);
    }

    // Handle the request
    public function update(Request $request)
    {
        $sptransfer = DB_SPTransfer::find($request->id);
        $user = User::find($request->user_id);
    
        if ($sptransfer && $user && $request->decision === 'ack') {
            // Approve Request and Update User Home Airport
            $settings = DB_SPSettings::first();

            if ($settings->charge_type === 1 && $settings->price > 0) {
                $memo = 'HUB Transfer request to ' . $sptransfer->hub_request_id;
                $amount = Money::createFromAmount($settings->price);
                $this->ChargeForFreeFlight($user, $amount, $memo);
            }

            $sptransfer->state = 1;
            $sptransfer->reject_reason = $request->input('reason', null);
            $sptransfer->save();
            $user->home_airport_id = $sptransfer->hub_request_id;
            $user->save();
            flash()->success('Transfer request approved.');
        } elseif ($sptransfer && $request->decision === 'rej') {
            // Reject Request
            $sptransfer->state = 2;          
            $sptransfer->reject_reason = $request->input('reason', '-');
            $sptransfer->save();
            flash()->warning('Transfer request rejected.');
        } elseif ($sptransfer && $request->decision === 'del') {
            // Delete Request
            $sptransfer->delete();
            flash()->error('Transfer request deleted.');
        } else {
            flash()->error('Nothing done.');
        }    
        return redirect(route('admin.sptransfer.index'));
    } 

    // Save admin settings
    public function storeSettings(Request $request)
    {
        $settings = DB_SPSettings::find($request->id);

        if ($settings) {
            $settings->charge_type = $request->sp_charge;
            $settings->price = $request->sp_price;
            $settings->limit = $request->sp_days;
            $settings->discord_url = $request->sp_discordurl;
            $settings->save();
            flash()->success('Settings saved.');
        } else {
            DB_SPSettings::create([
                'sp_charge'   => $request->sp_charge,
                'price'       => $request->sp_price,
                'limit'       => $request->sp_days,
                'discord_url' => $request->sp_discordurl,
            ]);
            flash()->success('Initial settings created.');
        }

        return redirect(route('admin.sptransfer.index'));
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
        Log::debug('SPTransfer | UserID: ' . $user->id . ' Name: ' . $user->name_private . ' charged for ' . $memo . ' by approval.');
    }
    
}

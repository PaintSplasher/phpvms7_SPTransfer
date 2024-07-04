<?php

namespace Modules\SPTransfer\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Modules\SPTransfer\Models\DB_SPTransfer;
use Modules\SPTransfer\Models\DB_SPSettings;
use Modules\SPTransfer\Models\Enums\Status;


class AdminController extends Controller
{
    // Open up the admin page and show the transfers
    public function index()
    {   
        $requests = DB_SPTransfer::with('user')->sortable(['created_at' => 'desc'])->paginate();
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
            $sptransfer->state = 1;
            $sptransfer->save();
            $user->home_airport_id = $sptransfer->hub_request_id;
            $user->save();
            flash()->success('Transfer request approved.');
        } elseif ($sptransfer && $request->decision === 'rej') {
            // Reject Request
            $sptransfer->state = 2;
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
        $validated = $request->validate([
            'sp_price' => 'required|numeric|min:0|max:100000',
            'sp_days' => 'required|numeric|min:0|max:1000',
        ]);

        $existingRecord = DB_SPSettings::find(1);

        if ($existingRecord) {
            DB_SPSettings::where('id', 1)->update([
                'price' => $request->input('sp_price'),
                'limit' => $request->input('sp_days'),
                'updated_at' => now(),
            ]);
        } else {
            DB_SPSettings::create([
                'id' => 1,
                'price' => $request->input('sp_price'),
                'limit' => $request->input('sp_days'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }         

        Flash::success('Settings saved.');
        return redirect(route('admin.sptransfer.index'));
    }
    
}

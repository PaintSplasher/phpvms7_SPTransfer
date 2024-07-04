<?php

namespace Modules\SPTransfer\Http\Controllers\Admin;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Modules\SPTransfer\Models\Enums\Status;
use Modules\SPTransfer\Models\DB_SPTransfer;
use Modules\SPTransfer\Models\DB_SPSettings;
use Laracasts\Flash\Flash;

class AdminController extends Controller
{
    // Open up the admin page and show the transfers
    public function index()
    {   
        $requests = DB::table('sptransfer')
        ->join('users', 'sptransfer.user_id', '=', 'users.id')
        ->select('sptransfer.*', 'users.name as name')
        ->orderBy('sptransfer.created_at', 'desc')
        ->get();

        $requests->transform(function ($request) {
            if (array_key_exists($request->state, Status::$labels)) {
                $request->statusLabel = Status::$labels[$request->state];
            }            
            $request->created_at = Carbon::parse($request->created_at);
           
            return $request;
        });  

        $settings = DB::table('sptransfer_settings')->get();

        return view('sptransfer::admin.index', [
            'requests'      => $requests, 
            'settings'      => $settings[0],
        ]);  
    }

    // Accept the request
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:sptransfer,id',
        ]);

        DB::transaction(function () use ($validated) {
            DB_SPTransfer::where('id', $validated['id'])->update(['state' => 1]);

            $sptransfer = DB_SPTransfer::find($validated['id']);

            $user = User::find($sptransfer->user_id);
            $user->update(['home_airport_id' => $sptransfer->hub_request]);
        });

        Flash::success('Transfer request approved.');
        return redirect(route('admin.sptransfer.index'));
    }

    // Reject the request
    public function deny(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:sptransfer,id',
        ]);

        DB_SPTransfer::where('id', $validated['id'])->update(['state' => 2]);

        Flash::warning('Transfer request rejected.');
        return redirect(route('admin.sptransfer.index'));
    }

    // Delete the request
    public function delete(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:sptransfer,id',
        ]);

        DB_SPTransfer::where('id', $validated['id'])->delete();

        Flash::error('Transfer request deleted.');
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

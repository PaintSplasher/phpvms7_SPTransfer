<?php

namespace Modules\SPTransfer\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\SPTransfer\Events\HubChangeRequest;
use Modules\SPTransfer\Models\DB_SPSettings;
use Modules\SPTransfer\Services\NotificationServices;

class HubChangeRequestListener
{
    public function handle(HubChangeRequest $event)
    {
        $setting = DB_SPSettings::first();
        $wh_url = filled($setting) ? $setting->discord_url : null;

        Log::debug('Hub Transfer Event Listener working');

        if (filled($wh_url)) {
            $transfer = $event->transfer;
            $transfer->loadMissing('user');
            // Send Discord Notification
            $NotificationSvc = app(NotificationServices::class);
            $NotificationSvc->TransferMessage($transfer);
        }
    }
}

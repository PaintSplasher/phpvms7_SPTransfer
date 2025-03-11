<?php

namespace Modules\SPTransfer\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\SPTransfer\Events\AirlineChangeRequest;
use Modules\SPTransfer\Models\DB_SPSettings;
use Modules\SPTransfer\Services\AirlineNotificationServices;

class AirlineChangeRequestListener
{
    public function handle(AirlineChangeRequest $event)
    {
        $setting = DB_SPSettings::first();
        $wh_url = filled($setting) ? $setting->discord_url : null;

        Log::debug('Airline Transfer Event Listener working');

        if (filled($wh_url)) {
            $transfer = $event->transfer;
            $transfer->loadMissing('user');
            // Send Discord Notification
            $NotificationSvc = app(AirlineNotificationServices::class);
            $NotificationSvc->TransferMessage($transfer);
        }
    }
}

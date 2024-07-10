<?php

namespace Modules\SPTransfer\Listeners;

use App\Events\TransferEvent;
use Modules\SPTransfer\Services\NotificationServices;

/**
 * A sample event listener
 */
class TransferEventListener
{
    public function handle(TransferEvent $event) {
        $transfer = $event->transfer;
        $transfer->loadMissing('user');

        if (DB_Setting('dbasic.discord_newsmsg', true)) {
            // Send Discord Notification
            $NotificationSvc = app(NotificationServices::class);
            $NotificationSvc->NewsMessage($transfer);
        }
    }
}
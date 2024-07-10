<?php

namespace Modules\SPTransfer\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\SPTransfer\Events\HubChangeRequest;
use Modules\SPTransfer\Services\NotificationServices;

class HubChangeRequestListener
{
    public function handle(HubChangeRequest $event) {

        $transfer = $event->transfer;
        // $transfer->loadMissing('user');

        Log::debug('Hub Transfer Event Listener working');
    }
}
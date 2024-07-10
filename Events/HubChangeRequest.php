<?php

namespace Modules\SPTransfer\Events;

use App\Contracts\Event;
use Modules\SPTransfer\Models\DB_SPTransfer;

class HubChangeRequest extends Event
{
    public DB_SPTransfer $transfer;

    public function __construct(DB_SPTransfer $transfer)
    {
        $this->transfer = $transfer;
    }
}

<?php

namespace Modules\SPTransfer\Providers;

use Modules\SPTransfer\Events\HubChangeRequest;
use Modules\SPTransfer\Listeners\HubChangeRequestListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        HubChangeRequest::class => [HubChangeRequestListener::class],
    ];

    public function boot()
    {
        parent::boot();
    }
}

<?php

namespace Modules\SPTransfer\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\SPTransfer\Events\AirlineChangeRequest;
use Modules\SPTransfer\Events\HubChangeRequest;
use Modules\SPTransfer\Listeners\AirlineChangeRequestListener;
use Modules\SPTransfer\Listeners\HubChangeRequestListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        HubChangeRequest::class     => [HubChangeRequestListener::class],
        AirlineChangeRequest::class => [AirlineChangeRequestListener::class],
    ];

    public function boot()
    {
        parent::boot();
    }
}

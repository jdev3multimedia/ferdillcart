<?php

namespace App\Providers;

use App\Models\BusinessSetting;
use App\Models\DataSetting;
use App\Models\Order;
use App\Models\Module;
use App\Models\Banner;
use App\Observers\BusinessSettingObserver;
use App\Observers\BannerObserver;
use App\Observers\DataSettingObserver;
use App\Observers\OrderObserver;
use App\Observers\ModuleObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \Laravel\Reverb\Events\MessageReceived::class => [
        \App\Listeners\HandleClientMessage::class,
    ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Order::observe(OrderObserver::class);
        BusinessSetting::observe(BusinessSettingObserver::class);
        Banner::observe(BannerObserver::class);
        DataSetting::observe(DataSettingObserver::class);
        Module::observe(ModuleObserver::class);
    }
}

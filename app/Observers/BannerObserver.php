<?php

namespace App\Observers;

use App\Models\Banner;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class BannerObserver
{
    /**
     * Handle the Banner "created" event.
     */
    public function created(Banner $Banner): void
    {
        $this->refreshBannersCache();
    }

    /**
     * Handle the Banner "updated" event.
     */
    public function updated(Banner $Banner): void
    {
        $this->refreshBannersCache();
    }

    /**
     * Handle the Banner "deleted" event.
     */
    public function deleted(Banner $Banner): void
    {
        $this->refreshBannersCache();
    }

    /**
     * Handle the Banner "restored" event.
     */
    public function restored(Banner $Banner): void
    {
        $this->refreshBannersCache();
    }

    /**
     * Handle the Banner "force deleted" event.
     */
    public function forceDeleted(Banner $Banner): void
    {
        $this->refreshBannersCache();
    }

    private function refreshBannersCache()
    {
        $prefix = 'banners_';
        $cacheKeys = DB::table('cache')
            ->where('key', 'like', "%" . $prefix . "%")
            ->pluck('key');
        $appName = env('APP_NAME').'_cache';
        $remove_prefix = strtolower(str_replace('=', '', $appName));
        $sanitizedKeys = $cacheKeys->map(function ($key) use ($remove_prefix) {
            $key = str_replace($remove_prefix, '', $key);
            return $key;
        });
        foreach ($sanitizedKeys as $key) {
            Cache::forget($key);
        }
    }
}

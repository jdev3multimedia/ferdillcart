<?php

namespace App\Traits;

use App\CentralLogics\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

trait FileManagerTrait
{
    public static function upload(string $dir, string $format, $image = null): string
    {
        return Helpers::upload($dir, $format, $image);
    }

    public static function updateAndUpload(string $dir, $old_image, string $format, $image = null): mixed
    {
        return Helpers::update($dir, $old_image, $format, $image);
    }

    public static function getDisk(): string
    {
        $config=Helpers::get_business_settings('local_storage');

        return isset($config)?($config==0?'s3':'public'):'public';
    }
}

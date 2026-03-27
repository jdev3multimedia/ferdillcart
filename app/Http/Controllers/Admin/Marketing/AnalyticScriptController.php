<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\AnalyticScript;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class AnalyticScriptController extends Controller
{
    public function analyticSetup()
    {
        $analytics =  AnalyticScript::select(['id', 'type', 'script_id','is_active'])->get();
        $analyticsData = [];
        foreach ($analytics as $analytic) {
            $analyticsData[$analytic['type']] = $analytic;
        }
        $analyticsTools= $this->dataArray();
        return view('admin-views.business-settings.analytics.index', compact('analyticsData','analyticsTools'));
    }
    public function analyticUpdate(Request $request)
    {
         $analyticScriptsTypes = ['meta_pixel', 'linkedin_insight', 'tiktok_tag', 'snapchat_tag', 'twitter_tag', 'pinterest_tag', 'google_tag_manager', 'google_analytics'];
        if (!in_array($request->type, $analyticScriptsTypes)) {
            Toastr::error(translate('Update_failed'));
            return back();
        }
        $script = AnalyticScript::where('type',$request->type)->firstOrNew();
        $script->name =str_replace(' ', '_', ucwords(str_replace('_', ' ', $request['type'])));
        $script->type = $request->type;
        $script->script_id = $request->script_id;
        $script->save();
        Toastr::success(translate($request->type) . translate('messages.analytic_script_updated_successfully'));
        return back();
    }

    public function analyticStatus(Request $request){
        $script = AnalyticScript::where('type',$request->type)->first();
        if(!$script || !$script->script_id ){
            Toastr::error(translate('Please_ensure_you_have_filled_in_the_' . translate($request->type) . '_script_ID.'));
            return back();
        }
        $script->is_active = !$script->is_active;
        $script->save();
        Toastr::success(translate(ucwords(str_replace('_', ' ', $request->type))) .' '. translate('messages.Turned') . ' '.($script->is_active ? translate('on') : translate('off')) .' '. translate('successfully'));
        return back();
    }

    private function dataArray(){
         $analyticsTools = [
            [
                'key' => 'google_analytics',
                'title' => 'Google_Analytics',
                'placeholder' => 'Enter_the_GA_Measurement_ID',
                'modal' => 'modalForGoogleAnalytics',
                'icon' => 'google.svg',
            ],
            [
                'key' => 'google_tag_manager',
                'title' => 'Google_Tag_Manager',
                'placeholder' => 'enter_the_GTM_Container_ID',
                'modal' => 'modalForGoogleTagManager',
                'icon' => 'google.svg',
            ],
            [
                'key' => 'linkedin_insight',
                'title' => 'LinkedIn_Insight_Tag',
                'placeholder' => 'Enter_Linkedin_insight_tag_id',
                'modal' => 'modalForLinkedInInsight',
                'icon' => 'linkedin.svg',
            ],
            [
                'key' => 'meta_pixel',
                'title' => 'Meta_Pixel',
                'placeholder' => 'Enter_the_Meta_Pixel_ID',
                'modal' => 'modalForFacebookMeta',
                'icon' => 'facebook.svg',
            ],
            [
                'key' => 'pinterest_tag',
                'title' => 'Pinterest_Pixel',
                'placeholder' => 'Enter_the_Pinterest_Tag_ID',
                'modal' => 'modalForPinterestPixel',
                'icon' => 'pinterest.svg',
            ],
            [
                'key' => 'snapchat_tag',
                'title' => 'Snapchat_Pixel',
                'placeholder' => 'Enter_the_Snap_Pixel_ID',
                'modal' => 'modalForSnapchatPixel',
                'icon' => 'snapchat.svg',
            ],
            [
                'key' => 'tiktok_tag',
                'title' => 'TikTok_Pixel',
                'placeholder' => 'Enter_the_TikTok_Pixel_ID',
                'modal' => 'modalForTikTokPixel',
                'icon' => 'tiktok.svg',
            ],
            [
                'key' => 'twitter_tag',
                'title' => 'X (Twitter) Pixel',
                'placeholder' => 'Enter_the_Pixel_ID',
                'modal' => 'modalForTwitterPixel',
                'icon' => 'twitter.svg',
            ],
        ];
        return $analyticsTools;
    }

}

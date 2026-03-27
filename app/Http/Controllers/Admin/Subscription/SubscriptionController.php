<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Models\Zone;
use App\Models\Store;
use App\Models\StoreWallet;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Carbon;
use App\Models\BusinessSetting;
use App\Mail\SubscriptionCancel;
use App\Models\StoreSubscription;
use Illuminate\Support\Facades\DB;
use App\Models\SubscriptionPackage;
use App\Http\Controllers\Controller;
use App\Mail\SubscriptionPlanUpdate;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SubscriptionTransaction;
use App\Exports\SubscritionPackageListExport;
use App\Exports\SubscriptionTransactionsExport;
use App\Exports\SubscriptionSubscriberListExport;
use App\Models\SubscriptionBillingAndRefundHistory;
use Modules\Rental\Emails\ProviderSubscriptionCancel;
use Modules\Rental\Emails\ProviderSubscriptionPlanUpdate;
use App\Contracts\Repositories\TranslationRepositoryInterface;

class SubscriptionController extends Controller
{

    public function __construct(
        protected TranslationRepositoryInterface $translationRepo
    )
    {
    }
    public function index(Request $request)
    {
        $key = explode(' ', $request['search']);
        $filter = $request['statistics'];

        $packages=  SubscriptionPackage::withcount('currentSubscribers')
            ->when($request?->module == 1, function($query){
                $query->where('module_type', 'rental');
            })
            ->when($request?->module != 1, function($query){
                $query->where('module_type', 'all');
            })
            ->when(isset($key), function($q) use($key){
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('package_name', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->paginate(config('default_pagination'));

        $package_sell_count= SubscriptionPackage::
        when($request?->module != 1, function($query){
            $query->where('module_type', 'all');
        } )
            ->when($request?->module == 1, function($query){
                $query->where('module_type', 'rental');
            } )-> withSum([
                'transactions' => function ($query) use ($filter) {
                    $query->where('is_trial',0)
                        ->when(isset($filter) && $filter == 'this_year', function ($query) {
                            return $query->whereYear('created_at', now()->format('Y'));
                        })

                        ->when(isset($filter) && $filter == 'this_month', function ($query) {
                            return $query->whereMonth('created_at', now()->format('m'))->whereYear('created_at', now()->format('Y'));
                        })

                        ->when(isset($filter) && $filter == 'this_week', function ($query) {
                            return $query->whereBetween('created_at', [now()->startOfWeek()->format('Y-m-d H:i:s'), now()->endOfWeek()->format('Y-m-d H:i:s')]);
                        });
                },
            ], 'paid_amount')->get();

        return view('admin-views.subscription.package.index',compact('packages','package_sell_count'));
    }
    public function create(Request $request)
    {
        $language = getWebConfig('language');
        $module= $request->module ?? 'all';
        return view('admin-views.subscription.package.create', compact('language','module'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'package_name' => 'max:191|unique:subscription_packages',
            'package_name.0' => 'required',

            'package_price' => 'required|numeric|between:0,999999999999.999',
            'package_validity' => 'required|integer|between:0,36160',
            'max_order' => 'nullable|integer|between:0,999999999',
            'max_product' => 'nullable|integer|between:0,999999999',
            'pos_system' => 'nullable|boolean',
            'mobile_app' => 'nullable|boolean',
            'self_delivery' => 'nullable|boolean',
            'chat' => 'nullable|boolean',
            'review' => 'nullable|boolean',
            'text' => 'nullable|max:1000',
        ], [
            'price.required' => translate('Must enter Price for the Package'),
            'package_validity.required' => translate('Must enter a validity period for the Package in days'),
            'package_validity.between' => translate('validity must be in 99 years'),
            'package_name.0.required'=>translate('default_package_name_is_required'),
        ]);

        $package = new SubscriptionPackage;
        $package->package_name = $request->package_name[array_search('default', $request->lang)];
        $package->text = $request->text[array_search('default', $request->lang)];
        $package->price = $request->package_price;
        $package->validity = $request->package_validity;
        $package->max_order = $request?->minimum_order_limit == 'on' ?   'unlimited' : $request->max_order;
        $package->max_product =   $request?->maximum_item_limit == 'on' ?   'unlimited' : $request->max_product;
        $package->pos = $request->pos_system ?? 0;
        $package->mobile_app = $request->mobile_app ?? 0;
        $package->self_delivery = $request->self_delivery ?? 0;
        $package->chat = $request->chat ?? 0;
        $package->review = $request->review ?? 0;
        $package->colour = $request?->colour;
        $package->module_type = $request?->module ?? 'all';
        $package->save();

        $this->translationRepo->addByModel(request: $request, model: $package, modelPath: 'App\Models\SubscriptionPackage', attribute: 'package_name');
        $this->translationRepo->addByModel(request: $request, model: $package, modelPath: 'App\Models\SubscriptionPackage', attribute: 'text');
        Toastr::success(translate('messages.Package_successfully_Added'));
        return redirect()->route('admin.business-settings.subscriptionackage.index',[ 'module' => $package->module_type== 'rental' ? 1 : 'all' ]);
    }

    public function statusChange(SubscriptionPackage $subscriptionackage){

        $subscriptionackage->status =!$subscriptionackage->status;
        $subscriptionackage->save();
        Toastr::success($subscriptionackage->status == 1 ? translate('messages.Package_Acitvated_successfully') : translate('Package_Deacitvated_successfully'));
        return back();
    }

    public function show(SubscriptionPackage $subscriptionackage)
    {
        $packages= SubscriptionPackage::where('status',1)->where('module_type', $subscriptionackage->module_type == 'rental' && addon_published_status('Rental') ? 'rental' : 'all' )->get();
        $over_view_data= $this->packageOverview($subscriptionackage);
        return view('admin-views.subscription.package.package-details', compact('subscriptionackage','over_view_data','packages'));
    }
    public function edit(SubscriptionPackage $subscriptionackage)
    {
        $subscriptionackage->load('translations')->withoutGlobalScope('translate');
        $language = getWebConfig('language');
        $defaultLang = str_replace('_', '-', app()->getLocale());
        return view('admin-views.subscription.package.edit', compact('language','defaultLang','subscriptionackage'));
    }

    public function update(SubscriptionPackage $subscriptionackage, Request $request)
    {

        $request->validate([
            'package_name' => 'max:191|unique:subscription_packages,package_name,'.$subscriptionackage->id,
            'package_name.0' => 'required',

            'package_price' => 'required|numeric|between:0,999999999999.999',
            'package_validity' => 'required|integer|between:0,36160',
            'max_order' => 'nullable|integer|between:0,999999999',
            'max_product' => 'nullable|integer|between:0,999999999',
            'pos_system' => 'nullable|boolean',
            'mobile_app' => 'nullable|boolean',
            'self_delivery' => 'nullable|boolean',
            'chat' => 'nullable|boolean',
            'review' => 'nullable|boolean',
            'text' => 'nullable|max:1000',
        ], [
            'price.required' => translate('Must enter Price for the Package'),
            'package_validity.required' => translate('Must enter a validity period for the Package in days'),
            'package_validity.between' => translate('validity must be in 99 years'),
            'package_name.0.required'=>translate('default_package_name_is_required'),
        ]);
        $subscriptionackage->package_name = $request->package_name[array_search('default', $request->lang)];
        $subscriptionackage->text = $request->text[array_search('default', $request->lang)];
        $subscriptionackage->price = $request->package_price;
        $subscriptionackage->validity = $request->package_validity;
        $subscriptionackage->max_order = $request?->minimum_order_limit == 'on' ?   'unlimited' : $request->max_order;
        $subscriptionackage->max_product =   $request?->maximum_item_limit == 'on' ?   'unlimited' : $request->max_product;
        $subscriptionackage->pos = $request->pos_system ?? 0;
        $subscriptionackage->mobile_app = $request->mobile_app ?? 0;
        $subscriptionackage->self_delivery = $request->self_delivery ?? 0;
        $subscriptionackage->chat = $request->chat ?? 0;
        $subscriptionackage->review = $request->review ?? 0;
        $subscriptionackage->colour = $request?->colour;
        $subscriptionackage->save();
        $this->translationRepo->updateByModel(request: $request, model: $subscriptionackage, modelPath: 'App\Models\SubscriptionPackage', attribute: 'package_name');
        $this->translationRepo->updateByModel(request: $request, model: $subscriptionackage, modelPath: 'App\Models\SubscriptionPackage', attribute: 'text');
        Toastr::success(translate('messages.Package_Updated_successfully'));


        try {

            $subscribers= StoreSubscription::with('store.vendor')->has('store')->where(['package_id' =>  $subscriptionackage->id,'status'=> 1])->get();
            foreach ($subscribers as $subscriber){


                if($subscriber?->store?->module->module_type == 'rental' && addon_published_status('Rental')){


                    if( Helpers::getRentalNotificationStatusData('provider','provider_subscription_plan_update','push_notification_status',$subscriber?->store?->id)  &&  $subscriber?->store?->vendor?->firebase_token){
                        $data = [
                            'title' => translate('subscription_plan_updated'),
                            'description' => translate('Your_subscription_plan_has_been_updated'),
                            'order_id' => '',
                            'image' => '',
                            'type' => 'subscription',
                            'order_status' => '',
                        ];
                        Helpers::send_push_notif_to_device($subscriber?->store?->vendor?->firebase_token, $data);
                        DB::table('user_notifications')->insert([
                            'data' => json_encode($data),
                            'vendor_id' => $subscriber?->store?->vendor_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                        if(config('mail.status') && Helpers::get_mail_status('rental_subscription_plan_upadte_mail_status_provider') == '1' &&  Helpers::getRentalNotificationStatusData('provider','provider_subscription_plan_update','mail_status' ,$subscriber?->store?->id)){
                            Mail::to($subscriber?->store?->getRawOriginal('email'))->send(new ProviderSubscriptionPlanUpdate($subscriber?->store?->name));
                        }

                } else{

                    if( Helpers::getNotificationStatusData('store','store_subscription_plan_update','push_notification_status',$subscriber?->store?->id)  &&  $subscriber?->store?->vendor?->firebase_token){
                        $data = [
                            'title' => translate('subscription_plan_updated'),
                            'description' => translate('Your_subscription_plan_has_been_updated'),
                            'order_id' => '',
                            'image' => '',
                            'type' => 'subscription',
                            'order_status' => '',
                        ];
                        Helpers::send_push_notif_to_device($subscriber?->store?->vendor?->firebase_token, $data);
                        DB::table('user_notifications')->insert([
                            'data' => json_encode($data),
                            'vendor_id' => $subscriber?->store?->vendor_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }

                        if(config('mail.status') && Helpers::get_mail_status('subscription_plan_upadte_mail_status_store') == '1' &&  Helpers::getNotificationStatusData('store','store_subscription_plan_update','mail_status' ,$subscriber?->store?->id)){
                            Mail::to($subscriber?->store?->getRawOriginal('email'))->send(new SubscriptionPlanUpdate($subscriber?->store?->name));
                        }
                    }
                }

        } catch (\Exception $ex) {
            info($ex->getMessage());
        }


        return redirect()->route('admin.business-settings.subscriptionackage.show',$subscriptionackage->id);
    }
    public function overView(SubscriptionPackage $subscriptionackage, Request $request)
    {
        $over_view_data= $this->packageOverview($subscriptionackage,$request?->type);
            return response()->json([
            'view'=>view('admin-views.subscription.package.partial._over-view-data',compact('over_view_data'))->render(),

            ]);
    }

    private function packageOverview($subscriptionackage,$type ='all'){
        $data=[];
        $subscription_deadline_warning_days = (int) BusinessSetting::where('key','subscription_deadline_warning_days')->first()?->value ?? 7;

        $totalSubscribersData = $subscriptionackage->subscribers()
        ->when($type == 'this_month' ,function($query){
            $query->whereMonth('renewed_at', Carbon::now()->month );
        })
        ->when($type == 'this_year' ,function($query){
            $query->whereYear('renewed_at', Carbon::now()->year );
        })
        ->when($type == 'this_week' ,function($query){
            $query->whereBetween('renewed_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        })
        ->selectRaw('COUNT(DISTINCT store_id) AS total_subscribers,
                    COUNT(DISTINCT CASE WHEN status = 1 THEN store_id END) AS active_subscriptions,
                    COUNT(DISTINCT CASE WHEN status = 0 THEN store_id END) AS expired_subscriptions,
                    COUNT(DISTINCT CASE WHEN status = 1 AND expiry_date <= ? THEN store_id END) AS expired_soon',
                    [Carbon::today()->addDays($subscription_deadline_warning_days)])
        ->first();

        $data['total_subscribed_user']= $totalSubscribersData['total_subscribers'];
        $data['active_subscription']= $totalSubscribersData['active_subscriptions'];
        $data['expired_subscription']= $totalSubscribersData['expired_subscriptions'];
        $data['expired_soon']= $totalSubscribersData['expired_soon'];

        $totals = $subscriptionackage->transactions()
        ->when($type == 'this_month' ,function($query){
            $query->whereMonth('created_at', Carbon::now()->month );
        })
        ->when($type == 'this_year' ,function($query){
            $query->whereYear('created_at', Carbon::now()->year );
        })
        ->when($type == 'this_week' ,function($query){
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        })
        ->selectRaw('COUNT(DISTINCT CASE WHEN is_trial = 1 THEN store_id END) AS total_free_trials,
                    COUNT(DISTINCT CASE WHEN is_trial = 0 THEN store_id END) AS total_renewed,
                    SUM(CASE WHEN is_trial = 0 THEN paid_amount ELSE 0 END) AS total_amount')
        ->first();

        $data['total_free_trials']= $totals['total_free_trials'];
        $data['total_renewed']= $totals['total_renewed'];
        $data['total_amount']= $totals['total_amount'];

        return $data;

    }


    public function transaction($id, Request $request){

        $filter= $request['filter'];
        $plan_type= $request['plan_type'];
        $from =$request['start_date'] ?? Carbon::now()->format('Y-m-d');
        $to =$request['end_date'] ?? Carbon::now()->format('Y-m-d');

        $key = explode(' ', $request['search']);
        $transactions= SubscriptionTransaction::where('package_id',$id)
        ->when(isset($key), function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('id', 'like', "%{$value}%");
                }
                $q->orWhereHas('store' , function ($q) use ($key) {
                    foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
                });
            });
        })
        ->when($filter == 'this_year' , function($query){
            $query->whereYear('created_at', Carbon::now()->year );
        })
        ->when($filter == 'this_month' , function($query){
            $query->whereMonth('created_at', Carbon::now()->month );
        })
        ->when($filter == 'this_week' , function($query){
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        })
        ->when($filter == 'custom' , function($query) use($from,$to) {
            $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
        })

        ->when( in_array( $plan_type,['renew','new_plan','first_purchased','free_trial'])  , function($query) use($plan_type){
            $query->where('plan_type', $plan_type );
        })

        ->latest()->paginate(config('default_pagination'));
            $subscription_deadline_warning_days = BusinessSetting::where('key','subscription_deadline_warning_days')->first()?->value ?? 7;
        return view('admin-views.subscription.package.transaction', compact('transactions','id','filter','subscription_deadline_warning_days'));

    }
    public function settings(){

        $key=['subscription_deadline_warning_days','subscription_deadline_warning_message','subscription_free_trial_days','subscription_free_trial_type','subscription_free_trial_status','subscription_usage_max_time'];
        $settings=BusinessSetting::whereIn('key', $key)->pluck('value','key');
        return view('admin-views.subscription.settings.setting', compact('settings'));

    }
    public function trialStatus(){
        $status = BusinessSetting::firstOrNew([
            'key' => 'subscription_free_trial_status'
        ]);
        $status->value =  $status->value != 1 ?  1 : 0;
        $status->save();
        Toastr::success($status->value == 1 ? translate('messages.Free_Trial_Activated_Successfully') : translate('messages.Free_Trial_Disabled_Successfully'));
        return back();
    }
    public function settingUpdate(Request $request){

        $key=['subscription_deadline_warning_days','subscription_deadline_warning_message','subscription_free_trial_days','subscription_free_trial_type','subscription_free_trial_status','subscription_usage_max_time'];
            foreach ($request->all() as $k => $value) {

                if(in_array($k, $key) ){
                    $status = BusinessSetting::firstOrNew([
                        'key' => $k
                    ]);
                    if( $k == 'subscription_free_trial_days'){
                        if($request->subscription_free_trial_type == 'year'){
                            $value = $value * 365;
                        } else if($request->subscription_free_trial_type == 'month'){
                            $value = $value * 30;
                        } else{
                            $value = $value;
                        }
                    }

                    $status->value =  $value;
                    $status->save();
                }
            }

        Toastr::success( translate('messages.Settings_Saved_Successfully'));
        return back();
    }
    public function invoice($id){
        $BusinessData= ['admin_commission' ,'business_name','address','phone','logo','email_address'];
        $transaction= SubscriptionTransaction::with(['store.vendor','package:id,package_name,price'])->find($id);
        $BusinessData=BusinessSetting::whereIn('key', $BusinessData)->pluck('value' ,'key') ;
        $logo=BusinessSetting::where('key', "logo")->first() ;

        $mpdf_view = View::make('subscription-invoice', compact('transaction','BusinessData','logo'));
        Helpers::gen_mpdf(view: $mpdf_view,file_prefix: 'Subscription',file_postfix: $id);
        return back();
    }


    public function subscriberList(Request $request){
        $key = explode(' ', $request['search']);
        $subscribers= Store::has('store_sub_update_application')->whereHas('vendor',function($query){
            $query->where('status', 1);
        })
        ->whereIn('store_business_model' ,['subscription','unsubscribed'])->with([
            'store_sub_update_application.package'
        ])->withCount('store_all_sub_trans')

        ->when(isset($key), function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
                $q->orWhereHas('store_sub_update_application.package' , function ($q) use ($key) {
                    foreach ($key as $value) {
                    $q->where('package_name', 'like', "%{$value}%");
                }
                });
            });
        })
        ->when(isset($request->zone_id) && is_numeric($request->zone_id), function ($query) use ($request) {
            return $query->where('zone_id', $request->zone_id);
        })


        ->when(isset($request->subscription_type) && $request->subscription_type == 'active', function ($query)  {
            return $query->whereHas('store_sub_update_application', function ($q)  {
                return $q->where('status',1);
            });
        })
        ->when(isset($request->subscription_type) && $request->subscription_type == 'expired', function ($query)  {
            return $query->whereHas('store_sub_update_application', function ($q)  {
                return $q->where('status',0);
            });
        })
        ->when(isset($request->subscription_type) && $request->subscription_type == 'cancaled', function ($query)  {
            return $query->whereHas('store_sub_update_application', function ($q)  {
                return $q->where('is_canceled',1);
            });
        })
        ->when(isset($request->subscription_type) && $request->subscription_type == 'free_trial', function ($query)  {
            return $query->whereHas('store_sub_update_application', function ($q)  {
                return $q->where('is_trial',1);
            });
        })
        ->latest()->paginate(config('default_pagination'));

        $data=[];
        $subscription_deadline_warning_days = (int) BusinessSetting::where('key','subscription_deadline_warning_days')->first()?->value ?? 7;

        $totalSubscribersData= StoreSubscription::whereHas('store',function ($query)use($request){
            $query->whereIn('store_business_model' ,['subscription','unsubscribed'])
            ->when(isset($request->zone_id) && is_numeric($request->zone_id), function ($query) use ($request) {
                return $query->where('zone_id', $request->zone_id);

            });
        })
        ->whereHas('store.vendor',function($query){
            $query->where('status', 1);
        })
        ->selectRaw('COUNT(DISTINCT store_id) AS total_subscribers,
        COUNT(DISTINCT CASE WHEN status = 1 THEN store_id END) AS active_subscriptions,
        COUNT(DISTINCT CASE WHEN status = 1 AND expiry_date <= ? THEN store_id END) AS expired_soon',
        [Carbon::today()->addDays($subscription_deadline_warning_days)])
        ->first();
        // COUNT(DISTINCT CASE WHEN status != 0 THEN store_id END) AS expired_subscriptions,

        $data['total_subscribed_user']= $totalSubscribersData['total_subscribers'];
            $data['active_subscription']= $totalSubscribersData['active_subscriptions'];
            $data['expired_soon']= $totalSubscribersData['expired_soon'];


            $total_inactive_subscription = Store::has('store_sub_update_application')
            ->whereIn('store_business_model' ,['unsubscribed'])
            ->when(is_numeric($request->zone_id), function ($query) use ($request) {
                return $query->where('zone_id', $request->zone_id);
                })
                ->whereDoesntHave('store_sub_update_application',function($query){
                    return $query->where('status', '1');
                    })->count();

        $data['expired_subscription']= $total_inactive_subscription;



            $totals= SubscriptionTransaction::whereHas('store.vendor',function($query){
                $query->where('status', 1);
            })->where('is_trial',0)
            ->when(isset($request->zone_id) && is_numeric($request->zone_id), function ($query) use ($request) {
                return $query->whereHas('store', function ($q) use ($request) {
                    return $q->where('zone_id', $request->zone_id);
                });
            })
            ->selectRaw('  COUNT(*) as total_transactions,
                SUM(paid_amount) as total_paid_amount,
                SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN paid_amount ELSE 0 END) as current_month_paid_amount ', [Carbon::now()->month, Carbon::now()->year])
            ->first();

            $data['total_transactions']= $totals['total_transactions'];
            $data['total_paid_amount']= $totals['total_paid_amount'];
            $data['current_month_paid_amount']= $totals['current_month_paid_amount'];

        return view('admin-views.subscription.subscriber.list',compact('subscribers','data'));

    }
    public function subscriberDetail($id){
        $store= Store::where('id',$id)->with([
            'store_sub_update_application.package','vendor','store_sub_update_application.last_transcations','module:id,module_type'
        ])->withcount('items')
        ->first();
        if($store->module_type == 'rental') {
            $store->loadCount('vehicles as items_count' );
        }

        $packages = SubscriptionPackage::where('status',1)->where('module_type', $store?->module?->module_type == 'rental' && addon_published_status('Rental') ? 'rental' : 'all' )->latest()->get();
        $admin_commission=BusinessSetting::where('key', 'admin_commission')->first()?->value ;
        $business_name=BusinessSetting::where('key', 'business_name')->first()?->value ;
        try {
            $index=  $store->store_business_model == 'commission' ? 0 : 1+ array_search($store?->store_sub_update_application?->package_id??1 ,array_column($packages->toArray() ,'id') );
        } catch (\Throwable $th) {
            $index= 2;
        }

        return view('admin-views.subscription.subscriber.vendor-subscription',compact('store','packages','business_name','admin_commission','index'));
    }
    public function cancelSubscription(Request $request, $id){

        StoreSubscription::where(['store_id' => $id, 'id'=>$request->subscription_id])->update([
            'is_canceled' => 1,
            'canceled_by' => 'admin',
        ]);

        try {
            $store=Store::where('id',$id)->first();

        if($store?->module?->module_type == 'rental' && addon_published_status('Rental')){
                if( Helpers::getRentalNotificationStatusData('provider','provider_subscription_cancel','push_notification_status',$store->id)  &&  $store?->vendor?->firebase_token){
                    $data = [
                        'title' => translate('subscription_canceled'),
                        'description' => translate('Your_subscription_has_been_canceled'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'subscription',
                        'order_status' => '',
                    ];
                    Helpers::send_push_notif_to_device($store?->vendor?->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $store?->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                if (config('mail.status') && Helpers::get_mail_status('rental_subscription_cancel_mail_status_provider') == '1' &&  Helpers::getRentalNotificationStatusData('provider','provider_subscription_cancel','mail_status' ,$store?->id)) {
                    Mail::to($store?->getRawOriginal('email'))->send(new ProviderSubscriptionCancel($store->name));
                }
            } else{
                if( Helpers::getNotificationStatusData('store','store_subscription_cancel','push_notification_status',$store->id)  &&  $store?->vendor?->firebase_token){
                    $data = [
                        'title' => translate('subscription_canceled'),
                        'description' => translate('Your_subscription_has_been_canceled'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'subscription',
                        'order_status' => '',
                    ];
                    Helpers::send_push_notif_to_device($store?->vendor?->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $store?->vendor_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                if (config('mail.status') && Helpers::get_mail_status('subscription_cancel_mail_status_store') == '1' &&  Helpers::getNotificationStatusData('store','store_subscription_cancel','mail_status' ,$store?->id)) {
                    Mail::to($store?->getRawOriginal('email'))->send(new SubscriptionCancel($store->name));
                }
            }
        } catch (\Exception $ex) {
            info($ex->getMessage());
        }
        return response()->json(200);

    }
    public function switchToCommission($id){

        $store=  Store::where('id',$id)->with('store_sub')->first();

        $store_subscription=  $store->store_sub;
        if($store->store_business_model == 'subscription'  && $store_subscription?->is_canceled === 0 && $store_subscription?->is_trial === 0){
            Helpers::calculateSubscriptionRefundAmount(store:$store);
        }

        $store->store_business_model = 'commission';
        $store->save();

        StoreSubscription::where(['store_id' => $id])->update([
            'status' => 0,
        ]);
        return response()->json(200);

    }
    public function packageView($id,$store_id){
        $store_subscription= StoreSubscription::where('store_id', $store_id)->with(['package'])->latest()->first();
//        dd($store_subscription);
        $package = SubscriptionPackage::where('status',1)->where('id',$id)->first();
        $store= Store::Where('id',$store_id)->first();
        $pending_bill= SubscriptionBillingAndRefundHistory::where(['store_id'=>$store->id,
                            'transaction_type'=>'pending_bill', 'is_success' =>0])->sum('amount') ;

        $balance = BusinessSetting::where('key', 'wallet_status')->first()?->value == 1 ? StoreWallet::where('vendor_id',$store->vendor_id)->first()?->balance ?? 0 : 0;
        $payment_methods = Helpers::getActivePaymentGateways();
        $disable_item_count=null;
        if(data_get(Helpers::subscriptionConditionsCheck(store_id:$store->id,package_id:$package->id) , 'disable_item_count') > 0 && ( !$store_subscription || $package->id != $store_subscription->package_id)){
            $disable_item_count=data_get(Helpers::subscriptionConditionsCheck(store_id:$store->id,package_id:$package->id) , 'disable_item_count');
        }
        $store_business_model=$store->store_business_model;
        $admin_commission=BusinessSetting::where('key', "admin_commission")->first()?->value ?? 0 ;
        $cash_backs=[];
        if($store->store_business_model == 'subscription' &&  $store_subscription->status == 1 && $store_subscription->is_canceled == 0 && $store_subscription->is_trial == 0  && $store_subscription->package_id !=  $package->id){
            $cash_backs= Helpers::calculateSubscriptionRefundAmount(store:$store, return_data:true);
        }

        return response()->json([
            'disable_item_count'=> $disable_item_count,
            'view' => view('admin-views.subscription.subscriber.partials._package_selected', compact('store_subscription','package','store_id','balance','payment_methods','pending_bill','store_business_model','admin_commission','cash_backs'))->render()
        ]);

    }
    public function packageBuy(Request $request){

        $request->validate([
            'package_id' => 'required',
            'store_id' => 'required',
            'payment_gateway' => 'required'
        ]);
        $store= Store::Where('id',$request->store_id)->first(['id','vendor_id']);
        $package = SubscriptionPackage::withoutGlobalScope('translate')->find($request->package_id);


        $pending_bill= SubscriptionBillingAndRefundHistory::where(['store_id'=>$store->id,
                            'transaction_type'=>'pending_bill', 'is_success' =>0])?->sum('amount')?? 0;

        if(!in_array($request->payment_gateway,['wallet','manual_payment_by_admin'])){
            $url= route('admin.business-settings.subscriptionackage.subscriberDetail',$store->id);
            return redirect()->away(Helpers::subscriptionPayment(store_id:$store->id,package_id:$package->id,payment_gateway:$request->payment_gateway,payment_platform:'web',url:$url,pending_bill:$pending_bill,type: $request?->type));
        }

        if($request->payment_gateway == 'wallet'){
        $wallet= StoreWallet::firstOrNew(['vendor_id'=> $store->vendor_id]);
        $balance = BusinessSetting::where('key', 'wallet_status')->first()?->value == 1 ? $wallet?->balance ?? 0 : 0;

            if($balance >= ($package?->price + $pending_bill)){
                $reference= 'wallet_payment_by_admin';
                $plan_data=   Helpers::subscription_plan_chosen(store_id:$store->id,package_id:$package->id,payment_method:$reference,discount:0,pending_bill:$pending_bill,reference:$reference,type: $request?->type);
                if($plan_data != false){
                    $wallet->total_withdrawn= $wallet?->total_withdrawn + $package->price + $pending_bill;
                    $wallet?->save();
                }

            }
            else{
                Toastr::error( translate('messages.Insufficient_balance_in_wallet'));
                return back();
            }
        } elseif($request->payment_gateway == 'manual_payment_by_admin'){
            $reference= 'manual_payment_by_admin';
            $plan_data=   Helpers::subscription_plan_chosen(store_id:$store->id,package_id:$package->id,payment_method:$reference,discount:0,pending_bill:$pending_bill,reference:$reference,type: $request?->type);
        }

        $plan_data != false ?  Toastr::success(  $request?->type == 'renew' ?  translate('Subscription_Package_Renewed_Successfully.'): translate('Subscription_Package_Shifted_Successfully.') ) : Toastr::error( translate('Something_went_wrong!.'));
        return back();

    }



    public function subscriberTransactions($id,Request $request){
        $filter= $request['filter'];
        $plan_type= $request['plan_type'];
        $from =$request['start_date'] ?? Carbon::now()->format('Y-m-d');
        $to =$request['end_date'] ?? Carbon::now()->format('Y-m-d');
        $store= Store::where('id',$id)->with([
            'store_sub_update_application.package'
        ])
        ->first();

        $key = explode(' ', $request['search']);
        $transactions= SubscriptionTransaction::where('store_id',$id)
        ->when(isset($key), function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('id', 'like', "%{$value}%");
                }
            });
        })
        ->when($filter == 'this_year' , function($query){
            $query->whereYear('created_at', Carbon::now()->year );
        })
        ->when($filter == 'this_month' , function($query){
            $query->whereMonth('created_at', Carbon::now()->month );
        })
        ->when($filter == 'this_week' , function($query){
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        })
        ->when($filter == 'custom' , function($query) use($from,$to) {
            $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
        })

        ->when( in_array( $plan_type,['renew','new_plan','first_purchased','free_trial'])  , function($query) use($plan_type){
            $query->where('plan_type', $plan_type );
        })

        ->latest()->paginate(config('default_pagination'));
            $subscription_deadline_warning_days = BusinessSetting::where('key','subscription_deadline_warning_days')->first()?->value ?? 7;
        return view('admin-views.subscription.subscriber.transaction',compact('store','transactions','id','filter','subscription_deadline_warning_days'));

    }

    public function switchPlan(Request $request){
        $request->validate([
            'package_id' => 'required',
        ]);

        SubscriptionPackage::where('id',$request->turn_off_package_id)->update([
            'status' => 0
        ]);

        $stores=  StoreSubscription::where('package_id',$request->turn_off_package_id)->where('status',1)->where('is_canceled',0)->where('is_trial',0)->get(['store_id']);

        if($request->package_id == 'commission'){
            StoreSubscription::where('package_id',$request->turn_off_package_id)->update([
                'status' => 0
            ]);
        }

        foreach($stores as $store){

            if($request->package_id == 'commission'){
                Store::where('id', $store->store_id)->update([
                    'store_business_model'=>'commission',
                    'item_section'=> 1
                ]);
            } else{
                $pending_bill=0;
                $pending_bill= SubscriptionBillingAndRefundHistory::where(['store_id'=>$store->store_id,
                'transaction_type'=>'pending_bill', 'is_success' =>0])?->sum('amount')?? 0;
                    $reference= 'plan_shift_by_admin';
                    Helpers::subscription_plan_chosen(store_id:$store->store_id,package_id:$request->package_id,payment_method:$reference,discount:0,pending_bill:$pending_bill,reference:$reference);
            }

        }
        Toastr::success( translate('messages.Plan_Switch_Successful'));
        return back();
    }



    public function packageExport(Request $request){

        $key = explode(' ', $request['search']);

        $packages=  SubscriptionPackage::withcount('currentSubscribers')
        ->when($request?->module == 1, function($query){
            $query->where('module_type', 'rental');
        })
        ->when($request?->module != 1, function($query){
            $query->where('module_type', 'all');
        })
        ->when(isset($key), function($q) use($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('package_name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->get();

        $data = [
            'data'=>$packages,
            'search'=>$request['search'],
        ];
        if ($request->export_type == 'excel') {
            return Excel::download(new SubscritionPackageListExport($data), 'SubscritionPackageListExport.xlsx');
        }
        return Excel::download(new SubscritionPackageListExport($data), 'SubscritionPackageListExport.csv');
    }
    public function TransactionExport(Request $request){
        $request->validate([
            'id' => 'required',
        ]);


        $filter= $request['filter'];
        $id= $request['id'];
        $plan_type= $request['plan_type'];
        $from =$request['start_date'] ?? Carbon::now()->format('Y-m-d');
        $to =$request['end_date'] ?? Carbon::now()->format('Y-m-d');

        $key = explode(' ', $request['search']);
        $transactions= SubscriptionTransaction::where('package_id',$id)
        ->when(isset($key), function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('id', 'like', "%{$value}%");
                }
                $q->orWhereHas('store' , function ($q) use ($key) {
                    foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
                });
            });
        })
        ->when($filter == 'this_year' , function($query){
            $query->whereYear('created_at', Carbon::now()->year );
        })
        ->when($filter == 'this_month' , function($query){
            $query->whereMonth('created_at', Carbon::now()->month );
        })
        ->when($filter == 'this_week' , function($query){
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        })
        ->when($filter == 'custom' , function($query) use($from,$to) {
            $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
        })

        ->when( in_array( $plan_type,['renew','new_plan','first_purchased','free_trial'])  , function($query) use($plan_type){
            $query->where('plan_type', $plan_type );
        })

        ->latest()->get();

        $data = [
            'data'=>$transactions,
            'plan_type'=>$request['plan_type'] ?? 'all',
            'filter'=>$request['filter'] ?? 'all',
            'search'=>$request['search'],
            'start_date'=>$request['start_date'],
            'end_date'=>$request['end_date'],
            'package_name'=>SubscriptionPackage::where('id',$id)->first()?->package_name,
        ];
        if ($request->export_type == 'excel') {
            return Excel::download(new SubscriptionTransactionsExport($data), 'SubscriptionTransactionsExport.xlsx');
        }
        return Excel::download(new SubscriptionTransactionsExport($data), 'SubscriptionTransactionsExport.csv');
    }
    public function subscriberListExport(Request $request){
        $key = explode(' ', $request['search']);

        $subscribers= Store::whereHas('vendor',function($query){
            $query->where('status', 1);
        })
        ->whereIn('store_business_model' ,['subscription','unsubscribed'])->with([
            'store_sub_update_application.package'
        ])->withCount('store_all_sub_trans')

        ->when(isset($key), function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
                $q->orWhereHas('store_sub_update_application.package' , function ($q) use ($key) {
                    foreach ($key as $value) {
                    $q->where('package_name', 'like', "%{$value}%");
                }
                });
            });
        })
        ->when(isset($request->zone_id) && is_numeric($request->zone_id), function ($query) use ($request) {
            return $query->where('zone_id', $request->zone_id);
        })


        ->when(isset($request->subscription_type) && $request->subscription_type == 'active', function ($query) use ($request) {
            return $query->whereHas('store_sub_update_application', function ($q) use ($request) {
                return $q->where('status',1);
            });
        })
        ->when(isset($request->subscription_type) && $request->subscription_type == 'expired', function ($query) use ($request) {
            return $query->whereHas('store_sub_update_application', function ($q) use ($request) {
                return $q->where('status',0);
            });
        })
        ->when(isset($request->subscription_type) && $request->subscription_type == 'cancaled', function ($query) use ($request) {
            return $query->whereHas('store_sub_update_application', function ($q) use ($request) {
                return $q->where('is_canceled',1);
            });
        })
        ->when(isset($request->subscription_type) && $request->subscription_type == 'free_trial', function ($query) use ($request) {
            return $query->whereHas('store_sub_update_application', function ($q) use ($request) {
                return $q->where('is_trial',1);
            });
        })
        ->latest()->get();

        $data = [
            'data'=>$subscribers,
            'zone'=>Zone::where('id' ,$request->zone_id)->first()?->name ?? 'all',
            'filter'=>$request->subscription_type ?? 'all',
            'search'=>$request['search'],

        ];
        if ($request->export_type == 'excel') {
            return Excel::download(new SubscriptionSubscriberListExport($data), 'SubscriptionSubscriberListExport.xlsx');
        }
        return Excel::download(new SubscriptionSubscriberListExport($data), 'SubscriptionSubscriberListExport.csv');
    }

    public function subscriberTransactionExport(Request $request){
        $request->validate([
            'id' => 'required',
        ]);
        $id= $request['id'];

        $filter= $request['filter'];
        $plan_type= $request['plan_type'];
        $from =$request['start_date'] ?? Carbon::now()->format('Y-m-d');
        $to =$request['end_date'] ?? Carbon::now()->format('Y-m-d');
        $store= Store::where('id',$id)->first();

        $key = explode(' ', $request['search']);
        $transactions= SubscriptionTransaction::where('store_id',$store->id)
        ->when(isset($key), function($query) use($key){
            $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('id', 'like', "%{$value}%");
                }
            });
        })
        ->when($filter == 'this_year' , function($query){
            $query->whereYear('created_at', Carbon::now()->year );
        })
        ->when($filter == 'this_month' , function($query){
            $query->whereMonth('created_at', Carbon::now()->month );
        })
        ->when($filter == 'this_week' , function($query){
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()] );
        })
        ->when($filter == 'custom' , function($query) use($from,$to) {
            $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
        })

        ->when( in_array( $plan_type,['renew','new_plan','first_purchased','free_trial'])  , function($query) use($plan_type){
            $query->where('plan_type', $plan_type );
        })

        ->latest()->get();

        $data = [
            'data'=>$transactions,
            'plan_type'=>$request['plan_type'] ?? 'all',
            'filter'=>$request['filter'] ?? 'all',
            'search'=>$request['search'],
            'start_date'=>$request['start_date'],
            'end_date'=>$request['end_date'],
            'store'=>$store->name,
        ];
        if ($request->export_type == 'excel') {
            return Excel::download(new SubscriptionTransactionsExport($data), 'SubscriptionTransactionsExport.xlsx');
        }
        return Excel::download(new SubscriptionTransactionsExport($data), 'SubscriptionTransactionsExport.csv');
    }

    public function subscriberWalletTransactions($id,Request $request){
        $store= Store::where('id',$id)->first();
        $transactions= SubscriptionBillingAndRefundHistory::where('store_id', $id)->with('package')
        ->where('transaction_type','refund')
        ->latest()->paginate(config('default_pagination'));

        return view('admin-views.subscription.subscriber.wallet-transaction',compact('transactions','store'));

    }

}

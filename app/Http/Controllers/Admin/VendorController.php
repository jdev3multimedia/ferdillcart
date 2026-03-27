<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\CentralLogics\StoreLogic;
use App\Exports\DisbursementHistoryExport;
use App\Exports\StoreCashTransactionExport;
use App\Exports\StoreListExport;
use App\Exports\StoreOrderTransactionExport;
use App\Exports\StoreWiseItemReviewExport;
use App\Exports\StoreWiseWithdrawTransactionExport;
use App\Exports\StoreWithdrawTransactionExport;
use App\Http\Controllers\Controller;
use App\Mail\WithdrawRequestMail;
use App\Models\AccountTransaction;
use App\Models\AddOn;
use App\Models\BusinessSetting;
use App\Models\Conversation;
use App\Models\DataSetting;
use App\Models\DisbursementDetails;
use App\Models\Item;
use App\Models\Message;
use App\Models\Module;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Store;
use App\Models\StoreConfig;
use App\Models\StoreSchedule;
use App\Models\StoreWallet;
use App\Models\SubscriptionPackage;
use App\Models\TempProduct;
use App\Models\UserInfo;
use App\Models\Vendor;
use App\Models\WithdrawRequest;
use App\Models\Zone;
use App\Scopes\StoreScope;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Facades\Excel;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Modules\Rental\Emails\ProviderWithdrawRequestMail;
use Modules\Rental\Entities\TripTransaction;
use Rap2hpoutre\FastExcel\FastExcel;

class VendorController extends Controller
{
    public function index()
    {
        return view('admin-views.vendor.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'name.0' => 'required',
            'name.*' => 'max:191',
            'address.0' => 'required',
            'address.*' => 'max:1000',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'email' => 'required|unique:vendors',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:vendors',
            'minimum_delivery_time' => 'required',
            'maximum_delivery_time' => 'required',
            'delivery_time_type' => 'required',
            'password' => ['required', Password::min(8)->mixedCase()->letters()->numbers()->symbols(),
                function ($attribute, $value, $fail) {
                    if (strpos($value, ' ') !== false) {
                        $fail('The :attribute cannot contain white spaces.');
                    }
                }, ],
            'zone_id' => 'required',
            'logo' => 'required|image|max:2048|mimes:'.IMAGE_FORMAT_FOR_VALIDATION,
            'cover_photo' => 'nullable|image|max:2048|mimes:'.IMAGE_FORMAT_FOR_VALIDATION,

        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
            'name.0.required' => translate('default_name_is_required'),
            'address.0.required' => translate('default_address_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request->zone_id) {
            $zone = Zone::query()
                ->whereContains('coordinates', new Point($request->latitude, $request->longitude, POINT_SRID))
                ->where('id', $request->zone_id)
                ->first();
            if (! $zone) {
                $validator->getMessageBag()->add('latitude', translate('messages.Please_select_a_location_within_the_selected_zone.'));

                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
        }

        if ($request->delivery_time_type == 'min') {
            $minimum_delivery_time = (int) $request->input('minimum_delivery_time');
            if ($minimum_delivery_time < 10) {
                $validator->getMessageBag()->add('minimum_delivery_time', translate('messages.minimum_delivery_time_should_be_more_than_10_min'));

                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
        }

        $vendor = new Vendor;
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->password = bcrypt($request->password);
        $vendor->save();

        $store = new Store;
        $store->name = $request->name[array_search('default', $request->lang)];
        $store->phone = $request->phone;
        $store->email = $request->email;
        $store->logo = Helpers::upload('store/', 'png', $request->file('logo'));
        $store->cover_photo = Helpers::upload('store/cover/', 'png', $request->file('cover_photo'));
        $store->address = $request->address[array_search('default', $request->lang)];
        $store->latitude = $request->latitude;
        $store->longitude = $request->longitude;
        $store->vendor_id = $vendor->id;
        $store->zone_id = $request->zone_id;
        $store->tin = $request->tin;
        $store->tin_expire_date = $request->tin_expire_date;
        $extension = $request->has('tin_certificate_image') ? $request->file('tin_certificate_image')->getClientOriginalExtension() : 'png';
        $store->tin_certificate_image = Helpers::upload('store/', $extension, $request->file('tin_certificate_image'));
        $store->delivery_time = $request->minimum_delivery_time.'-'.$request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->module_id = Config::get('module.current_module_id');
        try {
            $store->save();
            // $store->module->increment('stores_count');
            if (config('module.'.$store->module->module_type)['always_open']) {
                StoreLogic::insert_schedule($store->id);
            }

            Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'Store', data_id: $store->id, data_value: $store->name);
            Helpers::add_or_update_translations(request: $request, key_data: 'address', name_field: 'address', model_name: 'Store', data_id: $store->id, data_value: $store->address);

        } catch (\Exception $ex) {
            info($ex->getMessage());
            $validator->getMessageBag()->add('store_add', $ex->getMessage());

            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        return response()->json(['message' => translate('messages.store_added_successfully'), 'redirect' => route('admin.store.list')]);
    }

    public function edit($id)
    {
        if (env('APP_MODE') == 'demo' && $id == 2) {
            Toastr::warning(translate('messages.you_can_not_edit_this_store_please_add_a_new_store_to_edit'));

            return back();
        }
        $store = Store::withoutGlobalScope('translate')->findOrFail($id);

        return view('admin-views.vendor.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required|max:100',
            'l_name' => 'nullable|max:100',
            'name.0' => 'required',
            'name.*' => 'max:191',
            'address.0' => 'required',
            'address.*' => 'max:1000',
            'email' => 'required|unique:vendors,email,'.$store->vendor->id,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20|unique:vendors,phone,'.$store->vendor->id,
            'zone_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'password' => ['nullable', Password::min(8)->mixedCase()->letters()->numbers()->symbols(), function ($attribute, $value, $fail) {
                if (strpos($value, ' ') !== false) {
                    $fail('The :attribute cannot contain white spaces.');
                }
            }, ],
            'minimum_delivery_time' => 'required',
            'maximum_delivery_time' => 'required',
            'delivery_time_type' => 'required',
            'logo' => 'nullable|image|max:2048|mimes:'.IMAGE_FORMAT_FOR_VALIDATION,
            'cover_photo' => 'nullable|image|max:2048|mimes:'.IMAGE_FORMAT_FOR_VALIDATION,
        ], [
            'f_name.required' => translate('messages.first_name_is_required'),
            'name.0.required' => translate('default_name_is_required'),
            'address.0.required' => translate('default_address_is_required'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);

        }

        if ($request->zone_id) {
            $zone = Zone::query()
                ->whereContains('coordinates', new Point($request->latitude, $request->longitude, POINT_SRID))
                ->where('id', $request->zone_id)
                ->first();
            if (! $zone) {
                $validator->getMessageBag()->add('latitude', translate('messages.coordinates_out_of_zone'));

                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
        }
        if ($request->delivery_time_type == 'min') {
            $minimum_delivery_time = (int) $request->input('minimum_delivery_time');
            if ($minimum_delivery_time < 10) {
                $validator->getMessageBag()->add('minimum_delivery_time', translate('messages.minimum_delivery_time_should_be_more_than_10_min'));

                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
        }

        $vendor = Vendor::findOrFail($store->vendor->id);
        $vendor->f_name = $request->f_name;
        $vendor->l_name = $request->l_name;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->password = strlen($request->password) > 1 ? bcrypt($request->password) : $store->vendor->password;
        $vendor->save();

        $slug = Str::slug($request->name[array_search('default', $request->lang)]);
        $store->slug = $store->slug ? $store->slug : "{$slug}{$store->id}";
        $store->email = $request->email;
        $store->phone = $request->phone;
        $store->logo = $request->has('logo') ? Helpers::update('store/', $store->logo, 'png', $request->file('logo')) : $store->logo;
        $store->cover_photo = $request->has('cover_photo') ? Helpers::update('store/cover/', $store->cover_photo, 'png', $request->file('cover_photo')) : $store->cover_photo;
        $store->name = $request->name[array_search('default', $request->lang)];
        $store->address = $request->address[array_search('default', $request->lang)];
        $store->latitude = $request->latitude;
        $store->longitude = $request->longitude;
        $store->zone_id = $request->zone_id;
        $store->tin = $request->tin;
        $store->tin_expire_date = $request->tin_expire_date;
        $extension = $request->has('tin_certificate_image') ? $request->file('tin_certificate_image')->getClientOriginalExtension() : 'png';
        $store->tin_certificate_image = $request->has('tin_certificate_image') ? Helpers::update('store/', $store->tin_certificate_image, $extension, $request->file('tin_certificate_image')) : $store->tin_certificate_image;
        $store->delivery_time = $request->minimum_delivery_time.'-'.$request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->save();

        Helpers::add_or_update_translations(request: $request, key_data: 'name', name_field: 'name', model_name: 'Store', data_id: $store->id, data_value: $store->name);
        Helpers::add_or_update_translations(request: $request, key_data: 'address', name_field: 'address', model_name: 'Store', data_id: $store->id, data_value: $store->address);

        if ($vendor->userinfo) {
            $userinfo = $vendor->userinfo;
            $userinfo->f_name = $store->name;
            $userinfo->l_name = '';
            $userinfo->email = $store->email;
            $userinfo->image = $store->logo;
            $userinfo->save();
        }

        if ($request->approve_vendor == 1) {
            $request->merge([
                'status' => 1,
                'id' => $store->id,
            ]);
            $this->updateVendorApplication($request);
        }

        return response()->json([
            'message' => translate('messages.store_updated_successfully'),
            'redirect' => route('admin.store.list'),
        ]);
    }

    public function destroy(Request $request, Store $store)
    {
        if (env('APP_MODE') == 'demo' && $store->id == 2) {
            Toastr::warning(translate('messages.you_can_not_delete_this_store_please_add_a_new_store_to_delete'));

            return back();
        }
        if (Order::where('store_id', $store->id)->whereIn('order_status', ['pending', 'accepted', 'confirmed', 'processing', 'handover', 'picked_up'])->exists()) {
            Toastr::warning(translate('messages.you_can_not_delete_this_store_Please_complete_the_ongoing_and_accepted_orders'));

            return back();
        }

        Helpers::check_and_delete('vendor/', $store->vendor['image']);

        Helpers::check_and_delete('store/', $store->logo);

        Helpers::check_and_delete('store/cover/', $store->cover_photo);

        foreach ($store->deliverymen as $dm) {

            Helpers::check_and_delete('delivery-man/', $dm['image']);

            foreach (json_decode($dm['identity_image'], true) as $img) {

                Helpers::check_and_delete('delivery-man/', $img);

            }
        }

        $store?->deliverymen()?->delete();
        $store?->discount()?->delete();
        $store?->schedules()?->delete();
        $store?->storeConfig()?->delete();
        $store?->translations()?->delete();
        $store?->vendor?->userinfo()?->delete();
        $store?->vendor()?->delete();
        $store?->delete();

        Toastr::success(translate('messages.store_removed'));

        return back();
    }

    public function view(Request $request, $store_id, $tab = null, $sub_tab = 'cash')
    {
        $filter = $request?->filter;
        $key = explode(' ', request()->search);
        $store = Store::findOrFail($store_id);

        if (addon_published_status('Rental') && $store->module_type == 'rental') {
            return to_route('admin.rental.provider.details', ['id' => $store_id, 'tab' => $tab]);
        }

        $wallet = $store->vendor->wallet;
        if (! $wallet) {
            $wallet = new StoreWallet;
            $wallet->vendor_id = $store->vendor->id;
            $wallet->total_earning = 0.0;
            $wallet->total_withdrawn = 0.0;
            $wallet->pending_withdraw = 0.0;
            $wallet->created_at = now();
            $wallet->updated_at = now();
            $wallet->save();
        }
        if ($tab == 'settings') {
            if ($store->module->module_type == 'ecommerce' && ! StoreSchedule::where('store_id', $store->id)->exists()) {
                StoreLogic::insert_schedule($store->id);
            }

            return view('admin-views.vendor.view.settings', compact('store'));
        } elseif ($tab == 'order') {
            $orders = Order::where('store_id', $store->id)->latest()
                ->when(isset($key), function ($q) use ($key) {
                    $q->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%");
                        }
                    });
                })
                ->when(isset($filter) && $filter == 'scheduled_orders', function ($q) {
                    $q->Scheduled();
                })
                ->when(isset($filter) && $filter == 'pending_orders', function ($q) {
                    $q->where(['order_status' => 'pending'])->OrderScheduledIn(30);
                })
                ->when(isset($filter) && $filter == 'delivered_orders', function ($q) {
                    $q->where(['order_status' => 'delivered']);
                })
                ->when(isset($filter) && $filter == 'canceled_orders', function ($q) {
                    $q->where(['order_status' => 'canceled']);
                })
                ->StoreOrder()
                ->Notpos()->paginate(10);

            return view('admin-views.vendor.view.order', compact('store', 'orders'));
        } elseif ($tab == 'item') {
            if ($sub_tab == 'pending-items' || $sub_tab == 'rejected-items') {

                $foods = TempProduct::withoutGlobalScope(\App\Scopes\StoreScope::class)->where('store_id', $store->id)
                    ->when(isset($key), function ($q) use ($key) {
                        $q->where(function ($q) use ($key) {
                            foreach ($key as $value) {
                                $q->where('name', 'like', "%{$value}%");
                            }
                        });
                    })
                    ->when($sub_tab == 'pending-items', function ($q) {
                        $q->where('is_rejected', 0);
                    })
                    ->when($sub_tab == 'rejected-items', function ($q) {
                        $q->where('is_rejected', 1);
                    })
                    ->latest()->paginate(25);
            } else {

                $foods = Item::withoutGlobalScope(\App\Scopes\StoreScope::class)->where('store_id', $store->id)
                    ->when(isset($key), function ($q) use ($key) {
                        $q->where(function ($q) use ($key) {
                            foreach ($key as $value) {
                                $q->where('name', 'like', "%{$value}%");
                            }
                        });
                    })
                    ->when($sub_tab == 'active-items', function ($q) {
                        $q->where('status', 1);
                    })
                    ->when($sub_tab == 'inactive-items', function ($q) {
                        $q->where('status', 0);
                    })
                    ->latest()->paginate(25);
            }
            $taxData = Helpers::getTaxSystemType(getTaxVatList: false);
            $productWiseTax = $taxData['productWiseTax'];

            return view('admin-views.vendor.view.product', compact('store', 'foods', 'sub_tab', 'productWiseTax'));
        } elseif ($tab == 'discount') {
            return view('admin-views.vendor.view.discount', compact('store'));
        } elseif ($tab == 'transaction') {
            return view('admin-views.vendor.view.transaction', compact('store', 'sub_tab'));
        } elseif ($tab == 'reviews') {
            return view('admin-views.vendor.view.review', compact('store', 'sub_tab'));

        } elseif ($tab == 'conversations') {
            $user = UserInfo::where(['vendor_id' => $store->vendor->id])->first();
            if ($user) {
                $conversations = Conversation::with(['sender', 'receiver', 'last_message'])->WhereUser($user->id)
                    ->paginate(8);
            } else {
                $conversations = [];
            }

            return view('admin-views.vendor.view.conversations', compact('store', 'sub_tab', 'conversations'));
        } elseif ($tab == 'meta-data') {
            $store = Store::withoutGlobalScope('translate')->findOrFail($store_id);

            return view('admin-views.vendor.view.meta-data', compact('store', 'sub_tab'));
        } elseif ($tab == 'disbursements') {
            $disbursements = DisbursementDetails::where('store_id', $store->id)
                ->when(isset($key), function ($q) use ($key) {
                    $q->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('disbursement_id', 'like', "%{$value}%")
                                ->orWhere('status', 'like', "%{$value}%");
                        }
                    });
                })
                ->latest()->paginate(config('default_pagination'));

            return view('admin-views.vendor.view.disbursement', compact('store', 'disbursements'));
        } elseif ($tab == 'business_plan') {

            $store = Store::where('id', $store->id)->with([
                'store_sub_update_application.package', 'vendor', 'store_sub_update_application.last_transcations', 'module:id,module_type',
            ])->withcount('items')
                ->first();
            $packages = SubscriptionPackage::where('status', 1)
                ->where('module_type', $store?->module?->module_type == 'rental' && addon_published_status('Rental') ? 'rental' : 'all')
                ->latest()->get();
            $admin_commission = BusinessSetting::where('key', 'admin_commission')->first()?->value;
            $business_name = BusinessSetting::where('key', 'business_name')->first()?->value;
            try {
                $index = $store->store_business_model == 'commission' ? 0 : 1 + array_search($store?->store_sub_update_application?->package_id ?? 1, array_column($packages->toArray(), 'id'));
            } catch (\Throwable $th) {
                $index = 2;
            }

            return view('admin-views.vendor.view.subscription', compact('store', 'packages', 'business_name', 'admin_commission', 'index'));

        }

        return view('admin-views.vendor.view.index', compact('store', 'wallet'));
    }

    public function disbursement_export(Request $request, $id, $type)
    {
        $key = explode(' ', $request['search']);

        $store = Store::find($id);
        $disbursements = DisbursementDetails::where('store_id', $store->id)
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('disbursement_id', 'like', "%{$value}%")
                            ->orWhere('status', 'like', "%{$value}%");
                    }
                });
            })
            ->latest()->get();
        $data = [
            'disbursements' => $disbursements,
            'search' => $request->search ?? null,
            'store' => $store->name,
            'type' => 'store',
            'is_provider' => $request->provider_id ?? null,
        ];

        if ($request->type == 'excel') {
            return Excel::download(new DisbursementHistoryExport($data), 'Disbursementlist.xlsx');
        } elseif ($request->type == 'csv') {
            return Excel::download(new DisbursementHistoryExport($data), 'Disbursementlist.csv');
        }
    }

    public function view_tab(Store $store)
    {

        Toastr::error(translate('messages.unknown_tab'));

        return back();
    }

    public function list(Request $request)
    {

        $data = Store::selectRaw('
        SUM(CASE WHEN EXISTS (
        SELECT 1 FROM vendors WHERE vendors.id = stores.vendor_id AND vendors.status = 1
        ) THEN 1 ELSE 0 END) as total_store,

        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active_stores,

        SUM(CASE WHEN status = 0 AND EXISTS (
            SELECT 1 FROM vendors WHERE vendors.id = stores.vendor_id AND vendors.status = 1
        ) THEN 1 ELSE 0 END) as inactive_stores,

        SUM(CASE WHEN created_at >= ? AND EXISTS (
            SELECT 1 FROM vendors WHERE vendors.id = stores.vendor_id AND vendors.status = 1
        ) THEN 1 ELSE 0 END) as recent_stores
        ', [now()->subDays(30)->toDateTimeString()])
            ->where('module_id', Config::get('module.current_module_id'))
            ->first();
        $total_store = $data->total_store;
        $active_stores = $data->active_stores;
        $inactive_stores = $data->inactive_stores;
        $recent_stores = $data->recent_stores;

        $key = explode(' ', $request['search']);

        $zone_id = $request->query('zone_id', 'all');
        $type = $request->query('type', 'all');
        $module_id = $request->query('module_id', 'all');
        $stores = Store::with('vendor', 'module', 'zone')->whereHas('vendor', function ($query) {
            return $query->where('status', 1);
        })
            ->when(is_numeric($zone_id), function ($query) use ($zone_id) {
                return $query->where('zone_id', $zone_id);
            })
            ->when(is_numeric($module_id), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(isset($key), function ($query) use ($key, $request) {
                return $query->where(function ($query) use ($key) {
                    $query->orWhereHas('vendor', function ($q) use ($key) {
                        $q->where(function ($q) use ($key) {
                            foreach ($key as $value) {
                                $q->orWhere('f_name', 'like', "%{$value}%")
                                    ->orWhere('l_name', 'like', "%{$value}%")
                                    ->orWhere('email', 'like', "%{$value}%")
                                    ->orWhere('phone', 'like', "%{$value}%");
                            }
                        });
                    })->orWhere(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('name', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%");
                        }
                    });
                })->orderByRaw('FIELD(name, ?) DESC', [$request->search]);
            })
            ->module(Config::get('module.current_module_id'))
            ->with('vendor', 'module')->type($type)->latest()->paginate(config('default_pagination'));
        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;

        $result = OrderTransaction::where('module_id', Config::get('module.current_module_id'))
            ->selectRaw('COUNT(*) as total_transaction, SUM(admin_commission) as commission_earned')
            ->NotRefunded()
            ->first();

        $total_transaction = $result->total_transaction;
        $comission_earned = max(0, $result->commission_earned);

        $store_withdraws = WithdrawRequest::wherehas('store', function ($query) {
            $query->where('module_id', Config::get('module.current_module_id'));
        })
            ->where(['approved' => 1])
            ->sum('amount');

        return view('admin-views.vendor.list', compact('stores', 'zone', 'type', 'total_store', 'active_stores', 'inactive_stores', 'recent_stores', 'total_transaction', 'comission_earned', 'store_withdraws'));
    }

    public function pending_requests(Request $request)
    {
        $stores = $this->getNewStores($request, null);
        $zone_id = $request->query('zone_id', 'all');
        $type = $request->query('type', 'all');
        $search_by = $request->query('search_by');
        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;

        return view('admin-views.vendor.pending_requests', compact('stores', 'zone', 'type', 'search_by'));
    }

    private function getNewStores($request, $storeApproveStatus)
    {

        $zone_id = $request->query('zone_id', 'all');
        $search_by = $request->query('search_by');
        $key = explode(' ', $search_by);
        $type = $request->query('type', 'all');
        $module_id = $request->query('module_id', 'all');

        $stores = Store::with('vendor:id,f_name,l_name,status', 'module:id,module_name', 'zone:id,name')->whereHas('vendor', function ($query) use ($storeApproveStatus) {
            return $query->where('status', $storeApproveStatus);
        })
            ->when(is_numeric($zone_id), function ($query) use ($zone_id) {
                return $query->where('zone_id', $zone_id);
            })
            ->when(is_numeric($module_id), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when($search_by, function ($query) use ($key) {
                return $query->where(function ($query) use ($key) {
                    $query->orWhereHas('vendor', function ($q) use ($key) {
                        $q->where(function ($q) use ($key) {
                            foreach ($key as $value) {
                                $q->orWhereAny(['f_name', 'l_name', 'email', 'phone'], 'like', "%{$value}%");
                            }
                        });
                    })->orWhere(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhereAny(['name', 'email', 'phone'], 'like', "%{$value}%");
                        }
                    });
                });
            })
            ->module(Config::get('module.current_module_id'))
            ->type($type)->latest()->paginate(config('default_pagination'));

        return $stores;
    }

    public function deny_requests(Request $request)
    {
        $search_by = $request->query('search_by');
        $zone_id = $request->query('zone_id', 'all');
        $type = $request->query('type', 'all');
        $stores = $this->getNewStores($request, 0);
        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;

        return view('admin-views.vendor.deny_requests', compact('stores', 'zone', 'type', 'search_by'));
    }

    public function export(Request $request)
    {

        $key = explode(' ', $request['search']);

        $zone_id = $request->query('zone_id', 'all');
        $module_id = $request->query('module_id', 'all');
        $stores = Store::whereHas('vendor', function ($query) {
            return $query->where('status', 1);
        })
            ->when(is_numeric($zone_id), function ($query) use ($zone_id) {
                return $query->where('zone_id', $zone_id);
            })
            ->when(is_numeric($module_id), function ($query) use ($request) {
                return $query->module($request->query('module_id'));
            })
            ->when(isset($key), function ($query) use ($key) {
                return $query->where(function ($query) use ($key) {
                    $query->orWhereHas('vendor', function ($q) use ($key) {
                        $q->where(function ($q) use ($key) {
                            foreach ($key as $value) {
                                $q->orWhere('f_name', 'like', "%{$value}%")
                                    ->orWhere('l_name', 'like', "%{$value}%")
                                    ->orWhere('email', 'like', "%{$value}%")
                                    ->orWhere('phone', 'like', "%{$value}%");
                            }
                        });
                    })->orWhere(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('name', 'like', "%{$value}%")
                                ->orWhere('email', 'like', "%{$value}%")
                                ->orWhere('phone', 'like', "%{$value}%");
                        }
                    });
                });
            })
            ->module(Config::get('module.current_module_id'))
            ->with('vendor', 'module')
            ->orderBy('id', 'DESC')
            ->withCount('items')
            ->get();

        $data = [
            'data' => $stores,
            'zone' => is_numeric($zone_id) ? Helpers::get_zones_name($zone_id) : null,
            'module' => request('module_id') ? Helpers::get_module_name(Config::get('module.current_module_id')) : null,
            'search' => $request['search'] ?? null,
            'is_rental' => $request['is_rental'] ?? 0,
        ];

        $fileName = $request->is_rental == 1 ? 'Providers' : 'Stores';

        if ($request->type == 'csv') {
            return Excel::download(new StoreListExport($data), $fileName.'.csv');
        }

        return Excel::download(new StoreListExport($data), $fileName.'.xlsx');

    }

    public function get_stores(Request $request)
    {
        $zone_ids = isset($request->zone_ids) ? (count($request->zone_ids) > 0 ? $request->zone_ids : []) : 0;
        $data = Store::whereHas('module', function ($q) {
            $q->whereNot('module_type', 'rental');
        })->
        when($zone_ids, function ($query) use ($zone_ids) {
            $query->whereIn('stores.zone_id', [$zone_ids]);
        })
            ->when($request->module_id, function ($query) use ($request) {
                $query->where('module_id', $request->module_id);
            })
            ->when($request->module_type, function ($query) use ($request) {
                $query->whereHas('module', function ($q) use ($request) {
                    $q->where('module_type', $request->module_type);
                });
            })
            ->where('stores.name', 'like', '%'.$request->q.'%')
            ->limit(8)->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'text' => $store->name.' ('.$store->zone?->name.')',
                ];
            });
        if (isset($request->all)) {
            $data[] = (object) ['id' => 'all', 'text' => translate('messages.all')];
        }

        return response()->json($data);
    }

    public function get_providers(Request $request)
    {
        $zone_ids = isset($request->zone_ids) ? (count($request->zone_ids) > 0 ? $request->zone_ids : []) : [];

        $data = Store::wherehas('vendor', function ($query) {
            $query->where('status', 1);
        })
            ->when(count($zone_ids) > 0, function ($query) use ($zone_ids) {
                $query->whereIn('zone_id', $zone_ids);
            })
            ->when($request->module_id, function ($query) use ($request) {
                $query->where('module_id', $request->module_id);
            })
            ->whereHas('module', function ($q) {
                $q->where('module_type', 'rental');
            })
            ->where('name', 'like', '%'.$request->q.'%')
            ->limit(8)
            ->get()
            ->map(function ($store) {
                return [
                    'id' => $store->id,
                    'text' => $store->name.' ('.$store->zone?->name.')',
                ];
            });

        if (isset($request->all)) {
            $data[] = (object) ['id' => 'all', 'text' => translate('messages.all')];
        }

        return response()->json($data);
    }

    public function status(Store $store, Request $request)
    {
        $store->status = $request->status;
        $store->save();
        $vendor = $store->vendor;

        try {
            if ($request->status == 0) {
                $vendor->auth_token = null;
                if (isset($vendor->firebase_token) && Helpers::getNotificationStatusData('store', 'store_account_block', 'push_notification_status', $store?->id)) {
                    $data = [
                        'title' => translate('messages.suspended'),
                        'description' => translate('messages.your_account_has_been_suspended'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'block',
                    ];
                    Helpers::send_push_notif_to_device($vendor->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $vendor->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if (config('mail.status') && Helpers::get_mail_status('suspend_mail_status_store') == '1' && Helpers::getNotificationStatusData('store', 'store_account_block', 'mail_status', $store?->id)) {
                    Mail::to($vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorStatus('suspended', $vendor?->f_name.' '.$vendor?->l_name));
                }
            } else {

                if (Helpers::getNotificationStatusData('store', 'store_account_unblock', 'push_notification_status', $store?->id) && isset($vendor->firebase_token)) {
                    $data = [
                        'title' => translate('Account_Activation'),
                        'description' => translate('messages.your_account_has_been_activated'),
                        'order_id' => '',
                        'image' => '',
                        'type' => 'unblock',
                    ];
                    Helpers::send_push_notif_to_device($vendor->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data' => json_encode($data),
                        'vendor_id' => $vendor->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                if (config('mail.status') && Helpers::get_mail_status('unsuspend_mail_status_store') == '1' && Helpers::getNotificationStatusData('store', 'store_account_unblock', 'mail_status', $store?->id)) {
                    Mail::to($vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorStatus('unsuspended', $vendor?->f_name.' '.$vendor?->l_name));
                }
            }

        } catch (\Exception $e) {
            Toastr::warning(translate('messages.push_notification_faild'));
        }

        Toastr::success(translate('messages.store_status_updated'));

        return back();
    }

    public function store_status(Store $store, Request $request)
    {
        if ($request->menu == 'schedule_order' && ! Helpers::schedule_order()) {
            Toastr::warning(translate('messages.schedule_order_disabled_warning'));

            return back();
        }

        if ((($request->menu == 'delivery' && $store->take_away == 0) || ($request->menu == 'take_away' && $store->delivery == 0)) && $request->status == 0) {
            Toastr::warning(translate('messages.can_not_disable_both_take_away_and_delivery'));

            return back();
        }

        if ((($request->menu == 'veg' && $store->non_veg == 0) || ($request->menu == 'non_veg' && $store->veg == 0)) && $request->status == 0) {
            Toastr::warning(translate('messages.veg_non_veg_disable_warning'));

            return back();
        }
        if ($request->menu == 'self_delivery_system' && $request->status == '0') {
            $store['free_delivery'] = 0;
        }

        if ($request->menu == 'halal_tag_status') {
            $conf = StoreConfig::firstOrNew(
                ['store_id' => $store->id]
            );
            $conf[$request->menu] = $request->status;
            $conf->save();
            Toastr::success(translate('messages.Store_settings_updated!'));

            return back();
        }

        $store[$request->menu] = $request->status;
        $store->save();
        Toastr::success(translate('messages.vendor_settings_updated'));

        return back();
    }

    public function discountSetup(Store $store, Request $request)
    {
        $message = $store->discount ? translate('messages.discount_updated_successfully') : translate('messages.discount_added_successfully');
        $store->discount()->updateOrinsert(
            [
                'store_id' => $store->id,
            ],
            [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'min_purchase' => $request->min_purchase != null ? $request->min_purchase : 0,
                'max_discount' => $request->max_discount != null ? $request->max_discount : 0,
                'discount' => $request->discount_type == 'amount' ? $request->discount : $request['discount'],
                'discount_type' => 'percent',
            ]
        );

        return response()->json(['message' => $message], 200);
    }

    public function updateStoreSettings(Store $store, Request $request)
    {
        if ($request?->tab == 'business_plan') {
            $store->comission = $request->comission_status ? $request->comission : null;
            $store->save();
            Toastr::success(translate('messages.Commission_updated'));

            return back();
        }
        $request->validate([
            'minimum_order' => 'required',
            'minimum_delivery_time' => 'required|min:1|max:2',
            'maximum_delivery_time' => 'required|min:1|max:2|gt:minimum_delivery_time',
        ]);

        $store->minimum_order = $request->minimum_order;
        $store->order_place_to_schedule_interval = $request->order_place_to_schedule_interval;
        $store->delivery_time = $request->minimum_delivery_time.'-'.$request->maximum_delivery_time.' '.$request->delivery_time_type;
        $store->veg = (bool) ($request->veg_non_veg == 'veg' || $request->veg_non_veg == 'both');
        $store->non_veg = (bool) ($request->veg_non_veg == 'non_veg' || $request->veg_non_veg == 'both');

        $store->save();
        Toastr::success(translate('messages.store_settings_updated'));

        return back();
    }

    public function updateStoreMetaData(Store $store, Request $request)
    {
        if ($request->has('meta_image_deleted') && $request->meta_image_deleted == 1) {
            Helpers::check_and_delete('store/', $store->meta_image);
            $store->meta_image = null;
        }

        $store->meta_image = $request->has('meta_image') ? Helpers::update('store/', $store->meta_image, 'png', $request->file('meta_image')) : $store->meta_image;

        $store->meta_title = $request->meta_title;
        $store->meta_description = $request->meta_description;
        $store->meta_data = Helpers::formatMetaData($request->all(), $store->meta_data);
        $store->save();

        Toastr::success(translate('messages.meta_data_updated'));

        return back();
    }

    public function update_application(Request $request)
    {
        $this->updateVendorApplication($request);
        Toastr::success(translate('messages.application_status_updated_successfully'));

        return redirect(route('admin.store.pending-requests'));
    }


     private function updateVendorApplication($request)
    {
        $store = Store::findOrFail($request->id);
        $store->vendor->status = $request->status;
        $store->vendor->rejection_note = $request->rejection_note;
        $store->vendor->save();
        if ($request->status) {
            $store->status = 1;
        }

        $add_days = 1;
        if ($store?->store_sub_update_application) {
            if ($store?->store_sub_update_application && $store?->store_sub_update_application->is_trial == 1) {
                $add_days = BusinessSetting::where(['key' => 'subscription_free_trial_days'])->first()?->value ?? 1;
            } elseif ($store?->store_sub_update_application && $store?->store_sub_update_application->is_trial == 0) {
                $add_days = $store?->store_sub_update_application->validity;
            }
            $store?->store_sub_update_application->update([
                'expiry_date' => Carbon::now()->addDays((int) $add_days)->format('Y-m-d'),
                'status' => 1,
            ]);
            $store->store_business_model = 'subscription';
        }
        $store->save();
        try {
            if ($request->status == 1) {
                if (config('mail.status') && Helpers::get_mail_status('approve_mail_status_store') == '1' && Helpers::getNotificationStatusData('store', 'store_registration_approval', 'mail_status')) {
                    Mail::to($store?->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorSelfRegistration('approved', $store->vendor->f_name.' '.$store->vendor->l_name));
                }
            } else {
                if (config('mail.status') && Helpers::get_mail_status('deny_mail_status_store') == '1' && Helpers::getNotificationStatusData('store', 'store_registration_deny', 'mail_status')) {
                    Mail::to($store?->vendor?->getRawOriginal('email'))->send(new \App\Mail\VendorSelfRegistration('denied', $store->vendor->f_name.' '.$store->vendor->l_name));
                }
            }
        } catch (\Exception $ex) {
            info($ex->getMessage());
        }

        return true;
    }



    public function cleardiscount(Store $store)
    {
        $store->discount->delete();
        Toastr::success(translate('messages.store_discount_cleared'));

        return back();
    }

    public function withdraw(Request $request)
    {
        $key = isset($request['search']) ? explode(' ', $request['search']) : [];
        $all = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all' ? 1 : 0;
        $active = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved' ? 1 : 0;
        $denied = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied' ? 1 : 0;
        $pending = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending' ? 1 : 0;

        $withdraw_req = WithdrawRequest::with(['vendor.stores'])
            ->when($all, function ($query) {
                return $query;
            })
            ->when($active, function ($query) {
                return $query->where('approved', 1);
            })
            ->when($denied, function ($query) {
                return $query->where('approved', 2);
            })
            ->when($pending, function ($query) {
                return $query->where('approved', 0);
            })
            ->when(isset($key), function ($query) use ($key) {
                return $query->whereHas('vendor', function ($query) use ($key) {
                    $query->whereHas('stores', function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('name', 'like', "%{$value}%");
                        }
                    });
                });
            })
            ->latest()
            ->paginate(config('default_pagination'));

        if (! Helpers::module_permission_check('withdraw_list')) {
            return view('admin-views.wallet.withdraw-dashboard');
        }

        return view('admin-views.wallet.withdraw', compact('withdraw_req'));
    }

    public function withdraw_export(Request $request)
    {
        $key = isset($request['search']) ? explode(' ', $request['search']) : [];
        $all = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'all' ? 1 : 0;
        $active = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'approved' ? 1 : 0;
        $denied = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'denied' ? 1 : 0;
        $pending = session()->has('withdraw_status_filter') && session('withdraw_status_filter') == 'pending' ? 1 : 0;

        $withdraw_req = WithdrawRequest::with(['vendor'])
            ->when($all, function ($query) {
                return $query;
            })
            ->when($active, function ($query) {
                return $query->where('approved', 1);
            })
            ->when($denied, function ($query) {
                return $query->where('approved', 2);
            })
            ->when($pending, function ($query) {
                return $query->where('approved', 0);
            })
            ->when(isset($key), function ($query) use ($key) {
                return $query->whereHas('vendor', function ($query) use ($key) {
                    $query->whereHas('stores', function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('name', 'like', "%{$value}%");
                        }
                    });
                });
            })
            ->latest()->get();

        $data = [
            'withdraw_requests' => $withdraw_req,
            'search' => $request->search ?? null,
            'request_status' => session()->has('withdraw_status_filter') ? session('withdraw_status_filter') : null,

        ];

        if ($request->type == 'excel') {
            return Excel::download(new StoreWithdrawTransactionExport($data), 'WithdrawRequests.xlsx');
        } elseif ($request->type == 'csv') {
            return Excel::download(new StoreWithdrawTransactionExport($data), 'WithdrawRequests.csv');
        }
    }

    public function getWithdrawDetails(Request $request)
    {
        $withdraw = WithdrawRequest::with(['vendor.stores'])->where(['id' => $request->withdraw_id])->first();

        return response()->json([
            'view' => view('admin-views.wallet.partials._side_view', compact('withdraw'))->render(),
        ]);
    }

    public function withdraw_search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $withdraw_req = WithdrawRequest::whereHas('vendor', function ($query) use ($key) {
            $query->whereHas('stores', function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            });
        })->get();
        $total = $withdraw_req->count();

        return response()->json([
            'view' => view('admin-views.wallet.partials._table', compact('withdraw_req'))->render(), 'total' => $total,
        ]);
    }

    public function withdraw_view($withdraw_id, $seller_id)
    {
        $wr = WithdrawRequest::with(['vendor'])->where(['id' => $withdraw_id])->first();

        return view('admin-views.wallet.withdraw-view', compact('wr'));
    }

    public function status_filter(Request $request)
    {
        session()->put('withdraw_status_filter', $request['withdraw_status_filter']);

        return response()->json(session('withdraw_status_filter'));
    }

    public function withdrawStatus(Request $request, $id)
    {
        $request->validate([
            'note' => 'max:200',
        ]);
        $withdraw = WithdrawRequest::findOrFail($id);
        $withdraw->approved = $request->approved;
        $withdraw->transaction_note = $request['note'];

        $wallet = StoreWallet::where('vendor_id', $withdraw->vendor_id)->first();
        if ((string) $wallet->total_earning < (string) ($wallet->total_withdrawn + $wallet->pending_withdraw)) {
            Toastr::error(translate('messages.Blalnce_mismatched_total_earning_is_too_low'));

            return redirect()->route('admin.restaurant.withdraw_list');
        }

        $vendor = $withdraw->vendor;
        $store = $withdraw->vendor?->stores[0];
        $moduleType = $store?->module->module_type;

        if ($request->approved == 1) {
            $wallet->increment('total_withdrawn', $withdraw->amount);
            $wallet->decrement('pending_withdraw', $withdraw->amount);
            $withdraw->save();
            $push_notification_status = $moduleType == 'rental' ? Helpers::getRentalNotificationStatusData('provider', 'provider_withdraw_approve', 'push_notification_status', $store->id) : Helpers::getNotificationStatusData('store', 'store_withdraw_approve', 'push_notification_status', $store->id);
            $push_notification_status = $push_notification_status == 1 && $vendor?->firebase_token ? 1 : 0;

            $mail_status = $moduleType == 'rental' ? (config('mail.status') && Helpers::get_mail_status('rental_withdraw_approve_mail_status_provider') == '1' && Helpers::getRentalNotificationStatusData('provider', 'provider_withdraw_approve', 'mail_status', $store->id)) : (config('mail.status') && Helpers::get_mail_status('withdraw_approve_mail_status_store') == '1' && Helpers::getNotificationStatusData('store', 'store_withdraw_approve', 'mail_status', $store->id));

            $this->sentWithdrawRequestNotification($withdraw, $vendor->firebase_token, $vendor->getRawOriginal('email'), 'approved', $moduleType, $push_notification_status, $mail_status);

            Toastr::success(translate('messages.vendor_withdraw_request_approved'));

            return redirect()->route('admin.transactions.store.withdraw_list');
        } elseif ($request->approved == 2) {
            $wallet->decrement('pending_withdraw', $withdraw->amount);
            $withdraw->save();

            $push_notification_status = $moduleType == 'rental' ? Helpers::getRentalNotificationStatusData('provider', 'provider_withdraw_rejaction', 'push_notification_status', $store->id) : Helpers::getNotificationStatusData('store', 'store_withdraw_rejaction', 'push_notification_status', $store->id);
            $push_notification_status = $push_notification_status == 1 && $vendor?->firebase_token ? 1 : 0;

            $mail_status = $moduleType == 'rental' ? (config('mail.status') && Helpers::get_mail_status('rental_withdraw_deny_mail_status_provider') == '1' && Helpers::getRentalNotificationStatusData('provider', 'provider_withdraw_rejaction', 'mail_status', $store->id)) : (config('mail.status') && Helpers::get_mail_status('withdraw_deny_mail_status_store') == '1' && Helpers::getNotificationStatusData('store', 'store_withdraw_rejaction', 'mail_status', $store->id));

            $this->sentWithdrawRequestNotification($withdraw, $vendor->firebase_token,  $vendor->getRawOriginal('email'), 'denied', $moduleType, $push_notification_status, $mail_status);

            Toastr::info(translate('messages.vendor_withdraw_request_denied'));

            return redirect()->route('admin.transactions.store.withdraw_list');
        } else {
            Toastr::error(translate('messages.not_found'));

            return back();
        }
    }

    private function sentWithdrawRequestNotification($withdraw, $token, $email, $type = 'approved', $module_type = 'all', $push_notification_status = '1', $mail_status = '1')
    {
        try {
            if ($push_notification_status == 1) {
                $data = [
                    'title' => $type == 'approved' ? translate('Withdraw_approved') : translate('Withdraw_rejected'),
                    'description' => $type == 'approved' ? translate('Withdraw_request_approved_by_admin') : translate('Withdraw_request_rejected_by_admin'),
                    'order_id' => '',
                    'image' => '',
                    'type' => 'withdraw',
                    'order_status' => '',
                ];
                Helpers::send_push_notif_to_device($token, $data);
                DB::table('user_notifications')->insert([
                    'data' => json_encode($data),
                    'vendor_id' => $withdraw->vendor_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($mail_status == 1) {
                Mail::to($email)->send($module_type == 'rental' ? new ProviderWithdrawRequestMail($type, $withdraw) : new WithdrawRequestMail($type, $withdraw));
            }
        } catch (\Exception $e) {
            info($e->getMessage());
        }

        return true;
    }

    public function get_addons(Request $request)
    {
        $cat = AddOn::withoutGlobalScope(StoreScope::class)->
        // withoutGlobalScope('translate')->
        where(['store_id' => $request->store_id])->active()->get();
        $res = '';
        foreach ($cat as $row) {
            $res .= '<option value="'.$row->id.'"';
            if (count($request->data)) {
                $res .= in_array($row->id, $request->data) ? 'selected' : '';
            }
            $res .= '>'.$row->name.'</option>';
        }

        return response()->json([
            'options' => $res,
        ]);
    }

    public function get_store_data(Store $store)
    {
        return response()->json($store);
    }

    public function store_filter($id)
    {
        if ($id == 'all') {
            if (session()->has('store_filter')) {
                session()->forget('store_filter');
            }
        } else {
            session()->put('store_filter', Store::where('id', $id)->first(['id', 'name']));
        }

        return back();
    }

    public function get_account_data(Store $store)
    {
        $wallet = $store->vendor->wallet;
        $cash_in_hand = 0;
        $balance = 0;

        if ($wallet) {
            $cash_in_hand = $wallet->collected_cash;
            $balance = $wallet->total_earning;
        }

        return response()->json(['cash_in_hand' => $cash_in_hand, 'earning_balance' => $balance], 200);

    }

    public function bulk_import_index()
    {
        return view('admin-views.vendor.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        $request->validate([
            'products_file' => 'required|max:2048',
        ]);
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));

            return back();
        }
        $duplicate_phones = $collections->duplicates('phone');
        $duplicate_emails = $collections->duplicates('email');

        if ($duplicate_emails->isNotEmpty()) {
            Toastr::error(translate('messages.duplicate_data_on_column', ['field' => translate('messages.email')]));

            return back();
        }

        if ($duplicate_phones->isNotEmpty()) {
            Toastr::error(translate('messages.duplicate_data_on_column', ['field' => translate('messages.phone')]));

            return back();
        }

        $email = $collections->pluck('email')->toArray();
        $phone = $collections->pluck('phone')->toArray();

        if ($request->button == 'import') {

            if (Store::whereIn('email', $email)->orWhereIn('phone', $phone)->exists()
            ) {
                Toastr::error(translate('messages.duplicate_email_or_phone_exists_at_the_database'));

                return back();
            }

            $vendors = [];
            $stores = [];
            $vendor = Vendor::orderBy('id', 'desc')->first('id');
            $vendor_id = $vendor ? $vendor->id : 0;
            $store = Store::orderBy('id', 'desc')->first('id');
            $store_id = $store ? $store->id : 0;
            $store_ids = [];
            foreach ($collections as $key => $collection) {
                if ($collection['ownerFirstName'] === '' || $collection['storeName'] === '' || $collection['phone'] === ''
                || $collection['email'] === '' || $collection['latitude'] === '' || $collection['longitude'] === ''
                || $collection['zone_id'] === '' || $collection['DeliveryTime'] === '' || $collection['logo'] === '') {
                    Toastr::error(translate('messages.please_fill_all_required_fields'));

                    return back();
                }
                if (isset($collection['DeliveryTime']) && explode('-', (string) $collection['DeliveryTime'])[0] > explode('-', (string) $collection['DeliveryTime'])[1]) {
                    Toastr::error('messages.max_delivery_time_must_be_greater_than_min_delivery_time');

                    return back();
                }
                if (isset($collection['Comission']) && ($collection['Comission'] < 0 || $collection['Comission'] > 100)) {
                    Toastr::error('messages.Comission_must_be_in_0_to_100');

                    return back();
                }

                if (isset($collection['latitude']) && ($collection['latitude'] < -90 || $collection['latitude'] > 90)) {
                    Toastr::error('messages.latitude_must_be_in_-90_to_90');

                    return back();
                }
                if (isset($collection['longitude']) && ($collection['longitude'] < -180 || $collection['longitude'] > 180)) {
                    Toastr::error('messages.longitude_must_be_in_-180_to_180');

                    return back();
                }
                if (isset($collection['MinimumDeliveryFee']) && ($collection['MinimumDeliveryFee'] < 0)) {
                    Toastr::error('messages.Enter_valid_Minimum_Delivery_Fee');

                    return back();
                }
                if (isset($collection['MinimumOrderAmount']) && ($collection['MinimumOrderAmount'] < 0)) {
                    Toastr::error('messages.Enter_valid_Minimum_Order_Amount');

                    return back();
                }
                if (isset($collection['PerKmDeliveryFee']) && ($collection['PerKmDeliveryFee'] < 0)) {
                    Toastr::error('messages.Enter_valid_Per_Km_Delivery_Fee');

                    return back();
                }
                if (isset($collection['MaximumDeliveryFee']) && ($collection['MaximumDeliveryFee'] < 0)) {
                    Toastr::error('messages.Enter_valid_Maximum_Delivery_Fee');

                    return back();
                }

                array_push($vendors, [
                    'id' => $vendor_id + $key + 1,
                    'f_name' => $collection['ownerFirstName'],
                    'l_name' => $collection['ownerLastName'],
                    'password' => bcrypt(12345678),
                    'phone' => $collection['phone'],
                    'email' => $collection['email'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                array_push($stores, [
                    'name' => $collection['storeName'],
                    'phone' => $collection['phone'],
                    'email' => $collection['email'],
                    'logo' => $collection['logo'],
                    'cover_photo' => $collection['CoverPhoto'],
                    'latitude' => $collection['latitude'],
                    'longitude' => $collection['longitude'],
                    'address' => $collection['Address'],
                    'zone_id' => $collection['zone_id'],
                    'module_id' => $collection['module_id'],
                    'minimum_order' => $collection['MinimumOrderAmount'],
                    'comission' => $collection['Comission'],

                    'delivery_time' => (isset($collection['DeliveryTime']) && preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $collection['DeliveryTime'])) ? $collection['DeliveryTime'] : '30-40 min',
                    'minimum_shipping_charge' => $collection['MinimumDeliveryFee'],
                    'per_km_shipping_charge' => $collection['PerKmDeliveryFee'],
                    'maximum_shipping_charge' => $collection['MaximumDeliveryFee'],
                    'schedule_order' => $collection['ScheduleOrder'] == 'yes' ? 1 : 0,
                    'status' => $collection['Status'] == 'active' ? 1 : 0,
                    'self_delivery_system' => $collection['SelfDeliverySystem'] == 'active' ? 1 : 0,
                    'veg' => $collection['Veg'] == 'yes' ? 1 : 0,
                    'non_veg' => $collection['NonVeg'] == 'yes' ? 1 : 0,
                    'free_delivery' => $collection['FreeDelivery'] == 'yes' ? 1 : 0,
                    'take_away' => $collection['TakeAway'] == 'yes' ? 1 : 0,
                    'delivery' => $collection['Delivery'] == 'yes' ? 1 : 0,
                    'reviews_section' => $collection['ReviewsSection'] == 'active' ? 1 : 0,
                    'pos_system' => $collection['PosSystem'] == 'active' ? 1 : 0,
                    'active' => $collection['storeOpen'] == 'yes' ? 1 : 0,
                    'featured' => $collection['FeaturedStore'] == 'yes' ? 1 : 0,
                    'vendor_id' => $vendor_id + $key + 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                if ($module = Module::select('module_type')->where('id', $collection['module_id'])->first()) {
                    if (config('module.'.$module->module_type)) {
                        $store_ids[] = $store_id + $key + 1;
                    }
                }

            }

            // $data = array_map(function($id){
            //     return array_map(function($item)use($id){
            //         return     ['store_id'=>$id,'day'=>$item,'opening_time'=>'00:00:00','closing_time'=>'23:59:59'];
            //     },[0,1,2,3,4,5,6]);
            // },$store_ids);

            try {
                DB::beginTransaction();

                $chunkSize = 100;
                $chunk_stores = array_chunk($stores, $chunkSize);
                $chunk_vendors = array_chunk($vendors, $chunkSize);

                foreach ($chunk_stores as $key => $chunk_store) {
                    DB::table('vendors')->insert($chunk_vendors[$key]);
                    //                    DB::table('stores')->insert($chunk_store);
                    foreach ($chunk_store as $store) {
                        $insertedId = DB::table('stores')->insertGetId($store);
                        Helpers::updateStorageTable(get_class(new Store), $insertedId, $store['logo']);
                        Helpers::updateStorageTable(get_class(new Store), $insertedId, $store['cover_photo']);
                        StoreLogic::insert_schedule($insertedId);
                    }
                }
                // DB::table('store_schedule')->insert(array_merge(...$data));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                info(["line___{$e->getLine()}", $e->getMessage()]);
                Toastr::error(translate('messages.failed_to_import_data'));

                return back();
            }

            Toastr::success(translate('messages.store_imported_successfully', ['count' => count($stores)]));

            return back();
        }

        if (Store::whereIn('email', $email)->orWhereIn('phone', $phone)->doesntExist()
        ) {
            Toastr::error(translate('messages.email_or_phone_doesnt_exist_at_the_database'));

            return back();
        }

        $vendors = [];
        $stores = [];
        $vendor = Vendor::orderBy('id', 'desc')->first('id');
        $vendor_id = $vendor ? $vendor->id : 0;
        $store = Store::orderBy('id', 'desc')->first('id');
        $store_id = $store ? $store->id : 0;
        $store_ids = [];
        foreach ($collections as $key => $collection) {
            if ($collection['id'] === '' || $collection['ownerId'] === '' || $collection['ownerFirstName'] === '' || $collection['storeName'] === '' || $collection['phone'] === ''
            || $collection['email'] === '' || $collection['latitude'] === '' || $collection['longitude'] === ''
            || $collection['zone_id'] === '' || $collection['DeliveryTime'] === '' || $collection['logo'] === '') {
                Toastr::error(translate('messages.please_fill_all_required_fields'));

                return back();
            }
            if (isset($collection['DeliveryTime']) && explode('-', (string) $collection['DeliveryTime'])[0] > explode('-', (string) $collection['DeliveryTime'])[1]) {
                Toastr::error('messages.max_delivery_time_must_be_greater_than_min_delivery_time');

                return back();
            }
            if (isset($collection['Comission']) && ($collection['Comission'] < 0 || $collection['Comission'] > 100)) {
                Toastr::error('messages.Comission_must_be_in_0_to_100');

                return back();
            }

            if (isset($collection['latitude']) && ($collection['latitude'] < -90 || $collection['latitude'] > 90)) {
                Toastr::error('messages.latitude_must_be_in_-90_to_90');

                return back();
            }
            if (isset($collection['longitude']) && ($collection['longitude'] < -180 || $collection['longitude'] > 180)) {
                Toastr::error('messages.longitude_must_be_in_-180_to_180');

                return back();
            }
            if (isset($collection['MinimumDeliveryFee']) && ($collection['MinimumDeliveryFee'] < 0)) {
                Toastr::error('messages.Enter_valid_Minimum_Delivery_Fee');

                return back();
            }
            if (isset($collection['MinimumOrderAmount']) && ($collection['MinimumOrderAmount'] < 0)) {
                Toastr::error('messages.Enter_valid_Minimum_Order_Amount');

                return back();
            }
            if (isset($collection['PerKmDeliveryFee']) && ($collection['PerKmDeliveryFee'] < 0)) {
                Toastr::error('messages.Enter_valid_Per_Km_Delivery_Fee');

                return back();
            }
            if (isset($collection['MaximumDeliveryFee']) && ($collection['MaximumDeliveryFee'] < 0)) {
                Toastr::error('messages.Enter_valid_Maximum_Delivery_Fee');

                return back();
            }

            array_push($vendors, [
                'id' => $collection['ownerId'],
                'f_name' => $collection['ownerFirstName'],
                'l_name' => $collection['ownerLastName'],
                'password' => bcrypt(12345678),
                'phone' => $collection['phone'],
                'email' => $collection['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            array_push($stores, [
                'id' => $collection['id'],
                'name' => $collection['storeName'],
                'phone' => $collection['phone'],
                'email' => $collection['email'],
                'logo' => $collection['logo'],
                'cover_photo' => $collection['CoverPhoto'],
                'latitude' => $collection['latitude'],
                'longitude' => $collection['longitude'],
                'address' => $collection['Address'],
                'zone_id' => $collection['zone_id'],
                'module_id' => $collection['module_id'],
                'minimum_order' => $collection['MinimumOrderAmount'],
                'comission' => $collection['Comission'],

                'delivery_time' => (isset($collection['DeliveryTime']) && preg_match('([0-9]+[\-][0-9]+\s[min|hours|days])', $collection['DeliveryTime'])) ? $collection['DeliveryTime'] : '30-40 min',
                'minimum_shipping_charge' => $collection['MinimumDeliveryFee'],
                'per_km_shipping_charge' => $collection['PerKmDeliveryFee'],
                'maximum_shipping_charge' => $collection['MaximumDeliveryFee'],
                'schedule_order' => $collection['ScheduleOrder'] == 'yes' ? 1 : 0,
                'status' => $collection['Status'] == 'active' ? 1 : 0,
                'self_delivery_system' => $collection['SelfDeliverySystem'] == 'active' ? 1 : 0,
                'veg' => $collection['Veg'] == 'yes' ? 1 : 0,
                'non_veg' => $collection['NonVeg'] == 'yes' ? 1 : 0,
                'free_delivery' => $collection['FreeDelivery'] == 'yes' ? 1 : 0,
                'take_away' => $collection['TakeAway'] == 'yes' ? 1 : 0,
                'delivery' => $collection['Delivery'] == 'yes' ? 1 : 0,
                'reviews_section' => $collection['ReviewsSection'] == 'active' ? 1 : 0,
                'pos_system' => $collection['PosSystem'] == 'active' ? 1 : 0,
                'active' => $collection['storeOpen'] == 'yes' ? 1 : 0,
                'featured' => $collection['FeaturedStore'] == 'yes' ? 1 : 0,
                'vendor_id' => $collection['id'],
                'updated_at' => now(),
            ]);
        }

        try {
            $chunkSize = 100;
            $chunk_stores = array_chunk($stores, $chunkSize);
            $chunk_vendors = array_chunk($vendors, $chunkSize);

            DB::beginTransaction();

            foreach ($chunk_stores as $key => $chunk_store) {
                DB::table('vendors')->upsert($chunk_vendors[$key], ['id', 'email', 'phone'], ['f_name', 'l_name', 'password']);
                //                    DB::table('stores')->upsert($chunk_store,['id','email','phone','vendor_id'],['name','logo','cover_photo','latitude','longitude','address','zone_id','module_id','minimum_order','comission','tax','delivery_time','minimum_shipping_charge','per_km_shipping_charge','maximum_shipping_charge','schedule_order','status','self_delivery_system','veg','non_veg','free_delivery','take_away','delivery','reviews_section','pos_system','active','featured']);
                foreach ($chunk_store as $store) {
                    if (isset($store['id']) && DB::table('items')->where('id', $store['id'])->exists()) {
                        DB::table('stores')->where('id', $store['id'])->update($store);
                        Helpers::updateStorageTable(get_class(new Store), $store['id'], $store['logo']);
                        Helpers::updateStorageTable(get_class(new Store), $store['id'], $store['cover_photo']);
                    } else {
                        $insertedId = DB::table('stores')->insertGetId($store);
                        Helpers::updateStorageTable(get_class(new Store), $insertedId, $store['logo']);
                        Helpers::updateStorageTable(get_class(new Store), $insertedId, $store['cover_photo']);
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            info(["line___{$e->getLine()}", $e->getMessage()]);
            Toastr::error(translate('messages.failed_to_import_data'));

            return back();
        }

        Toastr::success(translate('messages.store_imported_successfully', ['count' => count($stores)]));

        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.vendor.bulk-export');
    }

    public function bulk_export_data(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'start_id' => 'required_if:type,id_wise',
            'end_id' => 'required_if:type,id_wise',
            'from_date' => 'required_if:type,date_wise',
            'to_date' => 'required_if:type,date_wise',
        ]);
        $vendors = Vendor::with('stores')
            ->when($request['type'] == 'date_wise', function ($query) use ($request) {
                $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
            })
            ->when($request['type'] == 'id_wise', function ($query) use ($request) {
                $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
            })->whereHas('stores', function ($q) {
                return $q->where('module_id', Config::get('module.current_module_id'));
            })
            ->get();

        // Export consumes only a few MB, even with 10M+ rows.
        return (new FastExcel(StoreLogic::format_export_stores(Helpers::Export_generator($vendors))))->download('Stores.xlsx');
        // return (new FastExcel(StoreLogic::format_export_stores($vendors)))->download('Stores.xlsx');
    }

    public function add_schedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'store_id' => 'required',
        ], [
            'end_time.after' => translate('messages.End time must be after the start time'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $temp = StoreSchedule::where('day', $request->day)->where('store_id', $request->store_id)
            ->where(function ($q) use ($request) {
                return $q->where(function ($query) use ($request) {
                    return $query->where('opening_time', '<=', $request->start_time)->where('closing_time', '>=', $request->start_time);
                })->orWhere(function ($query) use ($request) {
                    return $query->where('opening_time', '<=', $request->end_time)->where('closing_time', '>=', $request->end_time);
                });
            })
            ->first();

        if (isset($temp)) {
            return response()->json(['errors' => [
                ['code' => 'time', 'message' => translate('messages.schedule_overlapping_warning')],
            ]]);
        }

        $store = Store::find($request->store_id);
        $store_schedule = StoreLogic::insert_schedule($request->store_id, [$request->day], $request->start_time, $request->end_time.':59');

        return response()->json([
            'view' => view('admin-views.vendor.view.partials._schedule', compact('store'))->render(),
        ]);
    }

    public function remove_schedule($store_schedule)
    {
        $schedule = StoreSchedule::find($store_schedule);
        if (! $schedule) {
            return response()->json([], 404);
        }
        $store = $schedule->store;
        $schedule->delete();

        return response()->json([
            'view' => view('admin-views.vendor.view.partials._schedule', compact('store'))->render(),
        ]);
    }

    public function featured(Request $request)
    {
        $store = Store::findOrFail($request->store);
        $store->featured = $request->status;
        $store->save();
        Toastr::success(translate('messages.store_featured_status_updated'));

        return back();
    }

    public function conversation_list(Request $request)
    {

        $user = UserInfo::where('vendor_id', $request->user_id)->first();

        $conversations = Conversation::WhereUser($user->id);

        if ($request->query('key') != null) {
            $key = explode(' ', $request->get('key'));
            $conversations = $conversations->where(function ($qu) use ($key) {

                $qu->whereHas('sender', function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                    }
                })->orWhereHas('receiver', function ($query1) use ($key) {
                    foreach ($key as $value) {
                        $query1->where('f_name', 'like', "%{$value}%")->orWhere('l_name', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%");
                    }
                });
            });
        }

        $conversations = $conversations->paginate(8);

        $view = view('admin-views.vendor.view.partials._conversation_list', compact('conversations'))->render();

        return response()->json(['html' => $view]);
    }

    public function conversation_view($conversation_id, $user_id)
    {
        $convs = Message::where(['conversation_id' => $conversation_id])->get();
        $conversation = Conversation::find($conversation_id);
        $receiver = UserInfo::find($conversation->receiver_id);
        $sender = UserInfo::find($conversation->sender_id);
        $user = UserInfo::find($user_id);

        return response()->json([
            'view' => view('admin-views.vendor.view.partials._conversations', compact('convs', 'user', 'receiver'))->render(),
        ]);
    }

    public function cash_export(Request $request, $type, $store_id)
    {
        $store = Store::find($store_id);
        $account = AccountTransaction::where('from_type', 'store')->where('from_id', $store->id)->where('type', 'collected')->get();
        $data = [
            'data' => $account,
            'search' => $request['search'] ?? null,
            'is_provider' => $request['provider_id'] ?? null,
        ];
        if ($type == 'csv') {
            return Excel::download(new StoreCashTransactionExport($data), 'CashTransaction.csv');
        }

        return Excel::download(new StoreCashTransactionExport($data), 'CashTransaction.xlsx');
    }

    public function order_export(Request $request, $type, $store_id)
    {
        $store = Store::find($store_id);

        if ($request['provider_id']) {
            $fileName = 'Trip';
            $account = TripTransaction::where('provider_id', $store->vendor->id)->latest()->get();
        } else {
            $fileName = 'Order';
            $account = OrderTransaction::where('vendor_id', $store->vendor->id)->latest()->get();
        }

        $data = [
            'data' => $account,
            'search' => $request['search'] ?? null,
            'is_provider' => $request['provider_id'] ?? null,
        ];

        if ($type == 'csv') {
            return Excel::download(new StoreOrderTransactionExport($data), $fileName.'Transaction.csv');
        }

        return Excel::download(new StoreOrderTransactionExport($data), $fileName.'Transaction.xlsx');
    }

    public function withdraw_trans_export(Request $request, $type, $store_id)
    {
        $store = Store::find($store_id);
        $account = WithdrawRequest::where('vendor_id', $store->vendor->id)->get();

        $data = [
            'data' => $account,
            'search' => $request['search'] ?? null,
            'is_provider' => $request['provider_id'] ?? null,
        ];
        if ($type == 'csv') {
            return Excel::download(new StoreWiseWithdrawTransactionExport($data), 'WithdrawTransaction.csv');
        }

        return Excel::download(new StoreWiseWithdrawTransactionExport($data), 'WithdrawTransaction.xlsx');

    }

    public function store_wise_reviwe_export(Request $request)
    {
        $store = Store::where('id', $request->id)->first();
        $reviews = $store->reviews()->with('item', function ($query) {
            $query->withoutGlobalScope(\App\Scopes\StoreScope::class);
        })->latest()->get();
        $store_reviews = \App\CentralLogics\StoreLogic::calculate_store_rating($store['rating']);
        $data = [
            'store_name' => $store->name,
            'store_id' => $store->id,
            'rating' => $store_reviews['rating'],
            'total_reviews' => $store_reviews['total'],
            'data' => $reviews,
        ];
        if ($request->type == 'csv') {
            return Excel::download(new StoreWiseItemReviewExport($data), 'StoreWiseItemReview.csv');
        }

        return Excel::download(new StoreWiseItemReviewExport($data), 'StoreWiseItemReview.xlsx');
    }

    public function recommended_store()
    {
        $key = explode(' ', request()->search);
        $stores = Store::withcount(['orders', 'items'])->with('storeConfig')->where('module_id', Config::get('module.current_module_id'))
            ->wherehas('storeConfig', function ($q) {
                $q->where('is_recommended_deleted', 0);
            })
            ->when(isset($key), function ($q) use ($key) {
                $q->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('name', 'like', "%{$value}%");
                    }
                    $query->orWhereHas('translations', function ($query) use ($key) {
                        $query->where(function ($q) use ($key) {
                            foreach ($key as $value) {
                                $q->where('value', 'like', "%{$value}%");
                            }
                        });
                    });
                });
            })
            ->paginate(config('default_pagination'));

        $shuffle_recommended_store = DataSetting::where(['key' => 'shuffle_recommended_store', 'type' => Config::get('module.current_module_id')])?->first()?->value;

        return view('admin-views.vendor.recommended_store_list', compact('stores', 'shuffle_recommended_store'));
    }

    public function recommended_store_add(Request $request)
    {
        $request->validate([
            'selected_store_ids' => 'required',
        ], [
            'selected_store_ids.required' => translate('Please_select_a_store'),
        ]);
        $ids = explode(',', $request['selected_store_ids']);
        $ids = array_unique($ids);

        foreach ($ids as $id) {
            StoreConfig::updateOrInsert(['store_id' => $id], [
                'is_recommended' => 1,
                'is_recommended_deleted' => 0,
            ]);
        }
        Toastr::success(translate('messages.Recommended_Store_added_successfully'));

        return back();
    }

    public function recommended_store_remove($id)
    {
        StoreConfig::updateOrInsert(['store_id' => $id], [
            'is_recommended_deleted' => 1,
        ]);
        Toastr::success(translate('messages.store_is_removed_from_the_recommended_list'));

        return back();
    }

    public function recommended_store_status($id, $status)
    {
        StoreConfig::updateOrInsert(['store_id' => $id], [
            'is_recommended' => $status,
        ]);
        Toastr::success(translate('messages.store_recommendation_status_updated'));

        return back();
    }

    public function get_all_stores(Request $request)
    {
        $key = explode(' ', $request['name']);
        $stores = Store::withcount(['orders', 'items'])->where('module_id', Config::get('module.current_module_id'))
            ->where(function ($query) use ($key) {
                foreach ($key as $value) {
                    $query->where('name', 'like', "%{$value}%");
                }
                $query->orWhereHas('translations', function ($query) use ($key) {
                    $query->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->where('value', 'like', "%{$value}%");
                        }
                    });
                });
            })
            ->take(6)
            ->get()
            ->map(function ($stores) {
                $stores->ratings = StoreLogic::calculate_store_rating($stores['rating']);
                unset($stores['rating']);

                return $stores;
            });

        return response()->json([
            'result' => view('admin-views.vendor.partials._search_store', compact('stores'))->render(),
        ]);
    }

    public function selected_stores(Request $request)
    {
        $id = $request->id ?? [];
        $id = array_unique($id);

        $stores = Store::whereIn('id', $id)->where('module_id', Config::get('module.current_module_id'))
            ->get(['id', 'name', 'rating', 'logo'])
            ->map(function ($stores) {
                $stores->ratings = StoreLogic::calculate_store_rating($stores['rating']);
                unset($stores['rating']);

                return $stores;
            });

        return response()->json([
            'result' => view('admin-views.vendor.partials._selected_store', compact('stores'))->render(),
        ]);
    }

    public function shuffle_recommended_store($status)
    {
        // dd($status);
        $data = DataSetting::firstOrNew(
            ['key' => 'shuffle_recommended_store',
                'type' => Config::get('module.current_module_id')],
        );
        $data->value = $status == 1 ? 0 : 1;
        $data->save();

        Toastr::success(translate('messages.store_shuffle_status_updated'));

        return back();
    }

    public function get_store_ratings(Request $request)
    {

        $data = ['review' => 4.7, 'rating' => 2];

        if (! $request->store_id) {
            return response()->json($data);
        }

        $store = Store::where('id', $request->store_id)->first();
        if (! $store) {
            return response()->json($data);
        }
        $review = (int) $store->reviews_comments()->count();
        $reviewsInfo = $store->reviews()
            ->selectRaw('avg(reviews.rating) as average_rating, count(reviews.id) as total_reviews, items.store_id')
            ->groupBy('items.store_id')
            ->first();

        $rating = (float) $reviewsInfo?->average_rating ?? 0;

        $data = ['review' => round($review,1), 'rating' => round($rating,1)];

        return response()->json($data);
    }
}

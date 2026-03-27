      <div class="table-responsive datatable-custom">
          <table id="columnSearchDatatable"
              class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
              data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
              <thead class="thead-light">
                  <tr>
                      <th class="border-0">{{ translate('messages.SL') }}</th>
                      <th class="border-0">{{ translate('messages.zone_Id') }}</th>
                      <th class="border-0">{{ translate('messages.business_Zone_name') }}</th>
                      <th class="border-0">{{ translate('messages.vendors') }}</th>
                      <th class="border-0">{{ translate('messages.deliverymen') }}</th>
                        <th class="border-0 text-center">{{ translate('Default_Status') }}</th>
                      <th class="border-0">{{ translate('messages.status') }}</th>
                      <th class="border-0 text-center">{{ translate('messages.action') }}</th>
                  </tr>
              </thead>

              <tbody id="set-rows">
                  @php($non_mod = 0)
                  @foreach ($zones as $key => $zone)
                      @php($non_mod = count($zone->modules) > 0 && $non_mod == 0 ? $non_mod : $non_mod + 1)
                      <tr>
                          <td>{{ $key + $zones->firstItem() }}</td>
                          <td>{{ $zone->id }}</td>
                          <td>
                              <span class="d-block font-size-sm text-body">
                                  {{ $zone['name'] }}
                              </span>
                          </td>
                          <td>
                              {{ $zone->stores->filter(function ($store) {
                                      return $store->vendor && $store->vendor->status == 1;
                                  })->count() }}
                          </td>
                          <td>{{ $zone->deliverymen_count }}</td>
                        <td class="text-center">
                            @if ($zone->is_default)
                                <button
                                    class="btn btn-sm btn-ghost-success">{{ translate('messages.default_zone') }}
                                    <span data-toggle="tooltip" data-placement="right"
                                        data-original-title="{{ translate('This zone is set as the default for customers who visit the app or website without choosing a location.') }}"
                                        class="input-label-secondary text-success"><i class="tio-info text-success"></i></span>

                                </button>
                            @else

                              <form
                                  action="{{ route('admin.business-settings.zone.default-status',[$zone['id']]) }}"
                                  method="get" id="default-status-{{ $zone['id'] }}_form">
                              </form>

                                <button data-id="default-status-{{ $zone['id'] }}" data-type="status"
                                      data-image-off='{{ asset('/public/assets/admin/img/modal') }}/zone-status-on.png'
                                      data-title-off="{{ translate('Want_to_make_this_as_default_zone?') }}"
                                      data-text-off="<p>{{ translate('When enabled, customers who visit the app or website without choosing a location will be assigned to this zone.') }}</p>"
                                    id="default-status-{{ $zone['id'] }}"
                                class="btn border btn-soft-secondary dynamic-checkbox rounded py-2 px-3">{{ translate('messages.make_default') }}</button>
                            @endif
                        </td>

                          <td>
                              <label class="toggle-switch toggle-switch-sm" for="status-{{ $zone['id'] }}">
                                  <input type="checkbox" class="toggle-switch-input dynamic-checkbox"
                                      data-id="status-{{ $zone['id'] }}" data-type="status"
                                      data-image-on='{{ asset('/public/assets/admin/img/modal') }}/zone-status-on.png'
                                      data-image-off="{{ asset('/public/assets/admin/img/modal') }}/zone-status-off.png"
                                      data-title-on="{{ translate('Want_to_activate_this_Zone?') }}"
                                      data-title-off="{{ translate('Want_to_deactivate_this_Zone?') }}"
                                      data-text-on="<p>{{ translate('If_you_activate_this_zone,_Customers_can_see_all_stores_&_products_available_under_this_Zone_from_the_Customer_App_&_Website.') }}</p>"
                                      data-text-off="<p>{{ translate('If_you_deactivate_this_zone,_Customers_Will_NOT_see_all_stores_&_products_available_under_this_Zone_from_the_Customer_App_&_Website.') }}</p>"
                                      id="status-{{ $zone['id'] }}" {{ $zone->status ? 'checked' : '' }}>
                                  <span class="toggle-switch-label">
                                      <span class="toggle-switch-indicator"></span>
                                  </span>
                              </label>
                              <form
                                  action="{{ route('admin.business-settings.zone.status', [$zone['id'], $zone->status ? 0 : 1]) }}"
                                  method="get" id="status-{{ $zone['id'] }}_form">
                              </form>
                          </td>


                          <td>
                              <div class="btn--container justify-content-center">

                                  <div class="popover-wrapper {{ $non_mod == 1 ? 'active' : '' }}">

                                      <a href="{{ route('admin.business-settings.zone.module-setup', [$zone['id']]) }}"
                                          class="btn action-btn btn-outline-theme-dark " data-toggle="tooltip"
                                          data-placement="bottom"
                                          data-original-title="{{ translate('messages.connect_module') }}">
                                          <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                              xmlns="http://www.w3.org/2000/svg">
                                              <path
                                                  d="M3.08106 0.289917C1.6643 0.289917 0.5 1.45421 0.5 2.87097V4.45886C0.5 5.87562 1.66431 7.03992 3.08106 7.03992H4.66894C6.08569 7.03992 7.25 5.87562 7.25 4.45886V2.87097C7.25 1.45421 6.0857 0.289917 4.66894 0.289917H3.08106ZM3.08106 1.78992H4.66894C5.28064 1.78992 5.75 2.25927 5.75 2.87097V4.45886C5.75 5.07056 5.28064 5.53992 4.66894 5.53992H3.08106C2.46936 5.53992 2 5.07056 2 4.45886V2.87097C2 2.25927 2.46936 1.78992 3.08106 1.78992Z"
                                                  fill="#0177CD" />
                                              <path
                                                  d="M11.3311 8.53979C9.9143 8.53979 8.75 9.70409 8.75 11.1208V12.7087C8.75 14.1255 9.91431 15.2898 11.3311 15.2898H12.9189C14.3357 15.2898 15.5 14.1255 15.5 12.7087V11.1208C15.5 9.70409 14.3357 8.53979 12.9189 8.53979H11.3311ZM11.3311 10.0398H12.9189C13.5306 10.0398 14 10.5091 14 11.1208V12.7087C14 13.3204 13.5306 13.7898 12.9189 13.7898H11.3311C10.7194 13.7898 10.25 13.3204 10.25 12.7087V11.1208C10.25 10.5091 10.7194 10.0398 11.3311 10.0398Z"
                                                  fill="#0177CD" />
                                              <path
                                                  d="M3.08106 8.53979C1.6643 8.53979 0.5 9.70409 0.5 11.1208V12.7087C0.5 14.1255 1.66431 15.2898 3.08106 15.2898H4.66894C6.08569 15.2898 7.25 14.1255 7.25 12.7087V11.1208C7.25 9.70409 6.0857 8.53979 4.66894 8.53979H3.08106ZM3.08106 10.0398H4.66894C5.28064 10.0398 5.75 10.5091 5.75 11.1208V12.7087C5.75 13.3204 5.28064 13.7898 4.66894 13.7898H3.08106C2.46936 13.7898 2 13.3204 2 12.7087V11.1208C2 10.5091 2.46936 10.0398 3.08106 10.0398Z"
                                                  fill="#0177CD" />
                                              <path
                                                  d="M12.125 0.289551C11.9261 0.289551 11.7353 0.368579 11.5947 0.509232C11.454 0.649884 11.375 0.840638 11.375 1.03955V2.91455H9.5C9.30109 2.91455 9.11032 2.99358 8.96967 3.13423C8.82902 3.27488 8.75 3.46564 8.75 3.66455C8.75 3.86346 8.82902 4.05424 8.96967 4.19489C9.11032 4.33555 9.30109 4.41455 9.5 4.41455H11.375V6.28955C11.375 6.48846 11.454 6.67924 11.5947 6.81989C11.7353 6.96055 11.9261 7.03955 12.125 7.03955C12.3239 7.03955 12.5147 6.96055 12.6553 6.81989C12.796 6.67924 12.875 6.48846 12.875 6.28955V4.41455H14.75C14.9489 4.41455 15.1397 4.33555 15.2803 4.19489C15.421 4.05424 15.5 3.86346 15.5 3.66455C15.5 3.46564 15.421 3.27488 15.2803 3.13423C15.1397 2.99358 14.9489 2.91455 14.75 2.91455H12.875V1.03955C12.875 0.840638 12.796 0.649884 12.6553 0.509232C12.5147 0.368579 12.3239 0.289551 12.125 0.289551Z"
                                                  fill="#0177CD" />
                                          </svg>
                                      </a>
                                      @if ($non_mod == 1)
                                      <div class="popover __popover">
                                          <div class="arrow"></div>
                                          <h3 class="popover-header d-flex justify-content-between">
                                              <span>{{ translate('messages.Important!') }}</span>
                                          </h3>
                                          <div class="popover-body">
                                              {{ translate('The_Business_Zone_will_NOT_work_if_you_don’t_select_your_business_module_&_payment_method.') }}
                                          </div>
                                      </div>
                                      @endif
                                  </div>

                                  <a href="{{ route('admin.business-settings.zone.surge-price.list', [$zone['id']]) }}"
                                      class="btn action-btn btn-outline-theme-light" data-toggle="tooltip"
                                      data-placement="bottom"
                                      data-original-title="{{ translate('messages.surge_price') }}">
                                      <svg width="18" height="16" viewBox="0 0 18 16" fill="none"
                                          xmlns="http://www.w3.org/2000/svg">
                                          <path
                                              d="M9.625 5.91455C9.79076 5.91455 9.94973 5.8487 10.0669 5.73149C10.1842 5.61428 10.25 5.45531 10.25 5.28955C10.25 5.12379 10.1842 4.96482 10.0669 4.84761C9.94973 4.7304 9.79076 4.66455 9.625 4.66455H8.375V4.03955C8.375 3.87379 8.30915 3.71482 8.19194 3.59761C8.07473 3.4804 7.91576 3.41455 7.75 3.41455C7.58424 3.41455 7.42527 3.4804 7.30806 3.59761C7.19085 3.71482 7.125 3.87379 7.125 4.03955V4.66455H6.5C6.16848 4.66455 5.85054 4.79625 5.61612 5.03067C5.3817 5.26509 5.25 5.58303 5.25 5.91455V7.16455C5.25 7.49607 5.3817 7.81401 5.61612 8.04843C5.85054 8.28285 6.16848 8.41455 6.5 8.41455H9V9.66455H5.875C5.70924 9.66455 5.55027 9.7304 5.43306 9.84761C5.31585 9.96482 5.25 10.1238 5.25 10.2896C5.25 10.4553 5.31585 10.6143 5.43306 10.7315C5.55027 10.8487 5.70924 10.9146 5.875 10.9146H7.125V11.5396C7.125 11.7053 7.19085 11.8643 7.30806 11.9815C7.42527 12.0987 7.58424 12.1646 7.75 12.1646C7.91576 12.1646 8.07473 12.0987 8.19194 11.9815C8.30915 11.8643 8.375 11.7053 8.375 11.5396V10.9146H9C9.33152 10.9146 9.64946 10.7829 9.88388 10.5484C10.1183 10.314 10.25 9.99607 10.25 9.66455V8.41455C10.25 8.08303 10.1183 7.76509 9.88388 7.53067C9.64946 7.29625 9.33152 7.16455 9 7.16455H6.5V5.91455H9.625Z"
                                              fill="#47A7FF" />
                                          <path
                                              d="M10.5875 13.358C9.7098 13.8088 8.73674 14.0424 7.75004 14.0393C6.61369 14.0395 5.49877 13.73 4.52522 13.1439C3.55167 12.5578 2.75632 11.7174 2.22472 10.7131C1.69313 9.70877 1.44541 8.5785 1.50821 7.44389C1.57101 6.30928 1.94195 5.21326 2.58114 4.27372C3.22033 3.33419 4.10357 2.58669 5.13585 2.11164C6.16814 1.63659 7.31041 1.45197 8.43979 1.57763C9.56917 1.70328 10.6429 2.13447 11.5455 2.8248C12.4482 3.51514 13.1455 4.43849 13.5625 5.49555C13.5929 5.57188 13.638 5.64148 13.6953 5.70038C13.7525 5.75928 13.8209 5.80632 13.8963 5.83882C13.9717 5.87132 14.0529 5.88864 14.135 5.8898C14.2171 5.89095 14.2987 5.87591 14.375 5.84555C14.4514 5.81518 14.521 5.77007 14.5799 5.7128C14.6388 5.65554 14.6858 5.58723 14.7183 5.51178C14.7508 5.43634 14.7681 5.35523 14.7693 5.27308C14.7704 5.19094 14.7554 5.10938 14.725 5.03305C14.1682 3.62524 13.1984 2.41891 11.9431 1.57266C10.6878 0.726404 9.20577 0.279858 7.6919 0.291735C6.17803 0.303611 4.70319 0.773354 3.46132 1.6392C2.21944 2.50504 1.26866 3.72644 0.734011 5.14281C0.199357 6.55917 0.105855 8.10418 0.4658 9.57469C0.825745 11.0452 1.62228 12.3723 2.75068 13.3816C3.87908 14.3909 5.2865 15.0351 6.78789 15.2294C8.28928 15.4238 9.81434 15.1592 11.1625 14.4705C11.2356 14.4328 11.3005 14.381 11.3535 14.3182C11.4066 14.2553 11.4467 14.1827 11.4717 14.1043C11.4966 14.026 11.5059 13.9435 11.499 13.8615C11.492 13.7796 11.469 13.6998 11.4313 13.6268C11.3935 13.5537 11.3418 13.4889 11.2789 13.4358C11.2161 13.3828 11.1434 13.3426 11.0651 13.3177C10.9867 13.2927 10.9042 13.2834 10.8223 13.2904C10.7404 13.2973 10.6606 13.3203 10.5875 13.358Z"
                                              fill="#47A7FF" />
                                          <path
                                              d="M17.6125 10.5264L15.1125 7.40137C15.054 7.32837 14.9797 7.26946 14.8954 7.22898C14.811 7.1885 14.7186 7.16748 14.625 7.16748C14.5314 7.16748 14.439 7.1885 14.3547 7.22898C14.2703 7.26946 14.1961 7.32837 14.1375 7.40137L11.6375 10.5264C11.565 10.6178 11.5195 10.7278 11.5062 10.8438C11.493 10.9598 11.5125 11.0771 11.5625 11.1826C11.6132 11.289 11.6929 11.3789 11.7924 11.4419C11.8919 11.5049 12.0072 11.5385 12.125 11.5389H12.75V14.0389C12.75 14.3704 12.8817 14.6883 13.1161 14.9228C13.3506 15.1572 13.6685 15.2889 14 15.2889H15.25C15.5815 15.2889 15.8995 15.1572 16.1339 14.9228C16.3683 14.6883 16.5 14.3704 16.5 14.0389V11.5389H17.125C17.2428 11.5385 17.3581 11.5049 17.4577 11.4419C17.5572 11.3789 17.6369 11.289 17.6875 11.1826C17.7375 11.0771 17.757 10.9598 17.7438 10.8438C17.7305 10.7278 17.6851 10.6178 17.6125 10.5264ZM15.25 10.9139V14.0389H14V10.9139C14.0005 10.7564 13.9416 10.6046 13.835 10.4887C13.7284 10.3728 13.582 10.3015 13.425 10.2889L14.625 8.78887L15.825 10.2889C15.6681 10.3015 15.5217 10.3728 15.4151 10.4887C15.3085 10.6046 15.2495 10.7564 15.25 10.9139Z"
                                              fill="#47A7FF" />
                                      </svg>
                                  </a>
                                  <div class="btn-group">
                                      <button type="button"
                                          class="btn action-btn btn--primary btn-outline-primary rounded"
                                          data-toggle="dropdown" aria-expanded="false">
                                          <i class="tio-more-vertical" data-toggle="tooltip" data-placement="bottom"
                                              data-original-title="{{ translate('messages.menu') }}"></i>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-right">
                                          <a href="{{ route('admin.business-settings.zone.edit', [$zone['id']]) }}"
                                              title="{{ translate('messages.edit_zone') }}"
                                              class="dropdown-item  px-3 border-bottom fs-14 d-flex align-items-center gap-1 justify-content-between">
                                              {{ translate('Edit') }} <i class="tio-edit theme-clr-dark"></i>
                                          </a>
                                          <a href="javascript:" data-id="zone-{{ $zone['id'] }}"
                                              data-title="{{ translate('Want_to_Delete_this_Zone?') }}"
                                              data-message="{{ translate('If_yes,_all_its_modules,_stores,_and_products_will_be_DELETED_FOREVER.') }}"
                                              title="{{ translate('messages.delete_zone') }}"
                                              class="dropdown-item px-3 border-bottom fs-14 d-flex align-items-center gap-1 justify-content-between status_form_alert">
                                              {{ translate('Delete') }} <i class="tio-delete-outlined text-danger"></i>
                                          </a>
                                          @if ($digital_payment && $digital_payment['status'] == 1)
                                              <div
                                                  class="dropdown-item px-3 border-bottom fs-14 d-flex align-items-center gap-1 justify-content-between">
                                                  {{ translate('Digital Payment') }}
                                                  <label class="toggle-switch toggle-switch-sm"
                                                      for="digital_paymentCheckbox{{ $zone->id }}">
                                                      <input type="checkbox"
                                                          data-id="digital_payment-{{ $zone['id'] }}"
                                                          data-title="{{ $zone->digital_payment ? translate('Want_to_disable_‘Digital_Payment’?') : translate('Want_to_enable_‘Digital_Payment’?') }}"
                                                          data-message="{{ $zone->digital_payment ? translate('If_yes,_the_digital_payment_option_will_be_hidden_during_checkout.') : translate('If_yes,_Customers_can_choose_the_‘Digital_Payment’_option_during_checkout.') }}"
                                                          class="toggle-switch-input status_form_alert"
                                                          id="digital_paymentCheckbox{{ $zone->id }}"
                                                          {{ $zone->digital_payment ? 'checked' : '' }}>
                                                      <span class="toggle-switch-label">
                                                          <span class="toggle-switch-indicator"></span>
                                                      </span>
                                                  </label>
                                                  <form
                                                      action="{{ route('admin.business-settings.zone.digital-payment', [$zone['id'], $zone->digital_payment ? 0 : 1]) }}"
                                                      method="get" id="digital_payment-{{ $zone['id'] }}">
                                                  </form>
                                              </div>
                                          @endif
                                          @if ($offline_payment && $offline_payment == 1)
                                              <div
                                                  class="dropdown-item px-3 border-bottom fs-14 d-flex align-items-center gap-1 justify-content-between">
                                                  {{ translate('Offline Payment') }}
                                                  <label class="toggle-switch toggle-switch-sm"
                                                      for="offline_paymentCheckbox{{ $zone->id }}">
                                                      <input type="checkbox"
                                                          data-id="offline_payment-{{ $zone['id'] }}"
                                                          data-title="{{ $zone->offline_payment ? translate('Want_to_disable_‘offline_Payment’?') : translate('Want_to_enable_‘offline_Payment’?') }}"
                                                          data-message="{{ $zone->offline_payment ? translate('If_yes,_the_offline_payment_option_will_be_hidden_during_checkout.') : translate('If_yes,_Customers_can_choose_the_‘offline_Payment’_option_during_checkout.') }}"
                                                          class="toggle-switch-input status_form_alert"
                                                          id="offline_paymentCheckbox{{ $zone->id }}"
                                                          {{ $zone->offline_payment ? 'checked' : '' }}>
                                                      <span class="toggle-switch-label">
                                                          <span class="toggle-switch-indicator"></span>
                                                      </span>
                                                  </label>
                                                  <form
                                                      action="{{ route('admin.business-settings.zone.offline-payment', [$zone['id'], $zone->offline_payment ? 0 : 1]) }}"
                                                      method="get" id="offline_payment-{{ $zone['id'] }}">
                                                  </form>
                                              </div>
                                          @endif
                                          @if ($config && $config['status'] == 1)
                                              <div
                                                  class="dropdown-item px-3 fs-14 d-flex align-items-center gap-1 justify-content-between">
                                                  {{ translate('Cash On Delivery') }}
                                                  <label class="toggle-switch toggle-switch-sm"
                                                      for="cashOnDeliveryCheckbox{{ $zone->id }}">
                                                      <input type="checkbox"
                                                          data-id="cash_on_delivery-{{ $zone['id'] }}"
                                                          data-title="{{ $zone->cash_on_delivery ? translate('Want_to_disable_‘Cash_On_Delivery’?') : translate('Want_to_enable_‘Cash_On_Delivery’?') }}"
                                                          data-message="{{ $zone->cash_on_delivery ? translate('If_yes,_the_Cash_on_Delivery_option_will_be_hidden_during_checkout.') : translate('If_yes,_Customers_can_choose_the_‘Cash_On_Delivery’_option_during_checkout.') }}"
                                                          class="toggle-switch-input status_form_alert"
                                                          id="cashOnDeliveryCheckbox{{ $zone->id }}"
                                                          {{ $zone->cash_on_delivery ? 'checked' : '' }}>
                                                      <span class="toggle-switch-label">
                                                          <span class="toggle-switch-indicator"></span>
                                                      </span>
                                                  </label>
                                                  <form
                                                      action="{{ route('admin.business-settings.zone.cash-on-delivery', [$zone['id'], $zone->cash_on_delivery ? 0 : 1]) }}"
                                                      method="get" id="cash_on_delivery-{{ $zone['id'] }}">
                                                  </form>
                                              </div>
                                          @endif
                                      </div>
                                  </div>
                                  <form action="{{ route('admin.business-settings.zone.delete', [$zone['id']]) }}"
                                      method="post" id="zone-{{ $zone['id'] }}">
                                      @csrf @method('delete')
                                  </form>
                              </div>
                          </td>
                      </tr>
                  @endforeach
              </tbody>
          </table>
      </div>
      @if (count($zones) !== 0)
          <hr>
      @endif
      <div class="page-area">
          {!! $zones->withQueryString()->links() !!}
      </div>
      @if (count($zones) === 0)
          <div class="empty--data">
              <img src="{{ asset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
              <h5>
                  {{ translate('no_data_found') }}
              </h5>
          </div>
      @endif

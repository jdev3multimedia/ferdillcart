@extends('layouts.admin.app')

@section('title',translate('messages.notification'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-20 pb-0">
            <h1 class="font-bold mb-0">
                {{translate('messages.Send_Notification')}}
            </h1>
        </div>
        <!-- Page Header -->

        <div class="fs-12 px-3 py-2 rounded bg-warning bg-opacity-10 text-title d-flex gap-2 align-items-baseline mb-20">
            <span class="text-warning lh-1 fs-14">
                <i class="tio-info"></i>
            </span>
            <span class="fs-12 mb-0">
                {{ translate('messages.Setup Push Notification Messages for customer. Must setup') }}
                <a target="_blank" href="{{ route('admin.business-settings.fcm-config') }}" class="font-semibold text-info">{{ translate('messages.Firebase Configuration') }} </a>
                {{ lcfirst(translate('messages.page to work notifications.')) }}
            </span>
        </div>

        <div class="card card-body mb-20">
            <div class="mb-20">
                <h3 class="mb-1">
                    {{ translate('messages.Send Notification') }}
                </h3>
                <p class="fs-12 mb-0">
                    {{ translate('messages.Configure settings to send push notifications to targeted users in specific zones.') }}
                </p>
            </div>
            <form action="{{route('admin.notification.store')}}" method="post" enctype="multipart/form-data" id="notification">
                @csrf
                <div class="row g-3 mb-20">
                    <div class="col-lg-8">
                        <div class="__bg-F8F9FC-card h-100">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-capitalize" for="exampleFormControlInput1">{{translate('messages.zones')}}</label>
                                        <select name="zone" id="zone" class="form-control js-select2-custom" >
                                            <option value="all">{{translate('messages.all')}}</option>
                                            @foreach($zones as $zone)
                                                <option value="{{$zone['id']}}">{{$zone['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-capitalize" for="tergat">{{translate('messages.Targeted user')}}</label>

                                        <select name="tergat" class="form-control custom-select" id="tergat" data-placeholder="{{translate('messages.select_tergat')}}" required>
                                            <option value="customer">{{translate('messages.customer')}}</option>
                                            <option value="deliveryman">{{translate('messages.deliveryman')}}</option>
                                            <option value="store">{{translate('messages.store')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-capitalize d-flex gap-1 align-items-center" for="exampleFormControlInput1">
                                            {{translate('messages.title')}}
                                            <span data-toggle="tooltip" data-title="{{ translate('messages.enter_notification_title') }}">
                                                <i class="tio-info text-light-gray"></i>
                                            </span>
                                        </label>
                                        <textarea name="notification_title" class="form-control" maxlength="100" rows="1" placeholder="{{ translate('messages.Type_Title') }}" required></textarea>
                                        <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label text-capitalize d-flex gap-1 align-items-center" for="exampleFormControlInput1">
                                            {{translate('messages.description')}}
                                            <span data-toggle="tooltip" data-title="{{ translate('messages.enter_notification_description') }}">
                                                <i class="tio-info text-light-gray"></i>
                                            </span>
                                        </label>
                                        <textarea name="description" class="form-control" maxlength="200" rows="1" placeholder="{{ translate('messages.Type about the description') }}" required></textarea>
                                         <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="__bg-F8F9FC-card h-100">
                                <div class="mb-20">
                                    <h5 class="mb-1">{{ translate('messages.Image') }} </h5>
                                    <p class="mb-0 fs-12">
                                        {{ translate('messages.Upload your cover Image') }}
                                    </p>
                                </div>
                                @include('admin-views.partials._image-uploader', [
                                    'id' => 'image-input',
                                    'name' => 'image',
                                    'ratio' => '2:1',
                                    'isRequired' => false,
                                    'existingImage' => null,
                                    'imageExtension' => IMAGE_EXTENSION,
                                    'imageFormat' => IMAGE_FORMAT,
                                    'maxSize' => MAX_FILE_SIZE,
                                    'textPosition' => 'bottom',
                                    ])
                        </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end">
                    <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.Reset')}}</button>
                    <button type="submit" id="submit" class="btn btn--primary">{{translate('messages.Save_&_Send')}}</button>
                </div>
            </form>
        </div>

        <div class="card card-body">
            <div class="d-flex flex-wrap justify-content-lg-end align-items-center gap-3 mb-20">
                <h4 class="mb-0 flex-grow-1">{{ translate('Notification History') }}<span class="badge badge-soft-dark ml-2">{{$notifications->total()}}</span></h4>

                <select name="target" class="form-control custom-select max-w-200px min-w-100-mobile" id="filter_form" data-placeholder="{{translate('messages.select_target')}}">
                    <option value="all" {{ $target == 'all' ? 'selected' : '' }}>{{translate('messages.all')}}</option>
                    <option value="customer" {{ $target == 'customer' ? 'selected' : '' }}>{{translate('messages.customer')}}</option>
                    <option value="deliveryman" {{ $target == 'deliveryman' ? 'selected' : '' }}>{{translate('messages.deliveryman')}}</option>
                    <option value="store" {{ $target == 'store' ? 'selected' : '' }}>{{translate('messages.store')}}</option>
                </select>

                <form class="search-form flex-grow-1 flex-lg-grow-0" >
                    <!-- Search -->
                    <div class="input-group input--group min--270">
                        <input type="search" name="search"  class="form-control h--45px"
                        value="{{ request()?->search ?? null }}"  placeholder="{{translate('messages.search_notification')}}">
                        <button type="submit" class="btn btn--secondary h--45px">
                        <i class="tio-search"></i>
                        </button>
                    </div>
                    <!-- End Search -->
                </form>
                @if(request()->get('search'))
                <button type="reset" class="btn btn--primary ml-2 location-reload-to-base h--45px" data-url="{{url()->full()}}">{{translate('messages.reset')}}</button>
                @endif


                <!-- Unfold -->
                <div class="hs-unfold mr-2">
                    <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle min-height-40 h--45px" href="javascript:;"
                        data-hs-unfold-options='{
                                "target": "#usersExportDropdown",
                                "type": "css-animation"
                            }'>
                        <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                    </a>

                    <div id="usersExportDropdown"
                        class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">

                        <span class="dropdown-header">{{ translate('messages.download_options') }}</span>


                        <a id="export-excel" class="dropdown-item" href="{{route('admin.notification.export', ['type'=>'excel' , request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/excel.svg"
                                alt="Image Description">
                            {{ translate('messages.excel') }}
                        </a>
                        <a id="export-csv" class="dropdown-item" href="{{route('admin.notification.export', ['type'=>'csv', request()->getQueryString()])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"
                                alt="Image Description">
                            .{{ translate('messages.csv') }}
                        </a>

                    </div>
                </div>
                <!-- End Unfold -->
            </div>
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable"
                       class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                       data-hs-datatables-options='{
                         "order": [],
                         "orderCellsTop": true,
                         "paging": false
                       }'>
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{ translate('messages.SL') }}</th>
                            <th class="border-0">{{translate('messages.image')}}</th>
                            <th class="border-0">{{translate('messages.title')}}</th>
                            <th class="border-0">{{translate('messages.description')}}</th>
                            <th class="border-0">{{translate('messages.zones')}}</th>
                            <th class="text-center border-0">{{translate('messages.status')}}</th>
                            <th class="text-center border-0">{{translate('messages.action')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($notifications as $key=>$notification)
                        <tr>
                            <td>{{$key+$notifications->firstItem()}}</td>
                            <td>
                                @if($notification['image']!=null)
                                    <img width="60" height="30" class="w-60px object--cover onerror-image"
                                    src="{{ $notification['image_full_url'] }}"
                                        data-onerror-image="{{asset('public/assets/admin/img/160x160/img2.jpg')}}">
                                @else
                                    <label class="badge badge-soft-warning mb-0">{{translate('No Image')}}</label>
                                @endif
                            </td>
                            <td>
                            <span data-toggle="tooltip" data-title="{{ $notification['title'] }}" class="d-block font-size-sm text-body">
                                {{substr($notification['title'],0,25)}} {{strlen($notification['title'])>25?'...':''}}
                            </span>
                            </td>
                            <td>
                                {{-- {{substr($notification['description'],0,25)}} {{strlen($notification['description'])>25?'...':''}} --}}
                                <span class="max-w-280 line--limit-2" data-toggle="tooltip" data-title="{{ $notification['description'] }}">{{ $notification['description'] }}</span>
                            </td>
                            <td>
                                {{$notification->zone_id==null?translate('messages.all'):($notification->zone?$notification->zone->name:translate('messages.zone_deleted'))}}
                            </td>
                            <td>
                                <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$notification->id}}">
                                    <input type="checkbox" data-url="{{route('admin.notification.status',[$notification['id'],$notification->status?0:1])}}" class="toggle-switch-input redirect-url" id="stocksCheckbox{{$notification->id}}" {{$notification->status?'checked':''}} hidden>
                                    <span class="toggle-switch-label mx-auto">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <button type="button" class="btn action-btn btn--primary btn-outline-primary" data-toggle="modal"
                                     data-target="#notification-view-modal" title="{{translate('messages.view_notification')}}"
                                     data-title="{{$notification['title']}}"
                                     data-description="{{$notification['description']}}"
                                     data-image="{{$notification['image_full_url']}}"
                                     data-zone="{{$notification->zone_id==null?translate('messages.all'):($notification->zone?$notification->zone->name:translate('messages.zone_deleted'))}}"
                                     data-tergat="{{$notification['tergat']}}"
                                     ><i class="tio-invisible"></i>
                                    </button>
                                    <button type="button" class="btn action-btn btn--primary btn-outline-primary offcanvas-trigger edit-btn"
                                     data-target="#notification-update-offcanvas" title="{{translate('messages.edit_notification')}}"
                                     data-id="{{$notification['id']}}"
                                     data-title="{{$notification['title']}}"
                                     data-description="{{$notification['description']}}"
                                     data-image="{{$notification['image_full_url']}}"
                                     data-zone="{{$notification->zone_id}}"
                                     data-tergat="{{$notification['tergat']}}"
                                     ><i class="tio-edit"></i>
                                    </button>
                                    <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                       data-id="notification-{{$notification['id']}}" data-message="{{ translate('Want to delete this notification ?') }}" title="{{translate('messages.delete_notification')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.notification.delete',[$notification['id']])}}" method="post" id="notification-{{$notification['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($notifications) !== 0)
            <hr>
            @endif
            <div class="page-area">
                {!! $notifications->links() !!}
            </div>
            @if(count($notifications) === 0)
            <div class="empty--data">
                <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
            <!-- End Table -->
        </div>

    </div>

    {{-- Notification Update Offcanvas --}}
    <div id="notification-update-offcanvas" class="custom-offcanvas d-flex flex-column justify-content-between">
        <form action="" method="post" enctype="multipart/form-data" id="update-notification-form">
                @csrf
            <div>
                <div class="custom-offcanvas-header bg--secondary d-flex justify-content-end align-items-center  gap-3 px-3 py-3">
                    <div class="py-1 flex-grow-1">
                        <h3 class="mb-0">{{ translate('messages.Edit Send Notification') }}</h3>
                    </div>
                    <button type="submit" class="btn btn--primary btn-outline-primary btn-sm px-3 d-flex gap-2 align-items-center offcanvas-close">
                        <i class="tio-redo"></i> {{translate('resend')}}
                    </button>
                    <button type="button" class="btn-close w-25px h-25px border bg-white rounded-circle d-center text-dark flex-shrink-0 fz-15px p-0 offcanvas-close" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="custom-offcanvas-body custom-offcanvas-body-100  p-20">
                    <div class="mb-9">
                        <div class="__bg-F8F9FC-card mb-20">
                            <div class="mb-20">
                                <h5 class="mb-1">{{ translate('messages.Image') }} </h5>
                                <p class="mb-0 fs-12">
                                    {{ translate('messages.Upload your cover Image') }}
                                </p>
                            </div>
                            @include('admin-views.partials._image-uploader', [
                                'id' => 'image-input-u',
                                'name' => 'image',
                                'ratio' => '2:1',
                                'isRequired' => false,
                                'existingImage' => '',
                                'imageExtension' => IMAGE_EXTENSION,
                                'imageFormat' => IMAGE_FORMAT,
                                'maxSize' => MAX_FILE_SIZE,
                                'textPosition' => 'none',
                                ])

                        </div>
                        <div class="__bg-F8F9FC-card mb-20">
                            <div class="form-group mb-3">
                                <label class="input-label text-capitalize d-flex gap-1 align-items-center" for="exampleFormControlInput1">
                                    {{translate('messages.title')}}
                                    <span data-toggle="tooltip" data-title="{{ translate('messages.enter_notification_title') }}">
                                        <i class="tio-info text-light-gray"></i>
                                    </span>
                                </label>
                                    </span>
                                </label>
                                <textarea name="notification_title" id="notification_title_u" class="form-control" maxlength="100" rows="1" placeholder="{{ translate('messages.Type_Title') }}" required></textarea>
                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/100</span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="input-label text-capitalize d-flex gap-1 align-items-center" for="exampleFormControlInput1">
                                    {{translate('messages.description')}}
                                    <span data-toggle="tooltip" data-title="{{ translate('messages.enter_notification_description') }}">
                                        <i class="tio-info text-light-gray"></i>
                                    </span>
                                </label>
                                    </span>
                                </label>
                                <textarea name="description" id="description_u" class="form-control" maxlength="200" rows="3" placeholder="{{ translate('messages.Type about the description') }}" required></textarea>
                                <span class="text-right text-counting color-A7A7A7 d-block mt-1">0/200</span>
                            </div>
                            <div class="form-group mb-3">
                                <label class="input-label text-capitalize" for="exampleFormControlInput1">{{translate('messages.zones')}}</label>
                                <select name="zone" id="zone_u" class="form-control custom-select js-select2-custom" >
                                    <option value="all">{{translate('messages.all_zone')}}</option>
                                    @foreach($zones as $zone)
                                        <option value="{{$zone['id']}}">{{$zone['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label class="input-label text-capitalize" for="tergat">{{translate('messages.Targeted_user')}}</label>

                                <select name="tergat" class="form-control custom-select" id="tergat_u" data-placeholder="{{translate('messages.select_tergat')}}" required>
                                    <option value="customer">{{translate('messages.customer')}}</option>
                                    <option value="deliveryman">{{translate('messages.deliveryman')}}</option>
                                    <option value="store">{{translate('messages.store')}}</option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="offcanvas-footer d-flex gap-3 justify-content-center align-items-center bg-white bottom-0 mt-auto p-3">
                    <button type="reset" id="reset_btn" class="btn btn--reset w-100">{{translate('reset')}}</button>
                    <button type="submit" class="btn btn--primary w-100">
                        {{translate('update')}}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>

    {{-- Notification Short View Modal --}}
    <form action="">
        <div class="modal fade" id="notification-view-modal">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header px-2 pt-2">
                        <h3 class="mb-0 px-2">{{ translate('messages.Send Notification Short view') }}</h3>
                        <button type="button" class="close btn btn--reset btn-circle" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear fs-20 opacity-70"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="text-center mb-3">
                                <img class="img--vertical rounded aspect-2-1 max-w-300px onerror-image"
                                    src="" id="modal-image"
                                    data-onerror-image="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="image"/>
                            </div>
                            <div class="card card-body mb-3">
                                <div class="mb-20">
                                    <h5 class="mb-1">{{ translate('messages.Tittle') }}</h5>
                                    <p class="fs-12 mb-0" id="modal-title"></p>
                                </div>
                                <div>
                                    <h5 class="mb-1">{{ translate('messages.Description') }}</h5>
                                    <p class="fs-12 mb-0" id="modal-description"></p>
                                </div>
                            </div>
                            <div class="card card-body">
                                <div class="row g-3">
                                    <div class="col-sm-5">
                                        <div>
                                            <h5 class="mb-1">{{ translate('messages.Zones') }}</h5>
                                            <p class="fs-12 mb-0" id="modal-zone"></p>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 d-none d-sm-block">
                                        <div class="h-100 w-1px bg-soft-dark mx-auto"></div>
                                    </div>
                                    <div class="col-sm-5">
                                        <div>
                                            <h5 class="mb-1">{{ translate('messages.Targeted_user') }}</h5>
                                            <p class="fs-12 mb-0" id="modal-tergat"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 shadow">
                        <div class="btn--container justify-content-end">
                            <button data-dismiss="modal"
                                class="btn btn--reset min-w-120">{{translate("Close")}}</button>
                            {{-- <button data-dismiss="modal" type="button"
                                class="btn btn--primary d-flex gap-2 align-items-center min-w-120"><i class="tio-redo"></i> {{translate('Resend')}}</button> --}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/notification.js"></script>
    <script>
        "use strict";
        $('#notification').on('submit', function (e) {

            e.preventDefault();
            var formData = new FormData(this);

            Swal.fire({
                title: '{{translate('messages.are_you_sure')}}',
                text: '{{translate('messages.you want to sent notification to ')}}'+$('#tergat').val()+'?',
                imageUrl: '{{ asset('public/assets/admin/img/off-danger.png') }}',
                imageWidth: 80,
                imageHeight: 80,
                imageAlt: 'Custom icon',
                showCancelButton: true,
                showCloseButton: true,
                closeButtonHtml: '×',
                cancelButtonColor: 'default',
                confirmButtonColor: 'primary',
                cancelButtonText: '{{translate('messages.no')}}',
                confirmButtonText: '{{translate('messages.send')}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.post({
                        url: '{{route('admin.notification.store')}}',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data.errors) {
                                for (var i = 0; i < data.errors.length; i++) {
                                    toastr.error(data.errors[i].message, {
                                        CloseButton: true,
                                        ProgressBar: true
                                    });
                                }
                            } else {
                                toastr.success('Notifiction sent successfully!', {
                                    CloseButton: true,
                                    ProgressBar: true
                                });
                                setTimeout(function () {
                                    location.href = '{{route('admin.notification.add-new')}}';
                                }, 2000);
                            }
                        }
                    });
                }
            })
        })

            $('#reset_btn').click(function(){
                $('#zone').val('all').trigger('change');
                $('#viewer').attr('src','{{asset('public/assets/admin/img/900x400/img1.jpg')}}');
                $('#customFileEg1').val(null);
            })

            $('#filter_form').on('change', function () {
                let target = $(this).val();
                let url = '{{route('admin.notification.add-new')}}?target=' + target;
                window.location.href = url;
            });

            $('#notification-update-offcanvas').on('input', '.form-control', function () {
                var $this = $(this);
                var $counter = $this.closest('.form-group').find('.text-counting');
                if ($counter.length) {
                    var count = $this.val().length;
                    var max = $this.attr('maxlength');
                    $counter.text(count + '/' + max);
                }
            });

            $('#notification-view-modal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var title = button.data('title');
                var description = button.data('description');
                var image = button.data('image');
                var zone = button.data('zone');
                var tergat = button.data('tergat');

                var modal = $(this);
                modal.find('#modal-title').text(title);
                modal.find('#modal-description').text(description);
                modal.find('#modal-zone').text(zone);
                modal.find('#modal-tergat').text(tergat);
                modal.find('#modal-image').attr('src', image);
            });

            $('.edit-btn').on('click', function () {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var description = $(this).data('description');
                var image = $(this).data('image');
                var zone = $(this).data('zone');
                var tergat = $(this).data('tergat');

                $('#notification_title_u').val(title).trigger('input');
                $('#description_u').val(description).trigger('input');
                $('#zone_u').val(zone ? zone : 'all').trigger('change');
                $('#tergat_u').val(tergat);

                var $container = $('#image-input-u').closest('.upload-file_custom');
                var $overlay = $container.find('.overlay');

                if(image){
                    $container.find('.upload-file-img').attr('src', image).show();
                    $container.find('.upload-file-textbox').hide();
                    $container.addClass('input-disabled');
                    $overlay.addClass('show');
                    $container.find('.remove_btn').css('opacity', 1);
                } else {
                    $container.find('.upload-file-img').hide().attr('src', '');
                    $container.find('.upload-file-textbox').show();
                    $container.removeClass('input-disabled');
                    $overlay.removeClass('show');
                    $container.find('.remove_btn').css('opacity', 0);
                }

                let action = '{{route('admin.notification.update', 'temp_id')}}';
                action = action.replace('temp_id', id);
                $('#update-notification-form').attr('action', action);
            });
        </script>
@endpush

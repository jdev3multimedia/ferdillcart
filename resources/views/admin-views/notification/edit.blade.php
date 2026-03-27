@extends('layouts.admin.app')

@section('title',translate('messages.update_notification'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/notification.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.notification_update')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.notification.update',[$notification['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="row g-2">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.title')}}</label>
                                        <input type="text" value="{{$notification['title']}}" name="notification_title" class="form-control" placeholder="{{translate('messages.new_notification')}}" required maxlength="191">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.zone')}}</label>
                                        <select name="zone" id="zone" class="form-control js-select2-custom" >
                                            <option value="all" {{isset($notification->zone_id)?'':'selected'}}>{{translate('messages.all_zone')}}</option>
                                            @foreach($zones as $zone)
                                                <option value="{{$zone['id']}}"  {{$notification->zone_id==$zone['id']?'selected':''}}>{{$zone['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="tergat">{{translate('messages.send_to')}}</label>

                                        <select name="tergat" class="form-control" id="tergat" data-placeholder="{{translate('messages.select_tergat')}}" required>
                                            <option value="customer" {{$notification->tergat=='customer'?'selected':''}}>{{translate('messages.customer')}}</option>
                                            <option value="deliveryman" {{$notification->tergat=='deliveryman'?'selected':''}}>{{translate('messages.deliveryman')}}</option>
                                            <option value="store" {{$notification->tergat=='store'?'selected':''}}>{{translate('messages.store')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.description')}}</label>
                                        <textarea name="description" class="form-control" maxlength="1000" required>{{$notification['description']}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="h-100 d-flex flex-column">
                                <label class="d-block text-center mt-auto mb-0">
                                    {{translate('messages.image')}}
                                    <small class="text-danger">* ( {{translate('messages.ratio')}} 900x300 )</small>
                                </label>
                                <div class="text-center py-3 my-auto">
                                    <img class="img--vertical onerror-image" id="viewer"
                                    src="{{ $notification['image_full_url'] }}"
                                    data-onerror-image="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="image"/>
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".webp, .jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{translate('messages.choose_file')}}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container mt-4 justify-content-end">
                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('messages.send_again')}}</button>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/view-pages/notification.js"></script>
    <script>
        "use strict";
            $('#reset_btn').click(function(){
                $('#zone').val("{{$notification->zone_id}}").trigger('change');
                $('#viewer').attr('src', "{{asset('storage/app/public/notification')}}/{{$notification['image']}}");
            })
        </script>
@endpush

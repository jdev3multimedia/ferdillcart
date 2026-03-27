@extends('layouts.vendor.app')

@section('title',translate('Update Banner'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--26" alt="">
                </span>
                <span>
                    {{translate('messages.update_banner')}}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('vendor.banner.update', [$banner->id])}}" method="POST" enctype="multipart/form-data" class="custom-validation">
                    @csrf
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 d-flex justify-content-between">
                                    <h3 class="form-label d-block mb-2">
                                        {{translate('Upload_Banner')}}
                                    </h3>

                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group error-wrapper">

                                        <label for="title" class="form-label">{{translate('title')}}</label>
                                        <input id="title" type="text" name="title" class="form-control" value="{{ $banner->title }}" placeholder="{{translate('messages.title_here...')}}" required>
                                    </div>
                                    <div class="form-group error-wrapper">

                                        <label for="default_link" class="form-label">{{translate('Redirection_URL_/_Link')}}</label>
                                        <input id="default_link" type="url" name="default_link" class="form-control" value="{{ $banner->default_link }}" placeholder="{{translate('messages.Enter_URL')}}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="upload-img-3 m-0 d-block error-wrapper">
                                        <div class="img">
                                            <img src="{{$banner['image_full_url']}}"
                                            id="viewer"
                                                 data-onerror-image="{{asset('/public/assets/admin/img/upload-4.png')}}"
                                                  class="vertical-img mw-100 vertical onerror-image" alt="">
                                        </div>
                                            <input type="file" name="image"  hidden>
                                    </label>
                                    <h3 class="form-label d-block mt-2">
                                        {{translate('Banner_Image_Ratio_3:1')}}
                                    </h3>
                                    <p>{{translate('image_format_:_jpg_,_png_,_jpeg_|_maximum_size:_2_MB')}}</p>
                                </div>
                                <div class="col-sm-6">
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('Reset')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('Update')}}</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection

@push('script_2')
        <script>
            "use strict";
            $('#reset_btn').click(function(){
                $('#viewer').attr('src','{{asset('storage/app/public/banner')}}/{{$banner['image']}}');
            })
        </script>

@endpush

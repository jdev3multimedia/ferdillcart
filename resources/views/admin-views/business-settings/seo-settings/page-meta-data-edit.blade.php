@extends('layouts.admin.app')

@section('title',translate('messages.SEO Setup'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title text-break">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/seo-setting.png')}}" class="w--26" alt="">
            </span>
            <span>{{ translate('Manage Page SEO') }}</span>
        </h1> 
    </div>
    <!-- End Page Header -->

    <form action="{{ route('admin.business-settings.seo-settings.pageMetaDataUpdate') }}" method="POST"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="page_name" value="{{ request()->page_name }}">
                                    @include('admin-views.business-settings.landing-page-settings.partial._meta_data',['submit'=>true])
    </form>
    
    

</div>
@endsection




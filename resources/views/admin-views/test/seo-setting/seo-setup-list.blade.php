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
            <span>Manage Page SEO</span>
        </h1> 
    </div>
    <div class="bg-opacity-primary-10 rounded py-2 px-3 d-flex flex-wrap gap-1 align-items-center mb-20">
        <div class="gap-1 d-flex align-items-center">
            <i class="tio-light-on theme-clr-dark fs-16"></i>
            <p class="m-0 fs-12">{{ translate('Manage meta information to improve page performance in search results') }}</p>
        </div>
    </div>
    <!-- End Page Header -->
 
    <div class="card">
        <div class="card-header flex-wrap pt-3 pb-3 border-0 gap-2">
            <div class="search--button-wrapper mr-1">
                <h4 class="card-title fs-16 text-dark">{{ translate('messages.SEO Setup List')}} <span class="badge badge-soft-dark ml-2 rounded-circle fs-12" id="itemCount">12</span></h4>
                <form class="search-form min--260">
                    <div class="input-group input--group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h--40px" placeholder="Search Keywords" value="" aria-label="Search" tabindex="1">

                        <button type="submit" class="btn btn--secondary bg-modal-btn"><i class="tio-search text-muted"></i></button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Table -->
            <div class="table-responsive space-around-16 datatable-custom">
                <table class="table table-borderless table-thead-borderless table-align-middle table-nowrap card-table m-0">
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0 min-w--120">SL</th>
                            <th class="border-0">Page Name</th>
                            <th class="border-0 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    Terms & Conditions
                                </div>
                            </td>
                            <td>
                                <div class="text-right">
                                    <button type="button" class="btn btn-outline-success">
                                        <i class="tio-add"></i> Add Content
                                    </button>
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <td>2</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    Privacy Policy
                                </div>
                            </td>
                            <td>
                                <div class="text-right">
                                    <button type="button" class="btn btn-outline-theme-dark">
                                        <i class="tio-edit"></i> Edit Content
                                    </button>
                                </div>
                            </td>
                        </tr>   
                        <tr>
                            <td>3</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    User Agreement
                                </div>
                            </td>
                            <td>
                                <div class="text-right">
                                    <button type="button" class="btn btn-outline-success">
                                        <i class="tio-add"></i> Add Content
                                    </button>
                                </div>
                            </td>
                        </tr> 
                        <tr>
                            <td>4</td>
                            <td>
                                <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                    Refund Policy
                                </div>
                            </td>
                            <td>
                                <div class="text-right">
                                    <button type="button" class="btn btn-outline-theme-dark">
                                        <i class="tio-edit"></i> Edit Content
                                    </button>
                                </div>
                            </td>
                        </tr>                                    
                    </tbody>
                </table>
            </div>
            <!-- End Table -->
        </div>
    </div>             




 



</div>

@endsection


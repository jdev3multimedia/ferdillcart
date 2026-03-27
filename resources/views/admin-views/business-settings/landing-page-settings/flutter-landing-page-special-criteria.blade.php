@extends('layouts.admin.app')

@section('title',translate('messages.flutter_web_landing_page'))

@section('content')

<div class="content container-fluid">
    <div class="page-header pb-0">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/flutter.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{ translate('messages.flutter_web_landing_page') }}
                </span>
            </h1>
            <div class="text--primary-2 py-1 d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#how-it-works">
                <strong class="mr-2">{{translate('See_how_it_works!')}}</strong>
                <div>
                    <i class="tio-info-outined"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-20 mt-2">
        <div class="js-nav-scroller hs-nav-scroller-horizontal">
            @include('admin-views.business-settings.landing-page-settings.top-menu-links.flutter-landing-page-links')
        </div>
    </div>
    @php($language=\App\Models\BusinessSetting::where('key','language')->first())
    @php($language = $language->value ?? null)
    @php($defaultLang = str_replace('_', '-', app()->getLocale()))
    @if($language)
        <ul class="nav nav-tabs mb-4 border-0">
            <li class="nav-item">
                <a class="nav-link lang_link active"
                href="#"
                id="default-link">{{translate('messages.default')}}</a>
            </li>
            @foreach (json_decode($language) as $lang)
                <li class="nav-item">
                    <a class="nav-link lang_link"
                        href="#"
                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                </li>
            @endforeach
        </ul>
    @endif
    <div class="tab-content">
        <div class="tab-pane fade show active">
                <form action="{{ route('admin.business-settings.flutter-landing-page-settings', 'special-criteria-list') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                <h5 class="card-title mb-3 mt-3">
                    <span class="card-header-icon mr-2"><i class="tio-settings-outlined"></i></span> <span>{{translate('Special_Feature_List_Section ')}}</span>
                </h5>
                <div class="card mb-3">
                    <div class="card-body">

                            <div class="row g-3">
                                @if ($language)
                                <div class="col-sm-6 lang_form default-form">
                                    <label for="title" class="form-label">{{translate('Title')}} ({{ translate('messages.default') }})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                            </span></label>
                                    <input id="title" type="text"  maxlength="30" name="title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                    @foreach(json_decode($language) as $lang)
                                    <div class="col-sm-6 d-none lang_form" id="{{$lang}}-form1">
                                        <label for="title{{$lang}}" class="form-label">{{translate('Title')}} ({{strtoupper($lang)}})<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                            <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                        </span></label>
                                <input type="text" id="title{{$lang}}" maxlength="30" name="title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                    </div>
                                        <input type="hidden" name="lang[]" value="{{$lang}}">
                                    @endforeach
                                @else
                                <div class="col-sm-6">
                                    <label for="title" class="form-label">{{translate('Title')}}<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Write_the_title_within_30_characters') }}">
                                                <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                            </span></label>
                                    <input type="text" id="title" maxlength="30" name="title[]" class="form-control" placeholder="{{translate('messages.title_here...')}}">
                                </div>
                                    <input type="hidden" name="lang[]" value="default">
                                @endif
                                <div class="col-sm-6">
                                    <div>

                                        <label class="form-label">{{translate('Criteria Icon/ Image')}}<span class="form-label-secondary" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('Icon_ratio_(1:1)_and_max_size_2_MB.') }}">
                                            <img src="{{asset('public/assets/admin/img/info-circle.svg')}}" alt="">
                                        </span></label>
                                    </div>
                                    <label class="upload-img-3 m-0">
                                        <div class="img">
                                            <img src="{{asset('/public/assets/admin/img/aspect-1.png')}}" alt="" class="img__aspect-1 min-w-187px max-w-187px">
                                        </div>
                                          <input type="file"  name="image" hidden>
                                    </label>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-20">
                                <button type="reset" class="btn btn--reset mb-2">{{translate('Reset')}}</button>
                                <button type="submit"   class="btn btn--primary mb-2">{{translate('Add')}}</button>
                            </div>
                        </div>
                        </div>
                    </form>
                    @php($criterias=\App\Models\FlutterSpecialCriteria::all())
                    <div class="card-body p-0">
                        <!-- Table -->
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
                                    <th class="border-0">{{translate('sl')}}</th>
                                    <th class="border-0">{{translate('Title')}}</th>
                                    <th class="border-0">{{translate('Image')}}</th>
                                    <th class="border-0">{{translate('Status')}}</th>
                                    <th class="text-center border-0">{{translate('messages.action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($criterias as $key=>$criteria)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            <div class="text--title">
                                            {{ $criteria->title }}
                                            </div>
                                        </td>
                                        <td>
                                            <img
                                            src="{{ $criteria->image_full_url ?? asset('/public/assets/admin/img/upload-3.png') }}" 
                                            data-onerror-image="{{asset('/public/assets/admin/img/upload-3.png')}}" class="__size-105 onerror-image" alt="">
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm">
                                                <input type="checkbox"
                                                       data-id="status-{{$criteria->id}}"
                                                       data-type="status"
                                                       data-image-on="{{ asset('/public/assets/admin/img/modal/this-criteria-on.png') }}"
                                                       data-image-off="{{ asset('/public/assets/admin/img/modal/this-criteria-off.png') }}"
                                                       data-title-on="{{ translate('messages.want_to_enable') }} <strong>{{ translate('this_feature?') }}"
                                                       data-title-off="{{ translate('messages.want_to_disable') }} <strong>{{ translate('this_feature?') }}"
                                                       data-text-on="<p>{{ translate('If_yes,_it_will_be_available_on_the_landing_page.') }}</p>"
                                                       data-text-off="<p>{{ translate('If_yes,_it_will_be_hidden_from_the_landing_page.') }}</p>"
                                                       class="status toggle-switch-input dynamic-checkbox"

                                                       id="status-{{$criteria->id}}" {{$criteria->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                            <form action="{{route('admin.business-settings.flutter-criteria-status',[$criteria->id,$criteria->status?0:1])}}" method="get" id="status-{{$criteria->id}}_form">
                                            </form>
                                        </td>

                                        <td>
                                            <div class="btn--container justify-content-center">
                                                <a class="btn action-btn btn--primary btn-outline-primary" href="{{route('admin.business-settings.flutter-criteria-edit',[$criteria['id']])}}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                                <a class="btn action-btn btn--danger btn-outline-danger form-alert " href="javascript:"
                                                   data-id="criteria-{{$criteria['id']}}"
                                                   data-message="{{ translate('Want_to_delete_this_feature_?') }}"
                                                   data-test={{translate('If_yes,_It_will_be_removed_from_this_list_and_the_landing_page.')}}""
                                               title="{{translate('messages.delete_criteria')}}"><i class="tio-delete-outlined"></i>
                                                </a>
                                                <form action="{{route('admin.business-settings.flutter-criteria-delete',[$criteria['id']])}}" method="post" id="criteria-{{$criteria['id']}}">
                                                    @csrf @method('delete')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        <!-- End Table -->
                    </div>
                    @if(count($criterias) === 0)
                    <div class="empty--data">
                        <img src="{{asset('/public/assets/admin/svg/illustrations/sorry.svg')}}" alt="public">
                        <h5>
                            {{translate('no_data_found')}}
                        </h5>
                    </div>
                    @endif
                </div>
        </div>
    </div>

    <!-- How it Works -->
    @include('admin-views.business-settings.landing-page-settings.partial.how-it-work-flutter')
@endsection


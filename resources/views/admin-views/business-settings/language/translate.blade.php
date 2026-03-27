@extends('layouts.admin.app')

@section('title',translate('messages.language'))


@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <h2 class="h1 font-bold mb-1 text-capitalize">
                {{translate('View Translations')}} - {{ \App\CentralLogics\Helpers::get_language_name($lang) }} ({{$lang}})
            </h2>
            <h6 class="text-info fs-12 d-flex gap-2 align-items-center mb-0">
                <i class="tio-back-ui fs-10"></i>
                <a style="color: #245BD1;" href="{{ route('admin.business-settings.language.index') }}">{{ translate('messages.Back to Language Setup') }}</a>
            </h6>
        </div>
        <div class="fs-12 text-title px-3 py-2 rounded bg-warning d-flex align-items-center gap-2 h-100 bg-opacity-10 mb-3">
            <span class="text-warning lh-1 fs-14">
                <i class="tio-info"></i>
            </span>
            <span>
                {{ translate('messages.If you change your default language full') }}
                <span class="font-semibold">{{ translate('messages.System Language') }}</span>
                {{ lcfirst(translate('messages.will changed. So, make sure before change')) }}
                <span class="font-semibold">{{ translate('messages.Default Language.') }}</span>
            </span>

        </div>
        <div class="card card-body">
            <div class="d-flex align-items-center flex-wrap justify-content-end gap-3 mb-20">
                <h4 class="m-0 text-capitalize flex-grow-1">{{translate('language_content_table')}}</h4>
                <form class="search-form min--260">
                    <!-- Search -->
                    <div class="input-group input--group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control h--40px"
                                placeholder="{{ translate('messages.Search_Language') }}" aria-label="{{translate('messages.search')}}" value="{{ request()?->search ?? null }}" required>
                        <input type="hidden">
                        <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>

                    </div>
                    <!-- End Search -->
                </form>
                @if ($lang !== 'en')
                <button class="btn btn--primary d-flex align-items-center justify-content-center gap-2" id="translate-confirm-btn">
                    <img width="14" height="14" class="svg" src="{{asset('public/assets/admin/img/svg/language-exchange.svg')}}" alt="public">
                    {{ translate('Translate_All') }}
                </button>
                @endif
            </div>
            <input type="hidden" value="0" id="translating-count">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" >
                    <thead class="thead-light table-nowrap">
                    <tr>
                        <th>{{translate('SL#')}}</th>
                        <th class="__width-400">{{translate('Current_value')}}</th>
                        <th class="__min-width">{{translate('translated_value')}}</th>
                        <th class="text-center">{{translate('auto_translate')}}</th>
                        <th class="text-center">{{translate('update')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @php($count=0)
                    @foreach($full_data as $key=>$value)
                    @php($count++)

                    <tr id="lang-{{$count}}">
                        <td>{{ $count+$full_data->firstItem() -1}}</td>
                        <td >
                            <input type="text" name="key[]"
                            value="{{$key}}" hidden>
                            <div style="max-inline-size: 450px"> {{translate($key) }}</div>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="value[]"
                            id="value-{{$count}}"
                            value="{{$full_data[$key]}}">
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <button type="button"
                                    data-key="{{$key}}" data-id="{{$count}}"
                                    class="btn btn--primary btn-outline-primary action-btn auto-translate-btn">
                                    <img width="14" height="14" class="svg" src="{{asset('public/assets/admin/img/svg/language-exchange.svg')}}" alt="public">
                                </button>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <button type="button"
                                        data-key="{{$key}}"
                                        data-id="{{$count}}"
                                        class="btn btn--primary action-btn update-language-btn">
                                         <img width="14" height="14" class="svg" src="{{asset('public/assets/admin/img/svg/disk.svg')}}" alt="public">
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                    </tbody>
                </table>
                @if(count($full_data) !== 0)
                <hr>
                @endif
                <div class="page-area">
                    {!! $full_data->appends(request()->query())->links() !!}
                </div>
                @if(count($full_data) === 0)
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


    <div class="modal fade language-complete-modal" id="translate-confirm-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered max-w-450px">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="py-5">
                        <div class="mb-4">
                            <img src="{{asset('/public/assets/admin/img/language-complete.png')}}" alt="">
                        </div>
                        <h4 class="mb-3">{{ translate('messages.Are you sure ?') }}</h4>
                        <p class="mb-4 text-9EADC1 max-w-362px mx-auto">
                            {{ translate('You_want_to_auto_translate_all._It_may_take_a_while_to_complete_the_translation') }}
                        </p>
                        <div class="d-flex justify-content-center gap-3 pt-1">

                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                            <button type="button" class="btn btn--primary auto_translate_all" data-dismiss="modal" >{{ translate('Yes,_Translate_All') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade language-complete-modal" id="complete-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered max-w-450px">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="py-5">
                        <div class="mb-4">
                            <img src="{{asset('/public/assets/admin/img/language-complete.png')}}" alt="">
                        </div>
                        <h4 class="mb-3">{{ translate('Your_file_has_been_successfully_translated') }}</h4>
                        <p class="mb-4 text-9EADC1 max-w-362px mx-auto">
                            {{ translate('All_your_items_has_been_translated.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-3 pt-1">
                            <button type="button" class="btn btn--primary location_reload" data-dismiss="modal">{{ translate('messages.Okay') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade language-warning-modal" id="warning-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="d-flex gap-3 align-items-start">
                        <img src="{{asset('/public/assets/admin/img/invalid-icon.png')}}" alt="">
                        <div class="w-0 flex-grow-1">
                            <h3>{{ translate('Warning!') }}</h3>
                            <p>
                               {{ translate('Translating_in_progress._Are_you_sure,_want_to_close_this_tab?_If_you_close_the_tab,_then_some_translated_items_will_be_unchanged.') }}
                            </p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-3">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="button" class="btn btn--primary" id="close-tab" >{{ translate('Yes,_Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade language-complete-modal " id="translating-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="py-5 px-sm-2">
                        <div class="progress-circle-container mb-4">
                            <img width="80px" src="{{asset('/public/assets/admin/img/loader-icon.gif')}}" alt="">
                        </div>
                        <h4 class="mb-2">{{ translate('Translating_may_take_up_to') }} <span id="time-data"> {{ translate('Hours') }}</span></h4>
                        <p class="mb-4">
                            {{ translate('Please_wait_&_don’t_close/terminate_your_tab_or_browser') }}
                        </p>
                        <div class="max-w-215px mx-auto">
                            <div class="d-flex flex-wrap mb-1 justify-content-between font-semibold text--title">
                                <span>{{ translate('In_Progress') }}</span>
                                <span class="translating-modal-success-rate">0.4%</span>
                            </div>
                            <div class="progress mb-3 h-5px">
                                <div class="progress-bar bg-success rounded-pill translating-modal-success-bar" style="width: 0.4%"></div>
                            </div>
                        </div>
                        <p class="mb-4 text-9EADC1">
                            <span class="text-dark">{{ translate('note:') }}</span> {{ translate('All_the_translations_may_not_be_fully_accurate.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-3 pt-1">
                            <button type="button" class="btn btn--primary location-reload"  >{{ translate('messages.Cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
<script>
    "use strict"


    $(document).on('click', '.auto-translate-btn', function () {
        let key = $(this).data('key');
        let id = $(this).data('id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('admin.business-settings.language.auto-translate',[$lang])}}",
            method: 'POST',
            data: {
                key: key
            },
            beforeSend: function () {
                $('#loading').show();
            },
            success: function (response) {
                toastr.success('{{translate('Key translated successfully')}}');
                $('#value-'+id).val(response.translated_data);
            },
            complete: function () {
                $('#loading').hide();
            },
        });
    });
    $(document).on('click', '.update-language-btn', function () {
        let key = $(this).data('key');
        let id = $(this).data('id');
        let value = $('#value-'+id).val() ;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('admin.business-settings.language.translate-submit',[$lang])}}",
            method: 'POST',
            data: {
                key: key,
                value: value
            },
            beforeSend: function () {
                $('#loading').show();
            },
            success: function () {
                toastr.success('{{translate('text_updated_successfully')}}');
            },
            complete: function () {
                $('#loading').hide();
            },
        });
    });







    $(document).on('click', '#translate-confirm-btn', function () {
        $('#translate-confirm-modal').modal('show')

    });
    $(document).on('click', '.auto_translate_all', function () {
        auto_translate_all();

    });
    $(document).on('click', '.location_reload', function () {
        location.reload();

    });
    $(document).on('click', '.close-tab', function () {
        $('#translating-modal').removeClass('prevent-close')
        window.close();

    });

    function  auto_translate_all(){
        var total_count;
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('admin.business-settings.language.auto_translate_all',[$lang])}}",
            method: 'get',
            data: {
                translating_count: $('#translating-count').val(),
            },
            beforeSend: function () {
                $('#translating-modal').addClass('prevent-close')
                $('#translating-modal').modal('show')
            },
            success: function (response) {

                if(response.data === 'data_prepared'){
                    $('#translating-modal').modal('show')
                    $('#translating-count').val(response.total)
                    auto_translate_all();
                } else if(response.data === 'translating' &&  response.status === 'pending' ){
                    if($('#translating-count').val() == 0  ){
                        $('#translating-count').val(response.total)
                    }

                    $('.translating-modal-success-rate').html(response.percentage + '%');
                    $('.translating-modal-success-bar').attr('style', 'width:' + response.percentage + '%');


                        if(response.hours > 0){
                            $('#time-data').html(response.hours + ' {{ translate('hours') }} ' + response.minutes + ' {{ translate('min') }}' );
                        }
                        if(response.minutes > 0 && response.hours <= 0){
                            $('#time-data').html(response.minutes + ' {{ translate('min') }} ' +  response.seconds + ' {{ translate('seconds') }}');
                        }
                        if(response.seconds > 0 && response.minutes <= 0){
                            $('#time-data').html(response.seconds + ' {{ translate('seconds') }}');
                        }

                    auto_translate_all();

                    $('#translating-modal').modal('show')
                    } else if((response.data === 'translating' &&  response.status === 'done') || response.data === 'success' || response.data === 'error'  ){
                        $('#translating-modal').removeClass('prevent-close')
                        $('#translating-modal').modal('hide')
                        $('#translating-count').val(0)
                        if(response.data !== 'error'){
                            $('#complete-modal').modal('show')
                        } else{
                            toastr.error(response.message);
                        }
                    }
            },
            complete: function () {
            },
        });
    }

    const modal = document.getElementById('translating-modal');
    window.addEventListener('beforeunload', (event) => {

        if (modal.classList.contains('prevent-close')) {
            // $('#warning-modal').modal('show')
            event.preventDefault();
            event.returnValue = '';
        }
    });
</script>

@endpush

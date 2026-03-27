@extends('layouts.admin.app')

@section('title',translate('messages.Delivery Man Preview'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
                        @include('admin-views.delivery-man.partials._page_header')

            <div class="">
                @include('admin-views.delivery-man.partials._tab_menu')
            </div>
        </div>
        <!-- End Page Header -->

        <div class="content">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-header-title">{{ translate('messages.conversation_list') }}</h1>
            </div>
            <!-- End Page Header -->

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <!-- Card -->
                    <div class="card h-100">
                        <div class="card-header border-0">
                            <div class="input-group input---group">
                                <div class="input-group-prepend border-inline-end-0">
                                    <span class="input-group-text border-inline-end-0" id="basic-addon1"><i class="tio-search"></i></span>
                                </div>
                                <input type="text" class="form-control border-inline-start-0 pl-1" id="serach" placeholder="{{ translate('messages.search') }}" aria-label="Username"
                                    aria-describedby="basic-addon1" autocomplete="off">
                            </div>
                        </div>
                        <!-- Body -->
                        <div class="card-body p-0 initial-19"  id="dm-conversation-list">
                            <div class="d-flex justify-content-start px-3 gap-4 mb-3 tab-button-group">
                                <button id="customer_conversations" data-url="{{route('admin.users.delivery-man.preview', ['id'=>$deliveryMan->id, 'tab'=> 'conversation','conversation_with' =>'customer'])}}" class="{{ request()?->conversation_with != 'store' ? 'active' : 'redirect-url' }}">{{ translate('Customer') }}</button>
                                <button id="store_conversations" data-url="{{route('admin.users.delivery-man.preview', ['id'=>$deliveryMan->id, 'tab'=> 'conversation','conversation_with' =>'store'])}}" class="{{ request()?->conversation_with == 'store' ? 'active' : 'redirect-url' }}">{{ translate('Store')}}</button>
                            </div>
                            <div id="dm-conversation-list-search">
                                @include('admin-views.delivery-man.partials._conversation_list')
                            </div>
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->
                </div>
                <div class="col-lg-8 col-nd-6" id="dm-view-conversation">
                    <div class="h-100 card d-flex align-items-center justify-content-center">
                        <div class="text-center">
                            <div class="empty-conversation-content d-flex flex-column align-items-center gap-2">
                                <img width="120" height="120" src="{{asset('/public/assets/admin/img/icons/empty-conversation.png')}}" alt="public">
                                <h5 class="text-muted">
                                    {{translate('no_conversation_found')}}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->
        </div>

    </div>
@endsection

@push('script_2')
<script>
    "use strict";
    let lastPage ={{ is_object($conversations) ? $conversations?->lastPage() : 1 }};

    $(document).on('click', '.view-conv', function () {
    let url = $(this).data('url');
    let id_to_active = $(this).data('active-id');
    let conv_id = $(this).data('conv-id');
    let sender_id = $(this).data('sender-id');
    viewConvs(url, id_to_active, conv_id, sender_id);
});

    function viewConvs(url, id_to_active, conv_id, sender_id) {
        $('.customer-list').removeClass('conv-active');
        $('#' + id_to_active).addClass('conv-active');
        let new_url= "{{route('admin.users.delivery-man.preview', ['id'=>$deliveryMan->id, 'tab'=> 'conversation' , 'conversation_with' => request()?->conversation_with  ? request()?->conversation_with : 'customer'  ])}}" + '&conversation=' + conv_id+ '&user=' + sender_id;
            $.get({
                url: url,
                success: function(data) {
                    window.history.pushState('', 'New Page Title', new_url);
                    $('#dm-view-conversation').html(data.view);
                }
            });
    }

    let page = 1;
    let user_id =  {{ $deliveryMan->id }};
    $('#dm-conversation-list').scroll(function() {
        if ($('#dm-conversation-list').scrollTop() + $('#dm-conversation-list').height() >= $('#dm-conversation-list').height()  && lastPage > page ) {
            page++;
            loadMoreData(page);
        }
    });

    function loadMoreData(page) {
        $.ajax({
                url: "{{ route('admin.users.delivery-man.message-list-search') }}" + '?page=' + page + "&conversation_with=" + "{{request()->conversation_with  ? request()->conversation_with : 'customer'}}",
                type: "get",
                data:{"user_id":user_id},
                beforeSend: function() {

                }
            })
            .done(function(data) {
                if (data.html == " ") {
                    return;
                }
                $("#dm-conversation-list-search").append(data.html);
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                alert('server not responding...');
            });
    };

    function fetch_data(page, query) {
            $.ajax({
                url: "{{ route('admin.users.delivery-man.message-list-search') }}" + '?page=' + page + "&key=" + query + "&conversation_with=" + "{{request()->conversation_with  ? request()->conversation_with : 'customer'}}",
                type: "get",
                data:{"user_id":user_id},
                success: function(data) {
                    $('#dm-conversation-list-search').empty();
                    $("#dm-conversation-list-search").append(data.html);
                }
            })
        };

        $(document).on('keyup', '#serach', function() {
            let query = $('#serach').val();
            fetch_data(page, query);
        });
</script>
@endpush

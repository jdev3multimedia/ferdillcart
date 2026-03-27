@extends('layouts.vendor.app')

@section('title',translate('messages.Delivery Man Preview'))


@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/deliveryman.png')}}" class="w--30" alt="">
                </span>
                <span>
                    {{$dm['f_name'].' '.$dm['l_name']}}
                </span>
            </h1>
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <ul class="nav nav-tabs mb-3 border-0 nav--tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('vendor.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'info'])}}"  aria-disabled="true">{{translate('messages.info')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{route('vendor.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction'])}}"  aria-disabled="true">{{translate('messages.transaction')}}</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card mb-3 mb-lg-5 mt-2">
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h4 class="card-title">{{ translate('messages.order_transactions')}}</h4>
                    <form action="javascript:" id="search-form" class="search-form">
                        @csrf
                        <input type="hidden" name="dm_id" value="{{ $dm->id }}">
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input value="{{request()?->search ?? ''}}"  required type="search" name="search" class="form-control" placeholder="{{translate('messages.ex_search_order_id ')}}" aria-label="Search">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>

                </div>
            </div>
            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-borderless table-thead-bordered table-nowrap justify-content-between table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{translate('messages.sl#')}}</th>
                                <th class="border-0">{{translate('messages.order_id')}}</th>
                                <th class="border-0">{{translate('messages.deliveryman_earned')}}</th>
                                <th class="border-0">{{translate('messages.date')}}</th>
                            </tr>
                        </thead>
                        <tbody id="set-rows">
                        @php($digital_transaction = \App\Models\OrderTransaction::where('delivery_man_id', $dm->id)->paginate(25))
                        @foreach($digital_transaction as $k=>$dt)
                            <tr>
                                <td>{{$k+$digital_transaction->firstItem()}}</td>
                                <td><a href="{{route('vendor.order.details',$dt->order_id)}}">{{$dt->order_id}}</a></td>
                                <td>{{$dt->original_delivery_charge}}</td>
                                <td>{{$dt->created_at->format('Y-m-d')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- End Body -->
            <div class="card-footer">
                {!!$digital_transaction->links()!!}
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
<script>

    $('#search-form').on('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.delivery-man.transaction-search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
</script>
@endpush

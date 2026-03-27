<div class="h-100vh-150px">
    <div class="d-flex flex-column align-items-center gap-1 mb-3">
        <h3 class="mb-3">{{translate('withdraw_Information')}}</h3>
        <div class="d-flex gap-2 align-items-center mb-1 flex-wrap">
            <span>{{translate('withdraw_Amount')}}:</span>
            <span class="font-semibold">{{\App\CentralLogics\Helpers::format_currency($withdraw['amount'])}}</span>
    @if ($withdraw->approved == 1)
    <label class="badge badge-soft-success mb-0">{{translate('approved')}}</label>
    
    @elseif($withdraw->approved ==0)
    <label class="badge badge-soft-info mb-0">{{translate('Pending')}}</label>
    @else
    
    <label class="badge badge-soft-danger mb-0">{{translate('Denied')}}</label>
    @endif
    
    
        </div>
        <div class="d-flex gap-2 align-items-center fs-12">
            <span>{{translate('request_time')}}:</span>
            <span>{{ \App\CentralLogics\Helpers::time_date_format($withdraw->created_at) }}</span>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0 font-medium">{{translate('deliveryman_Info')}}</h6>
        </div>
        <div class="card-body">
            <div class="key-val-list d-flex flex-column mb-2 gap-2" style="--min-width: 60px">
                <div class="key-val-list-item d-flex gap-3">
                    <span>{{ translate('Name') }}:</span>
                    <span>{{$withdraw->deliveryman->f_name.' '.$withdraw->deliveryman->l_name}}</span>
                </div>
            </div>
            <div class="key-val-list d-flex flex-column mb-2 gap-2" style="--min-width: 60px">
                <div class="key-val-list-item d-flex gap-3">
                    <span>{{ translate('Email') }}:</span>
                    <span class="text-title">{{$withdraw->deliveryman->email}}</span>
                </div>
            </div>
            <div class="key-val-list d-flex flex-column mb-2 gap-2" style="--min-width: 60px">
                <div class="key-val-list-item d-flex gap-3">
                    <span>{{ translate('Phone') }}:</span>
                    <span class="text-title">{{$withdraw->deliveryman->phone}}</span>
                </div>
            </div>
    
            <div class="rounded bg-light p-3 mt-3">
                <div class="key-val-list-item d-flex gap-3 align-items-center lh-1">
                    <span>{{ translate('deliveryman_Balance') }}:</span>
                    <span class="font-semibold text-primary fs-16"> {{ $withdraw->deliveryman->wallet->balance > 0 ? \App\CentralLogics\Helpers::format_currency($withdraw->deliveryman->wallet->balance) : 0}}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0 font-medium">{{translate('payment_Info')}}</h6>
        </div>
        <div class="card-body">
            <div class="key-val-list d-flex flex-column gap-2" style="--min-width: 60px">
              <div class="key-val-list-item d-flex gap-3">
                <span>{{ translate('method') }}:</span>
                <span class="text-title">{{ $withdraw?->method?->method_name ?? translate('messages.No_Data_found')}}</span>
            </div>
    
            @if($withdraw?->withdrawal_method_fields)
            @foreach(json_decode($withdraw?->withdrawal_method_fields, true)?? [] as $key => $item)
                <div class="key-val-list-item d-flex gap-3">
                    <span>{{ translate($key) }}:</span>
                    <span>{{ is_array($item) ? '' : htmlspecialchars($item) }}</span>
                </div>
            @endforeach
            @else
                <h5 class="text-capitalize">{{ translate('messages.No_Data_found') }}</h5>
            @endif
            </div>
        </div>
    </div>
    
        @if ($withdraw->approved == 1)
        <div class="">
            <h5 class="font-medium">{{translate('approved_Note')}}</h5>
            <div class="rounded bg-light p-3">
                {{str_replace('_' ,' ' ,$withdraw->transaction_note)}}
            </div>
            @elseif($withdraw->approved == 2)
        </div> <div class="">
            <h5 class="font-medium">{{translate('Denied_Note')}}</h5>
            <div class="rounded bg-light p-3">
                {{str_replace('_' ,' ' ,$withdraw->transaction_note)}}
            </div>
        </div>
        @endif
</div>

@if ($withdraw->approved == 0)
<div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3 w-100 withdraw-offcanvas-footer">
     <button type="button" data-id="{{$withdraw->id}}" class="btn btn-soft-danger min-w-100px w-100 show-deny-view">{{translate('deny')}}</button>
     <button type="button" data-id="{{$withdraw->id}}" class="btn btn--primary min-w-100px w-100 show-approve-view">{{translate('approve')}}</button>
 </div>
@endif



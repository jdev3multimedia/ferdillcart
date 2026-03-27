@foreach($reviews as $key=>$review)
@if(isset($review->delivery_man))
    <tr>
        <td>{{$key+1}}</td>
        <td>
        <span class="d-block font-size-sm text-body">
            <a href="{{route('admin.users.delivery-man.preview',[$review['delivery_man_id']])}}">
                {{$review->delivery_man->f_name.' '.$review->delivery_man->l_name}}
            </a>
        </span>
        </td>
        <td>
            @if ($review->customer)
            <a href="{{route('admin.users.customer.view',[$review->user_id])}}">
                {{$review->customer?$review->customer->f_name:""}} {{$review->customer?$review->customer->l_name:""}}
            </a>
            @else
                {{translate('messages.customer_not_found')}}
            @endif

        </td>
        <td>
            {{$review->comment}}
        </td>
        <td>
            <label class="rating">
                {{$review->rating}} <i class="tio-star"></i>
            </label>
        </td>
    </tr>
@endif
@endforeach

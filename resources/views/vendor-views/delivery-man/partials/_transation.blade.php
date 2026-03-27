@foreach($digital_transaction as $k=>$dt)
<tr>
    <td>{{$k+$digital_transaction->firstItem()}}</td>
    <td><a href="{{route('admin.order.details',$dt->order_id)}}">{{$dt->order_id}}</a></td>
    <td>{{$dt->original_delivery_charge}}</td>
    <td>{{$dt->created_at->format('Y-m-d')}}</td>
</tr>
@endforeach

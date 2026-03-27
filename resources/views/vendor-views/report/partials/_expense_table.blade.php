@foreach($expense as $k=>$exp)
<tr>
    <td scope="row">{{$k+1}}</td>
    <td><label class="text-uppercase">{{$exp['type']}}</label></td>
    <td><div class="pl-4">
        {{\App\CentralLogics\Helpers::format_currency($exp['amount'])}}
    </div></td>
    <td><div class="pl-4">
        {{$exp['description']}}
    </div></td>
    <td>{{date('Y-m-d '.config('timeformat'),strtotime($exp->created_at))}}</td>
</tr>
@endforeach

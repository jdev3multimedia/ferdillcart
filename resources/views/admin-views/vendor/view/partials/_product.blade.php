@foreach($foods as $key=>$food)

<tr>
    <td>{{$key+1}}</td>
    <td>
        <a class="media align-items-center" href="{{route('admin.item.view',[$food['id']])}}">
            <img class="avatar avatar-lg mr-3 onerror-image"
            src="{{ $food['image'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}" 
            data-onerror-image="{{asset('public/assets/admin/img/160x160/img2.jpg')}}" alt="{{$food->name}} image">
            <div class="media-body">
                <h5 class="text-hover-primary mb-0">{{Str::limit($food['name'],20,'...')}}</h5>
            </div>
        </a>
    </td>
    <td>
    {{Str::limit($food->category?$food->category->name:translate('messages.category_deleted'),20,'...')}}
    </td>
    <td>{{\App\CentralLogics\Helpers::format_currency($food['price'])}}</td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$food->id}}">
            <input type="checkbox" class="toggle-switch-input redirect-url" data-url="{{route('admin.item.status',[$food['id'],$food->status?0:1])}}" id="stocksCheckbox{{$food->id}}" {{$food->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn action-btn btn--primary btn-outline-primary"
               href="{{route('admin.item.edit',[$food['id']])}}" title="{{translate('messages.edit_item')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
               data-id="food-{{$food['id']}}" data-message="{{ translate('messages.Want to delete this item ?') }}" title="{{translate('messages.delete_item')}}"><i class="tio-delete-outlined"></i>
            </a>
        </div>
        <form action="{{route('admin.item.delete',[$food['id']])}}"
              method="post" id="food-{{$food['id']}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach
<script src="{{asset('public/assets/admin')}}/js/view-pages/common.js"></script>

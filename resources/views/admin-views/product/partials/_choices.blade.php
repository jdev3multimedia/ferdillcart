@foreach($choice_options as $key=>$choice)
@isset($choice_no[$key])
<div class="row">
    <div class="col-md-3 error-wrapper">
        <input type="hidden" name="choice_no[]" value="{{$choice_no[$key]}}">
        <input type="text" class="form-control" name="choice[]" value="{{$choice['title']}}"
               placeholder="{{translate('messages.choice_title')}}" readonly>
    </div>
    <div class="col-lg-9 error-wrapper">
        <input type="text" class="form-control call-update-sku" name="choice_options_{{$choice_no[$key]}}[]" data-role="tagsinput"
               value="@foreach($choice['options'] as $c) {{$c.','}} @endforeach">
    </div>
</div>

@endisset
@endforeach

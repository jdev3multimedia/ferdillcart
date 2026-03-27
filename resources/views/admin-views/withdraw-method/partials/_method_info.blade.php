{{-- {{ dd($withdrawal_method) }} --}}

<div class="modal-body pt-0 pb-2">
    <h4 class="text-center mb-1">{{translate('withdraw_Method_List')}}</h4>
    <div class="d-flex justify-content-center  align-items-center gap-2">
        <span>{{translate('method_Name')}}</span>
        :
        <span class="font-semibold text-dark">{{ $withdrawal_method->method_name }}</span>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-align-middle text-dark">
        <tbody>
            @foreach($withdrawal_method['method_fields'] as $key=>$method_field)
            <tr>
                <td class="px-4 {{1+$key === 1 ? "border-top-0" : ""}}">{{1+$key}}</td>
                <td class="{{1+$key === 1 ? "border-top-0" : ""}}">
                    <div>
                        <div>{{ translate('messages.Name')}}: {{ translate($method_field['input_name'])}}</div>
                        <div>{{ translate('messages.Type')}}: {{ translate($method_field['input_type']) }}</div>
                        <div>{{ translate('messages.Placeholder')}}: {{ $method_field['placeholder'] }}</div>
                    </div>
                </td>
                <td class="{{1+$key === 1 ? "border-top-0" : ""}}">
                    <div class="d-flex gap-3 align-items-center">
                        {!! $method_field['is_required'] ?
                            '<svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                                <path d="M3.43848 8.76026C3.11543 8.76116 2.79924 8.85351 2.52649 9.02662C2.25374 9.19973 2.03558 9.44652 1.89724 9.73845C1.7589 10.0304 1.70603 10.3555 1.74476 10.6762C1.78349 10.9969 1.91224 11.3001 2.1161 11.5507L6.46189 16.8743C6.61683 17.0667 6.81545 17.2194 7.04124 17.3196C7.26704 17.4198 7.51348 17.4647 7.76011 17.4506C8.2876 17.4222 8.76383 17.1401 9.06745 16.6761L18.0948 2.13765C18.0962 2.13524 18.0978 2.13283 18.0994 2.13045C18.1841 2.0004 18.1566 1.74267 17.9818 1.58076C17.9337 1.5363 17.8771 1.50214 17.8154 1.48038C17.7537 1.45863 17.6881 1.44975 17.6228 1.45427C17.5576 1.4588 17.4939 1.47665 17.4357 1.50672C17.3776 1.53678 17.3263 1.57843 17.2848 1.6291C17.2816 1.63309 17.2782 1.63701 17.2748 1.64087L8.17065 11.9272C8.13601 11.9664 8.09393 11.9982 8.04687 12.021C7.9998 12.0437 7.94869 12.0569 7.8965 12.0597C7.8443 12.0625 7.79207 12.055 7.74282 12.0374C7.69358 12.0199 7.64831 11.9927 7.60965 11.9576L4.58815 9.20797C4.27434 8.92031 3.86419 8.76058 3.43848 8.76026Z" fill="#10DC7C"/>
                            </svg>' :
                            ''
                        !!}
                        {{ $method_field['is_required'] ? translate('messages.Required') :  translate('messages.Optional') }}
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

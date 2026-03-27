<div class="row">
    <div class="col-lg-12 text-center "><h1 >{{ translate('reviews') }}</h1></div>
    <div class="col-lg-12">



    <table>
        <thead>
            <tr>
                <th>{{ translate('review') }}</th>
                <th></th>
                <th></th>
                <th>
                    {{ translate('Total')  }}: {{ $data['data']->count() }}


                </th>
                <th> </th>
                <th></th>
                <th></th>
                <th></th>
                </tr>
            <tr>
                <th>{{ translate('Search_Criteria') }}</th>
                <th></th>
                <th></th>
                <th>
                    {{ translate('Search_Bar_Content')  }}: : {{  $data['search']  ??translate('N/A') }}
                </th>
                <th> </th>
                <th></th>
                <th></th>
                <th></th>
                </tr>
        <tr>
            <th class="border-0">{{translate('messages.#')}}</th>
            <th class="border-0">{{translate('messages.Review_Id')}}</th>
            <th class="border-0">{{translate('messages.item')}}</th>
            <th class="border-0">{{translate('messages.order_id')}}</th>
            <th class="border-0">{{translate('messages.reviewer')}}</th>
            <th class="border-0">{{translate('messages.review')}}</th>
            <th class="border-0">{{translate('messages.date')}}</th>
        </thead>
        <tbody>
        @foreach($data['data'] as $key => $review)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{$review->review_id}}</td>
        <td>{{ $review->item['name'] }}</td>
        <td>{{ $review->order_id }}</td>

        <td>
            @if($review->customer)
                <div>
                    <h5 class="d-block text-hover-primary mb-1">{{Str::limit($review->customer['f_name']." ".$review->customer['l_name'])}} <i
                            class="tio-verified text-primary" data-toggle="tooltip" data-placement="top"
                            title="Verified Customer"></i></h5>
                    <span class="d-block font-size-sm text-body">({{Str::limit($review->customer->phone)}})</span>
                </div>
            @else
                {{translate('messages.customer_not_found')}}
            @endif
        </td>
        <td>
            <div class="text-wrap w-18rem">
                <label class="rating">
                    <i class="tio-star"></i>
                    <span>{{$review->rating}}</span>
                </label>
                <p data-toggle="tooltip" data-placement="bottom"
                   data-original-title="{{ $review?->comment }}" >
                    {{$review['comment']}}
                </p>
            </div>
        </td>
        <td>
            <span class="d-block">
                {{ \App\CentralLogics\Helpers::date_format($review->created_at)  }}
            </span>
            <span class="d-block"> {{ \App\CentralLogics\Helpers::time_format($review->created_at)  }}</span>
        </td>

            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

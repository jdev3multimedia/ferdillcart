<div class="card h-100">
    <!-- Header -->
    <div class="card-header">
        <div class="chat-user-info w-100 d-flex align-items-center">
            <div class="chat-user-info-img">
                <img class="avatar-img onerror-image"
                src="{{$user['image_full_url'] }}"
                    data-onerror-image="{{asset('public/assets/admin')}}/img/160x160/img1.jpg"
                    alt="Image Description">
            </div>
            <div class="chat-user-info-content">
                <h5 class="mb-0 text-capitalize">
                    {{$user['f_name'].' '.$user['l_name']}}</h5>
                <small>{{ $user['phone'] }}</small>
            </div>
        </div>
    </div>

    <div class="card-body d-flex flex-column">
        <div class="scroll-down">
            @php($count=0)
            @php($created_for=0)
            @forelse($conversations as $con)
                    @if ( Carbon\Carbon::parse($con?->created_at)->format('Y-m-d') == now()->format('Y-m-d') && $count == 0)
                        <div class="d-flex justify-content-center">{{ translate('Today').' '. \App\CentralLogics\Helpers::time_format($con?->created_at) }}</div>
                        @php($count=1)
                    @elseif(Carbon\Carbon::parse($con?->created_at)->format('Y-m-d') != $created_for && $count == 0)
                        <div class="d-flex justify-content-center">{{  \App\CentralLogics\Helpers::time_date_format($con?->created_at) }}</div>
                        @php($count=1)
                        @php($created_for=Carbon\Carbon::parse($con?->created_at)->format('Y-m-d'))
                    @else
                        @php($count=0)
                    @endif

                @if($con->sender_id == $user->id)
                    <div class="py-2 d-flex gap-2 align-items-end">
                        <div class="chat-user-conv-img">
                            <img class="avatar-img onerror-image" width="28" height="28" src="{{$user['image_full_url'] }}" data-onerror-image="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}" alt="Image Description">
                        </div>

                        <div class="conv-reply-1">
                            <h6 data-toggle="tooltip" data-placement="top" title="{{\App\CentralLogics\Helpers::time_date_format($con?->created_at)}}">{{$con->message}}</h6>
                            @if($con->file!=null)
                            @foreach ($con->file_full_url as $img)
                            <br>
                                <img class="w-100 mb-3"

                                src="{{$img }}"
                                >
                                @endforeach
                            @endif
                        </div>
                    </div>
                @else
                    <div class="py-2">
                        <div class="conv-reply-2">
                            <h6 data-toggle="tooltip" data-placement="top" title="{{\App\CentralLogics\Helpers::time_date_format($con?->created_at)}}">{{$con->message}}</h6>
                            @if($con->file!=null)
                            @foreach ($con->file_full_url as $img)
                            <br>
                                <img class="w-100 mb-3"

                                src="{{$img }}"
                                >
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
                @empty

                <div class="empty-conversation-content d-flex flex-column align-items-center gap-3">
                    <img width="128" height="128" src="{{asset('/public/assets/admin/img/icons/empty-conversation.png')}}" alt="public">
                    <h5 class="text-muted">
                        {{translate('no_conversation_found')}}
                    </h5>
                </div>

            @endforelse
            <div id="scroll-here"></div>
        </div>
    </div>



    <div class="mt-auto d-flex justify-content-center fs-12 font-medium text-dark p-3">
        {{ translate('You_can’t_reply_to_this_conversation.') }} &nbsp;
        <div class="text-danger d-inline-block learn-more-wrap cursor-pointer">
            {{ translate('Learn_more') }}

            <div class="learn-more-content p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img class="rounded-circle" width="20" height="20" src="{{asset('public/assets/admin/img/icons/info-icon.png')}}" alt="">
                    <h6 class="mb-0"> {{ translate('Learn_more') }}</h6>
                </div>
                <p class="mb-0 text-muted text-normal">{{ translate('You can’t chat with deliveryman because it’s delivery man previous chat history, only you can monitor or view their conversation to avoid unexpected situation.')}}</p>
            </div>
        </div>
    </div>




</div>
<script>
    "use strict";
    $(document).ready(function () {
        $('.scroll-down').animate({
            scrollTop: $('#scroll-here').offset().top
        },0);
    });
</script>

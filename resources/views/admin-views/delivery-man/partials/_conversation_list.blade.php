

@forelse($conversations as $conv)
@php($user= $conv->sender_type == 'delivery_man' ? $conv->receiver :  $conv->sender)
@if (isset($user))
    @php($unchecked=($conv->last_message->sender_id == $user->id) ? $conv->unread_message_count : 0)
    <input type="hidden" id="deliver_man" value="{{ $deliveryMan->id }}">
    <div
        class="chat-user-info d-flex p-3 align-items-center customer-list view-conv "
        data-url="{{route('admin.users.delivery-man.message-view',['conversation_id'=>$conv->id,'user_id'=>$user->id])}}" data-active-id="customer-{{$user->id}}" data-conv-id="{{ $conv->id }}" data-sender-id="{{ $user->id }}"
        id="customer-{{$user->id}}">
        <div class="chat-user-info-img d-none d-md-block">
            <img class="avatar-img onerror-image"
            src="{{ $user['image_full_url'] }}"
                    data-onerror-image="{{asset('public/assets/admin')}}/img/160x160/img1.jpg"
                    alt="Image Description">
        </div>
        <div class="chat-user-info-content">
            <h5 class="mb-0 d-flex justify-content-between">
                <span class=" mr-3">{{$user['f_name'].' '.$user['l_name']}}</span>
                <small class="text-muted">{{$conv?->last_message?->created_at ?  \App\CentralLogics\Helpers::time_date_format($conv?->last_message?->created_at) : '' }}</small>
            </h5>
            <small class="text-muted mb-1">{{ $user['phone'] }}</small>
            <div class="d-flex justify-content-between gap-1" >

                <div class="text-title fs-12">{{ $conv?->last_message?->message ?? ($conv?->last_message?->file ? translate('files_send') : '' )}}</div>
                <span class="{{$unchecked ? 'badge badge-primary' : ''}}">{{$unchecked ? $unchecked : ''}}</span>
            </div>
        </div>
    </div>
@else
    <div
        class="chat-user-info d-flex border-bottom p-3 align-items-center customer-list">
        <div class="chat-user-info-img d-none d-md-block">
            <img class="avatar-img"
                    src='{{asset('public/assets/admin')}}/img/160x160/img1.jpg'
                    alt="Image Description">
        </div>
        <div class="chat-user-info-content">
            <h5 class="mb-0 d-flex justify-content-between">
                <span class=" mr-3">{{ translate('Account not found') }}</span>
            </h5>
        </div>
    </div>
@endif
@empty

<div class="empty-conversation-content pt-5 mt-5">
    <div class="d-flex flex-column align-items-center gap-2 pt-5 mt-5">
        <img width="70" height="70" src="{{asset('/public/assets/admin/img/icons/empty-conversation.png')}}" alt="public">
        <h5 class="text-muted">
            {{translate('no_conversation_found')}}
        </h5>
    </div>
</div>

@endforelse

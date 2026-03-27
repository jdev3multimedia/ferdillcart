<div class="row">
    <div class="col-lg-12 text-center "><h1 >{{ translate('Contact_messages') }}</h1></div>
    <div class="col-lg-12">



    <table>
        <thead>
            <tr>
                <th>{{ translate('Message_Analytics') }}</th>
                <th></th>
                <th></th>
                <th>
                    {{ translate('Total')  }}: {{ $data->count() }}


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
                    {{ translate('Search_Bar_Content')  }}: : {{ $search ??translate('N/A') }}
                </th>
                <th> </th>
                <th></th>
                <th></th>
                <th></th>
                </tr>
        <tr>
            <th>{{ translate('sl') }}</th>
            <th>{{ translate('Name') }}</th>
            <th>{{ translate('Email') }}</th>
            <th>{{ translate('Subject') }}</th>
            <th>{{ translate('Message') }}</th>
            <th>{{ translate('Reply') }}</th>
            <th>{{ translate('Seen') }}</th>
            <th>{{ translate('Created_at') }} </th>
        </thead>
        <tbody>
        @foreach($data as $key => $message)
            <tr>
        <td>{{ $loop->index+1}}</td>
        <td>{{ $message->name }}</td>
        <td>{{ $message->email }}</td>
        <td>{{ $message->subject }}</td>
        <td>{{ $message->message }}</td>
        <td>{{ $message->reply ?? translate('messages.N/A') }}</td>
        <td>{{ $message->seen == 0 ? translate('unseen') : translate('seen') }}</td>
        <td>{{  \App\CentralLogics\Helpers::time_date_format($message->created_at)}}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

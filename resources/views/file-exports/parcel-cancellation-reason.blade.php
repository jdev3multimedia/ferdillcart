<div class="row">
    <div class="col-lg-12 text-center ">
        <h1> {{ translate('Parcel_Cancellation_Reason_List') }}
        </h1>
    </div>
    <div class="col-lg-12">

        <table>
            <thead>
                <tr>
                    <th>{{ translate('Filter_Criteria') }}</th>
                    <th></th>
                    <th>

                        {{ translate('Search_Bar_Content') }}: {{ $data['search'] ?? translate('N/A') }}

                    </th>
                    <th> </th>
                </tr>


                <tr>
                    <th class="fs-14 text-title font-semibold top-border-table">
                        {{ translate('SL') }}
                    </th>
                    <th class="fs-14 text-title font-semibold top-border-table">
                        {{ translate('messages.reason') }}
                    </th>
                    <th class="fs-14 text-title font-semibold top-border-table">
                        {{ translate('messages.cancellation_type') }}
                    </th>
                    <th class="fs-14 text-title font-semibold top-border-table">
                        {{ translate('messages.user_type') }}
                    </th>
                    <th class="fs-14 text-title font-semibold top-border-table">
                        {{ translate('messages.status') }}
                    </th>

                </tr>

            </thead>
            <tbody>
                @foreach ($data['data'] as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td class="p-3">
                            <div class="max-w-700 fs-14 title-clr font-medium min-w-140">
                                {{ Str::limit($item->reason, 25, '...') }}
                            </div>
                        </td>
                        <td class="p-3 fs-14 title-clr font-medium min-w-140">
                            {{ translate($item->cancellation_type) }}</td>
                        <td class="p-3 fs-14 title-clr font-regular min-w-140">{{ translate($item->user_type) }}
                        </td>
                        <td class="p-3">
                            @if ($item->status == 1)
                                <span class="badge badge-soft-success fs-12">{{ translate('Active') }}</span>
                            @else
                                <span class="badge badge-soft-danger fs-12">{{ translate('Inactive') }}</span>
                            @endif
                        </td>

                    </tr>
                @endforeach



            </tbody>
        </table>
    </div>
</div>

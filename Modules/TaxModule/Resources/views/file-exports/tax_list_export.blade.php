
<div class="row">
    <div class="col-lg-12 text-center "><h1 > {{translate('Tax_List')}}
    </h1></div>
    <div class="col-lg-12">

    <table>
        <thead>
            <tr>
                <th>{{ translate('Filter_Criteria') }}</th>
                <th></th>
                <th>
                    {{ translate('Search_Bar_Content')  }}: {{ $data['search'] ?? translate('N/A') }}

                </th>
                <th> </th>
                </tr>
        <tr>
             <th class="border-0">{{ translate('sl') }}</th>
            <th class="border-0">{{ translate('messages.tax_name') }}</th>
            <th class="border-0">{{ translate('messages.tax_rate') }}</th>
            <th class="border-0 text-end">{{ translate('messages.status') }}</th>

        </thead>
        <tbody>
        @foreach($data['data'] as $key => $taxVat)
            <tr>
        <td>{{ $loop->index+1}}</td>
        <td>{{ $taxVat->name }}</td>
        <td>{{ $taxVat->tax_rate }} %</td>
        <td>{{ $taxVat->is_active == 1 ? translate('messages.Active') : translate('messages.Inactive')  }}</td>

            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>

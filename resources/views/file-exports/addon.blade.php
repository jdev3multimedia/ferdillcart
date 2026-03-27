<div class="row">
    <div class="col-lg-12 text-center ">
        <h1> {{ translate('Addon_List') }}
        </h1>
    </div>
    <div class="col-lg-12">

        <table>
            <thead>
                <tr>
                    <th>{{ translate('Filter_Criteria') }}</th>
                    <th></th>
                    <th>
                        {{ translate('Store') }}: {{ $data['store'] ?? translate('N/A') }}
                        <br>
                        {{ translate('Search_Bar_Content') }}: {{ $data['search'] ?? translate('N/A') }}

                    </th>
                    <th> </th>
                </tr>


                <tr>
                    <th>{{ translate('sl') }}</th>
                    <th>{{ translate('Addon_Name') }}</th>
                    <th>{{ translate('Price') }}</th>
                    <th>{{ translate('Store_name') }}</th>


                    @if ($data['productWiseTax'])
                        <th class="border-0 w--1">{{ translate('messages.Vat/Tax') }}</th>
                    @endif

            </thead>
            <tbody>
                @foreach ($data['data'] as $key => $addon)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ $addon->name }}</td>
                        <td>
                            {{ \App\CentralLogics\Helpers::format_currency($addon->price) }}
                        </td>
                        <td>{{ $addon?->store?->name ?? translate('N/A') }}</td>


                        @if ($data['productWiseTax'])
                            <td>
                                <span class="d-block font-size-sm text-body">

                                    @forelse ($addon?->taxVats?->pluck('tax.name', 'tax.tax_rate')->toArray() as $key => $item)
                                        <br>
                                        <span> {{ $item }} : <span class="font-bold">
                                                ({{ $key }}%)
                                            </span> </span>
                                        <br>
                                    @empty
                                        <span> {{ translate('messages.no_tax') }} </span>
                                    @endforelse
                                </span>
                            </td>
                        @endif

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

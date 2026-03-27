<div class="row">
    <div class="col-lg-12 text-center ">
        <h1> {{ translate('Category_List') }}
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
                    <th class="border-0">{{ translate('sl') }}</th>
                    <th class="border-0">{{ translate('messages.id') }}</th>
                    <th class="">{{ translate('messages.Category_Name') }}</th>
                    @if ($data['categoryWiseTax'])

                    <th class="border-0 w--1">{{ translate('messages.Vat/Tax') }}</th>
                    @endif
                    <th class="border-0 text-center">{{ translate('messages.status') }}</th>

            </thead>

            <tbody>
                @foreach ($data['data'] as $key => $category)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $category->id }}</td>
                        <td>
                            {{ Str::limit($category['name'], 20, '...') }}

                        </td>
                        @if ($data['categoryWiseTax'])

                        <td>
                            <span class="d-block font-size-sm text-body">

                                @forelse ($category?->taxVats?->pluck('tax.name', 'tax.tax_rate')->toArray() as $key => $item)
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

                        <td>{{ $category->status == 1 ? translate('messages.Active') : translate('messages.Inactive') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

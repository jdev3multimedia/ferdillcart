@if(count($combinations[0]) > 0)
    <table class="table table-borderless table--vertical-middle">
        <thead class="thead-light __bg-7">
            <tr>
                <th class="text-center border-0">
                    <span class="control-label m-0">{{translate('messages.Variant')}}</span>
                </th>
                <th class="text-center border-0">
                    <span class="control-label">{{translate('messages.Variant Price')}}</span>
                </th>
                @if($stock)
                    <th class="text-center border-0">
                        <span class="control-label text-capitalize">{{translate('messages.stock')}}</span>
                    </th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $combination)
                @if(strlen($combination['name']) > 0)
                    <tr>
                        <td class="text-center">
                            <label class="control-label m-0">{{ $combination['name'] }}</label>
                        </td>
                        <td class="error-wrapper">
                            <input type="number" name="price_{{ $combination['name'] }}" value="{{ $combination['price'] }}" min="0"
                                step="0.01" class="form-control" required>
                        </td>
                        @if ($stock)
                            <td class="error-wrapper"><input type="number" name="stock_{{ $combination['name'] }}"
                                    value="{{ $combination['stock'] }}" min="0" class="form-control" required></td>
                        @endif

                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@endif
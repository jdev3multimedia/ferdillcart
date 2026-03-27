@if(count($combinations[0]) > 0)
    <table class="table table-bordered">
        <thead class="thead-light table-nowrap">
            <tr>
                <th class="text-center border-0">
                    <span class="control-label">{{translate('messages.Variant')}}</span>
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
            @foreach ($data as $key => $combination)
                @if(strlen($combination['name']) > 0)
                    <tr>
                        <td class="text-center">
                            <label class="control-label m-0">{{ $combination['name'] }}</label>
                        </td>
                        <td>
                            <input type="number" name="price_{{ $combination['name'] }}" value="{{ $combination['price'] }}" min="0" step="0.01"
                                   class="form-control" required>
                        </td>
                        @if ($stock)
                            <td><input type="number" name="stock_{{ $combination['name'] }}" value="{{ $combination['stock'] }}" min="0"
                                       class="form-control" required></td>
                        @endif

                    </tr>
                    @endif
                @endforeach
        </tbody>
    </table>
    <script>
        update_qty();
        function update_qty()
        {
            let total_qty = 0;
            let qty_elements = $('input[name^="stock_"]');
            for(let i=0; i<qty_elements.length; i++)
            {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if(qty_elements.length > 0)
            {

                $('input[name="current_stock"]').attr("readonly", true);
                $('input[name="current_stock"]').val(total_qty);
            }
            else{
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }
        $('input[name^="stock_"]').on('keyup', function () {
            let total_qty = 0;
            let qty_elements = $('input[name^="stock_"]');
            for(let i=0; i<qty_elements.length; i++)
            {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            $('input[name="current_stock"]').val(total_qty);
        });

    </script>
@endif

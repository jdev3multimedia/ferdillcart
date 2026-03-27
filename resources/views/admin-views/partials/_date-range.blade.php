<div class="bg--secondary rounded p-20 mb-20">
                        <div class="d-flex flex-column gap-lg-4 gap-3">
                            <div>
                                <span class="mb-2 d-block title-clr fw-normal">{{ translate('messages.duration') }}</span>
                                <select name="date_range" id="date_range_type"
                                    class="custom-select custom-select-color border rounded w-100">
                                    <option value="all_time" {{ request()->input('date_range') == 'all_time' ? 'selected' : '' }}>{{ translate('messages.all_time') }}</option>
                                    <option value="this_week" {{ request()->input('date_range') == 'this_week' ? 'selected' : '' }}>{{ translate('messages.this_week') }}</option>
                                    <option value="this_month" {{ request()->input('date_range') == 'this_month' ? 'selected' : '' }}>{{ translate('messages.this_month') }}</option>
                                    <option value="this_year" {{ request()->input('date_range') == 'this_year' ? 'selected' : '' }}>{{ translate('messages.this_year') }}</option>
                                    <option value="custom" {{ request()->input('date_range') == 'custom' ? 'selected' : '' }}>
                                        {{ translate('messages.custom') }}
                                    </option>
                                </select>
                            </div>
                            <div id="date_range" class="{{ request()->input('date_range') == 'custom' ? '' : 'd-none' }}">
                                <label class="form-label">{{ translate('messages.start_date') }}</label>
                                <div class="position-relative">
                                    <i class="tio-calendar-month icon-absolute-on-right"></i>
                                    <input type="text" name="dates" value="{{ $date }}"
                                        class="form-control h-45 position-relative bg-white"
                                        placeholder="{{ translate('messages.select_date') }}">
                                </div>
                            </div>
                        </div>
                    </div>

@push('script_2')
    <script>
        $(document).ready(function () {
            var dateString = '{{ $date }}';
            if (dateString) {
                var dates = dateString.split(' - ');
                if (dates.length === 2) {
                    var start = moment(dates[0], 'MM/DD/YYYY');
                    var end = moment(dates[1], 'MM/DD/YYYY');

                    var picker = $('input[name="dates"]').data('daterangepicker');
                    if (picker) {
                        picker.setStartDate(start);
                        picker.setEndDate(end);
                    }
                }
            }

            if ($('#date_range_type').val() !== 'custom') {
                $('input[name="dates"]').prop('disabled', true);
            }

            $('#date_range_type').on('change', function () {
                if (this.value === 'custom') {
                    $('input[name="dates"]').prop('disabled', false);
                } else {
                    $('input[name="dates"]').prop('disabled', true);
                }
            });
        });
    </script>
@endpush
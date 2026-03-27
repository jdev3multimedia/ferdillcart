<div class="row">
    <div class="col-lg-12 text-center ">
        <h3>{{ translate('delivery_man_referral_and_earn_history') }}</h3>
    </div>
    <div class="col-lg-12">



        <table>
            <thead>
                <tr>
                    <th>
                        {{ translate('name')  }}- {{ $data['dm']->f_name . ' ' . $data['dm']->l_name}}
                        <br>
                        {{ translate('phone')  }}- {{ $data['dm']->phone}}
                        <br>
                        {{ translate('email')  }}- {{ $data['dm']->email}}
                        <br>
                        {{ translate('total_rating')  }}- {{ count($data['dm']->rating)}}
                        <br>
                        {{ translate('average_review')  }}-
                        {{count($data['dm']->rating) > 0 ? number_format($data['dm']->rating[0]->average, 1, '.', ' ') : 0}}

                    </th>
                    <th></th>
                    <th></th>

                    <th> </th>
                </tr>

                <tr>
                    <th>{{ translate('SL') }}</th>
                    <th>{{translate('messages.Transaction ID')}}</th>
                    <th>{{translate('messages.Date')}}</th>
                    <th>{{translate('messages.Amount')}}</th>
                    <th>{{translate('messages.Reference')}}</th>

            </thead>
            <tbody>
                @foreach($data['histories'] as $key => $referralEarning)
                    <tr>
                                    <td class="text-center">{{ $key + 1}}</td>
                                    <td>
                                        <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                            {{ $referralEarning->transaction_id }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-wrap line--limit-1  max-w--220px min-w-160 text-title">
                                            {{ \App\CentralLogics\Helpers::date_format($referralEarning->created_at) }}
                                        </div>
                                         @if ($referralEarning->refer_type == 'referrerBonus')
                                            <div>
                                                <span class="text--title">({{ translate('messages.Referral_Bonus') }})</span>
                                            </div>
                                            @endif
                                    </td>
                                    <td>
                                        <div class="text-center text-title">
                                            {{ \App\CentralLogics\Helpers::format_currency($referralEarning->amount) }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $referralEarning->reference ?? translate('N/A') }}
                                    </td>
                                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

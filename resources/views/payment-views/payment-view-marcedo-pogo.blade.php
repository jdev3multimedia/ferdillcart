<?php
    $payerInformation = json_decode($data['payer_information'], true);
    $additionalData = json_decode($data['additional_data'] ?? '', true);
?>

@extends('payment-views.layouts.master')

@push('script')
    <title>{{ translate('MercadoPago_Payment') }} - {{ $additionalData['business_name'] ?? '' }}</title>
    <link rel="shortcut icon" href="{{ $additionalData['business_logo'] ?? '' }}" type="image/x-icon">
    <script src="https://sdk.mercadopago.com/js/v2"></script>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center align-items-center py-5 min-vh-100">
            <div class="col-12 col-md-8 col-lg-6">
                <div id="cardPaymentBrick_container"></div>
            </div>
        </div>
    </div>
    <script>
        const mpLocales = {
            argentina: 'es-AR',
            brasil: 'pt-BR',
            mexico: 'es-MX',
            uruguay: 'es-UY',
            colombia: 'es-CO',
            chile: 'es-CL',
            peru: 'es-PE'
        };

        const selectedCountry = "{{ $config?->supported_country ?? 'argentina' }}";
        const selectedLocale = mpLocales[selectedCountry] ?? 'en-US';

        const mp = new MercadoPago('{{ $config->public_key }}', {
            locale: selectedLocale
        });
        const bricksBuilder = mp.bricks();
        const renderCardPaymentBrick = async (bricksBuilder) => {
            const settings = {
                initialization: {
                    amount: {{$data->payment_amount}},
                    payer: {
                        email: "",
                    },
                },
                customization: {
                    visual: {
                        style: {
                            theme: 'bootstrap', // | 'dark' | 'bootstrap' | 'flat'
                            customVariables: {
                            },
                        },
                    },
                    paymentMethods: {
                        maxInstallments: 1,
                    },
                },
                callbacks: {
                    onReady: () => {
                        // callback llamado cuando Brick esté listo
                    },
                    onSubmit: (cardFormData) => {
                        return new Promise((resolve, reject) => {
                            fetch("{{ route('mercadopago.make_payment', ['payment_id' => $data->id]) }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify(cardFormData)
                            })
                                .then((response) => response.json())
                                .then((result) => {
                                    // recibir el resultado del pago
                                    console.log(result);
                                    if (result.status === 'success') {
                                        // alert("Pago aprobado!"); // show success
                                        window.location.href = "{!! route('mercadopago.callback', ['status' => 'success', 'payment_id' => $data['id']]) !!}";
                                    } else {
                                        // alert("Pago fallido!");
                                        window.location.href = "{!! route('mercadopago.callback', ['status' => 'fail', 'payment_id' => $data['id']]) !!}";
                                    }
                                    resolve();
                                })
                                .catch((error) => {
                                    console.error("MercadoPago Brick Error:", error);
                                    alert("Ocurrió un error. Intente nuevamente.");
                                    reject();
                                })
                        });
                    },
                    onError: (error) => {
                        // callback llamado para todos los casos de error de Brick
                    },
                },
            };
            window.cardPaymentBrickController = await bricksBuilder.create('cardPayment', 'cardPaymentBrick_container', settings);
        };
        renderCardPaymentBrick(bricksBuilder);
    </script>
@endsection


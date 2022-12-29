@extends('app')

@section('content')
    <h2 class="mt-5">Order Payment with Paypal</h2>
    <div class="card mt-3 shadow-sm">
        <div class="card-body text-center">
            <form class="w-50 m-auto" id="form">
                @csrf
                <div class="alert alert-danger text-start" role="alert" id="error-msg" style="display: none;"></div>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" placeholder="Price" name="amount">
                    <select class="form-select" name="currency" id="currency-select">
                        @foreach(config('paypal.currency') as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="paypal-buttons" class="w-50 m-auto"></div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
            integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"
            integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('document').ready(() => {
            renderPaypalButtons();
        });

        $('#currency-select').change(function (e) {
            renderPaypalButtons($(this).val());
        });

        function renderPaypalButtons(currency = 'USD') {
            let url = 'https://www.paypal.com/sdk/js?client-id=' + "{{ config('paypal.client_id') }}" + '&currency=' + currency;
            $.ajaxSetup({cache: true});
            $.getScript(url, () => {
                paypal.Buttons({
                    env: 'sandbox',
                    style: {shape: 'pill'},

                    onClick: (data, actions) => {
                        return fetch('{{ route('order.validate') }}', {
                            credentials: "same-origin",
                            method: 'post',
                            body: new FormData(document.getElementById('form'))
                        }).then(function (res) {
                            return res.json();
                        }).then(function (resData) {
                            if (resData.success === true) {
                                return actions.resolve();
                            }
                            let errEle = $('#error-msg');
                            errEle.empty();
                            let ulEle = $('<ul></ul>');
                            Object.values(resData.data).forEach(v => {
                                ulEle.append('<li>' + v + '</li>');
                            })
                            errEle.append(ulEle).show();

                            return actions.reject();
                        });
                    },

                    createOrder: (data, actions) => {
                        $('#error-msg').show();
                        return fetch('{{ route('order.create') }}', {
                            credentials: "same-origin",
                            method: 'post',
                            body: new FormData(document.getElementById('form'))
                        }).then(function (res) {
                            return res.json();
                        }).then(function (resData) {
                            return resData.links[1].href.split('EC-')[1];
                        });
                    },

                    onApprove: (data, actions) => {
                        return fetch('{{ route('order.process') }}', {
                            method: 'post',
                            headers: {
                                'Content-Type': 'application/json',
                                "X-CSRF-Token": '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                orderId: data.orderID,
                                paymentId: data.paymentID,
                                payerId: data.payerID
                            })
                        }).then(function (res) {
                            return res.json();
                        }).then(function (orderData) {
                            var errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                            if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                                return actions.restart(); // Recoverable state, per:
                                // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                            }

                            if (errorDetail) {
                                var msg = 'Sorry, your transaction could not be processed.';
                                if (errorDetail.description) msg += '\n\n' + errorDetail.description;
                                if (orderData.debug_id) msg += ' (' + orderData.debug_id + ')';
                                return alert(msg);
                            }
                            // Successful capture! For demo purposes:
                            console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                            actions.redirect('{{ route('order.complete') }}' + '?order_id=' + data.orderID);
                        });
                    }
                }).render('#paypal-buttons');
                $.ajaxSetup({cache: false});
            })
        }
    </script>
@endsection

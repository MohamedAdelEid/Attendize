@php
    $checkoutId = request()->get('hyperpay_checkout') ?? session()->get('hyperpay_checkout_id_' . $event->id);
    $testMode = $account_payment_gateway->config['testMode'] ?? false;
    $widgetUrl = $testMode ? 'https://eu-test.oppwa.com/v1/paymentWidgets.js' : 'https://eu-prod.oppwa.com/v1/paymentWidgets.js';
    $entityId = $account_payment_gateway->config['entityId'] ?? '';
@endphp

@if($checkoutId)
    <!-- HyperPay Payment Widget -->
    <div id="hyperpay-payment-container">
        <form action="{{ route('showEventCheckoutPaymentReturn', ['event_id' => $event->id, 'is_payment_successful' => 1]) }}" class="paymentWidgets" data-brands="VISA MASTER AMEX">
            <!-- HyperPay widget will inject payment form fields here -->
        </form>
    </div>
    
    <script src="{{ $widgetUrl }}?checkoutId={{ $checkoutId }}"></script>
    <script>
        // HyperPay widget handles the form submission automatically
        // After payment, it will redirect to the form's action URL (shopperResultUrl)
    </script>
@else
    <!-- Initial payment form - will create checkout session -->
    <form class="online_payment" action="{{ route('postCreateOrder', ['event_id' => $event->id]) }}" method="post" id="hyperpay-payment-form">
        <div class="form-row">
            <label>
                @lang("Public_ViewEvent.payment_information")
            </label>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> سيتم تحميل نموذج الدفع بعد النقر على الزر أدناه
            </div>
        </div>
        {!! Form::token() !!}
        <input class="btn btn-lg btn-success card-submit" style="width:100%;" type="submit" value="@lang('Public_ViewEvent.complete_payment')">
    </form>
@endif

<style type="text/css">
    #hyperpay-payment-container {
        margin: 20px 0;
    }
    
    .paymentWidgets {
        margin-bottom: 20px;
    }
    
    .paymentWidgets input[type="text"],
    .paymentWidgets input[type="tel"],
    .paymentWidgets select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        margin-bottom: 15px;
    }
</style>


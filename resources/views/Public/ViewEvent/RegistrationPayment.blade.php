@extends('Public.ViewEvent.Layouts.EventPage')

@section('head')
    {!! HTML::style(config('attendize.cdn_url_static_assets') . '/assets/stylesheet/frontend.css') !!}
    {!! HTML::style(config('attendize.cdn_url_static_assets') . '/assets/stylesheet/static.css') !!}
    <style>
        .event-header {
            margin-bottom: 20px;
        }

        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }

        .order-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .order-summary h3 {
            margin-top: 0;
        }

        .order-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .order-summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 1.2em;
        }
    </style>
@stop

@section('content')
    <div class="event-header">
        <h1>{{ $event->title }}</h1>
        <h2>الدفع للتسجيل</h2>
    </div>

    <div class="payment-container">
        @if ($payment_failed)
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> فشلت عملية الدفع. يرجى المحاولة مرة أخرى.
            </div>
        @endif

        <div class="order-summary">
            <h3>ملخص الطلب</h3>
            <div class="order-summary-item">
                <span>المؤتمر:</span>
                <span>{{ $registration_order['conference_id'] ? 'تم الاختيار' : '-' }}</span>
            </div>
            <div class="order-summary-item">
                <span>المهنة:</span>
                <span>{{ $registration_order['profession_id'] ? 'تم الاختيار' : '-' }}</span>
            </div>
            <div class="order-summary-item">
                <span>المبلغ الإجمالي:</span>
                <span>{{ number_format($order_total, 2) }} {{ $event->currency->code ?? 'SAR' }}</span>
            </div>
        </div>

        @php
            $checkoutId = isset($checkout_id)
                ? $checkout_id
                : request()->get('hyperpay_checkout') ?? session()->get('hyperpay_checkout_id_' . $event->id);
        @endphp

        @if ($payment_gateway && $checkoutId)
            @php
                $testMode = $account_payment_gateway->config['testMode'] ?? false;
                $widgetUrl = $testMode
                    ? 'https://eu-test.oppwa.com/v1/paymentWidgets.js'
                    : 'https://eu-prod.oppwa.com/v1/paymentWidgets.js';
                $entityId = $account_payment_gateway->config['entityId'] ?? '';
            @endphp

            <div id="hyperpay-payment-container">
                <form action="{{ route('showRegistrationPaymentReturn', ['event_id' => $event->id]) }}"
                    class="paymentWidgets" data-brands="VISA MASTER AMEX">
                </form>
            </div>
            <script src="{{ $widgetUrl }}?checkoutId={{ $checkoutId }}"></script>
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
        @else
            <div class="alert alert-warning">
                بوابة الدفع غير مدعومة للتسجيل أو لم يتم إنشاء جلسة الدفع.
            </div>
        @endif
    </div>
@stop

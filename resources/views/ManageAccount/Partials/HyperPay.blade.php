<section class="payment_gateway_options" id="gateway_{{ $payment_gateway['id'] }}">
    <h4>إعدادات HyperPay</h4>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <strong>ملاحظة:</strong> أدخل بيانات الدخول من حسابك في HyperPay. يمكنك الحصول عليها من لوحة تحكم
                HyperPay.
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('hyperpay[accessToken]', 'Access Token (رمز الوصول)', ['class' => 'control-label']) !!}
                {!! Form::text('hyperpay[accessToken]', $account->getGatewayConfigVal($payment_gateway['id'], 'accessToken'), [
                    'class' => 'form-control',
                    'placeholder' => 'OGE4Mjk0MTc2N...',
                ]) !!}
                <small class="form-text text-muted">رمز الوصول من HyperPay</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('hyperpay[entityId]', 'Entity ID (Visa/Mastercard)', ['class' => 'control-label']) !!}
                {!! Form::text('hyperpay[entityId]', $account->getGatewayConfigVal($payment_gateway['id'], 'entityId'), [
                    'class' => 'form-control',
                    'placeholder' => '8a829417...',
                ]) !!}
                <small class="form-text text-muted">معرف الكيان للفيزا والماستركارد</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('hyperpay[entityIdMada]', 'Entity ID (Mada)', ['class' => 'control-label']) !!}
                {!! Form::text('hyperpay[entityIdMada]', $account->getGatewayConfigVal($payment_gateway['id'], 'entityIdMada'), [
                    'class' => 'form-control',
                    'placeholder' => '8ac7a4ca80a5...',
                ]) !!}
                <small class="form-text text-muted">معرف الكيان لمدى (اختياري - إذا كان مختلف عن
                    Visa/Mastercard)</small>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <div class="custom-checkbox">
                    <input type="checkbox" name="hyperpay[testMode]" value="1" id="hyperpay_test_mode"
                        {{ $account->getGatewayConfigVal($payment_gateway['id'], 'testMode') ? 'checked' : '' }}>
                    <label for="hyperpay_test_mode">وضع الاختبار (Test Mode)</label>
                </div>
                <small class="form-text text-muted">فعّل هذا الخيار للاستخدام في بيئة الاختبار</small>
            </div>
        </div>
    </div>
</section>

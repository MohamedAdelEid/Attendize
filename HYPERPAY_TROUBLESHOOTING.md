# حل مشاكل HyperPay

## طريقة التحقق من الأخطاء

### 1. من المتصفح (Console)
- اضغط F12
- اذهب لتبويب Console
- جرب عملية التسجيل
- راقب الأخطاء والرسائل

### 2. من Laravel Log
افتح ملف:
```
storage/logs/laravel.log
```
وابحث عن:
- `HyperPay Request`
- `HyperPay Response`
- `HyperPay Error`

## الأخطاء الشائعة والحلول

### الخطأ: "Access Token غير موجود في الإعدادات"
**السبب**: لم يتم حفظ Access Token بشكل صحيح
**الحل**: 
1. تأكد من حفظ البيانات في لوحة التحكم
2. تأكد من أن الحقل `hyperpay[accessToken]` مملوء

### الخطأ: "Entity ID غير موجود في الإعدادات"
**السبب**: لم يتم حفظ Entity ID بشكل صحيح
**الحل**: 
1. تأكد من حفظ Entity ID في لوحة التحكم
2. تأكد من أن الحقل `hyperpay[entityId]` مملوء

### الخطأ: "HTTP 401" أو "Unauthorized"
**السبب**: Access Token غير صحيح
**الحل**: 
1. تأكد من نسخ Access Token كاملاً بدون مسافات
2. تأكد من أن Access Token صحيح من HyperPay

### الخطأ: "HTTP 400" أو "Bad Request"
**السبب**: Entity ID غير صحيح أو بيانات ناقصة
**الحل**: 
1. تأكد من Entity ID صحيح
2. تأكد من أن جميع الحقول مملوءة

### الخطأ: "فشل في إنشاء checkout ID"
**السبب**: HyperPay رفض الطلب
**الحل**: 
1. تحقق من الـ Response في Log
2. تأكد من أن Test Mode مفعل إذا كنت في بيئة الاختبار
3. تأكد من أن Entity ID و Access Token صحيحين

## التحقق من الإعدادات

### من قاعدة البيانات:
```sql
SELECT * FROM account_payment_gateways 
WHERE payment_gateway_id = (SELECT id FROM payment_gateways WHERE name = 'HyperPay');
```

تحقق من:
- `config` يحتوي على `accessToken`
- `config` يحتوي على `entityId`
- `config` يحتوي على `testMode: true` (في بيئة الاختبار)

## Test Mode vs Live Mode

- **Test Mode**: استخدم `https://test.oppwa.com/v1`
- **Live Mode**: استخدم `https://oppwa.com/v1`

تأكد من:
- في `.env`: `ENABLE_TEST_PAYMENTS=true`
- في لوحة التحكم: Test Mode مفعل ✓

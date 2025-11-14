@component('mail::message')
# Payment Received - Thank You!

Hi {{ $userName }},

We've successfully received your payment for the **{{ $planName }}** subscription plan.

## Payment Details

@component('mail::table')
| Description | Details |
|:------------|:--------|
| **Plan** | {{ $planName }} |
| **Amount** | {{ $currency }} {{ $amount }} |
| **Reference** | {{ $reference }} |
| **Date** | {{ $paymentDate }} |
| **Status** | ✅ Confirmed |
@endcomponent

Your subscription has been activated and you now have full access to all {{ $planName }} features.

@component('mail::button', ['url' => $dashboardUrl])
Access Your Dashboard
@endcomponent

## Receipt

Keep this email as your payment receipt. You can also view your payment history in your account dashboard under Subscription → History.

## Questions?

If you have any questions about your payment or subscription, please don't hesitate to contact our support team.

Thank you for your business!

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent

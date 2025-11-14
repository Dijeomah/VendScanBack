@component('mail::message')
# Your Subscription is Expiring Soon

Hi {{ $userName }},

This is a friendly reminder that your **{{ $planName }}** subscription will expire in **{{ $daysUntilExpiry }} days** on {{ $expiryDate }}.

## What Happens When Your Subscription Expires?

If your subscription expires, you'll be automatically downgraded to the Free plan, which includes:
- 1 Business
- 10 Tables
- 10 Servers
- 50 Menu Items

@component('mail::panel')
⚠️ **Important:** Any resources exceeding the Free plan limits will become inactive until you renew your subscription.
@endcomponent

## Renew Now

Don't lose access to your premium features! Renew your subscription today to continue enjoying:
- Unlimited resources
- Advanced analytics
- Priority support
- Custom branding

@component('mail::button', ['url' => $renewUrl])
Renew Subscription
@endcomponent

## Need Help?

If you have any questions or need assistance with renewal, please contact our support team. We're here to help!

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent

@component('mail::message')
# Subscription Upgraded Successfully!

Hi {{ $userName }},

Congratulations! Your subscription has been upgraded from **{{ $oldPlanName }}** to **{{ $planName }}** plan.

## Your New Benefits

@component('mail::panel')
**{{ $planName }} Plan** - ${{ number_format($planPrice, 2) }}/month

✅ {{ $features['businesses'] === 'Unlimited' ? 'Unlimited' : $features['businesses'] }} Businesses
✅ {{ $features['tables'] === 'Unlimited' ? 'Unlimited' : $features['tables'] }} Tables
✅ {{ $features['servers'] === 'Unlimited' ? 'Unlimited' : $features['servers'] }} Servers
✅ {{ $features['items'] === 'Unlimited' ? 'Unlimited' : $features['items'] }} Menu Items
@if($planName !== 'Free')
✅ Advanced Analytics
✅ Custom Branding
✅ Priority Support
@endif
@endcomponent

Your upgraded limits are now active and ready to use!

@component('mail::button', ['url' => $dashboardUrl])
View Dashboard
@endcomponent

## What's Next?

Start taking advantage of your new limits:
- Create more businesses
- Add more tables
- Expand your team with more servers
- Build a comprehensive menu

Thanks for choosing VendScan!

Best regards,<br>
The {{ config('app.name') }} Team
@endcomponent

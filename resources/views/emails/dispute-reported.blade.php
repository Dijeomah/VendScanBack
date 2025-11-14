@component('mail::message')
# Payment Dispute Reported

Hi {{ $userName }},

We wanted to inform you that a dispute has been reported regarding one of your payment transactions.

## Dispute Details

**Dispute Type:** {{ $disputeType }}
**Payment Reference:** {{ $disputeReference }}
**Disputed Amount:** ₦{{ $disputedAmount }}
**Reported Date:** {{ $reportedDate }}

## Reason for Dispute

{{ $reason }}

## What Happens Next?

Our team will investigate this dispute and verify the transaction details with our payment processor. This process typically takes 3-5 business days.

You'll receive an email notification once the dispute has been reviewed and resolved.

@component('mail::panel')
ℹ️ **Note:** Your account remains active during the investigation. If you have any questions or additional information to provide, please contact our support team.
@endcomponent

@component('mail::button', ['url' => $supportUrl])
Contact Support
@endcomponent

Thank you for your patience and understanding.

Best regards,<br>
The {{ config('app.name') }} Support Team
@endcomponent

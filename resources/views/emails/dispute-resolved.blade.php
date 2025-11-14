@component('mail::message')
# Payment Dispute {{ $statusText }}

Hi {{ $userName }},

We're writing to inform you that the payment dispute for reference **{{ $disputeReference }}** has been {{ strtolower($statusText) }}.

## Resolution Details

**Status:** {{ $statusText }}
**Resolved Date:** {{ $resolvedDate }}

@if($disputeStatus === 'resolved')
@component('mail::panel')
✅ **Good News!** Your dispute has been resolved in your favor.
@if($refundAmount)

**Refund Amount:** ₦{{ $refundAmount }}

The refund will be processed within 3-5 business days and will appear in your original payment method.
@endif
@endcomponent
@else
@component('mail::panel')
❌ **Dispute Rejected:** After careful review, we were unable to validate this dispute.
@endcomponent
@endif

## Resolution Notes

{{ $resolutionNotes }}

## Need More Help?

If you have any questions about this resolution or need further assistance, our support team is here to help.

@component('mail::button', ['url' => $supportUrl])
Contact Support
@endcomponent

Thank you for your understanding and patience throughout this process.

Best regards,<br>
The {{ config('app.name') }} Support Team
@endcomponent

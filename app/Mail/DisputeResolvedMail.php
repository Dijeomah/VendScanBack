<?php

namespace App\Mail;

use App\Models\PaymentDispute;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisputeResolvedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $dispute;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, PaymentDispute $dispute)
    {
        $this->user = $user;
        $this->dispute = $dispute;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $statusText = $this->dispute->status === 'resolved' ? 'Resolved' : 'Rejected';

        return $this->subject('Payment Dispute ' . $statusText . ' - ' . $this->dispute->payment->reference)
            ->markdown('emails.dispute-resolved')
            ->with([
                'userName' => $this->user->first_name,
                'disputeReference' => $this->dispute->payment->reference,
                'disputeStatus' => $this->dispute->status,
                'statusText' => $statusText,
                'resolutionNotes' => $this->dispute->resolution_notes,
                'refundAmount' => $this->dispute->refund_amount ? number_format($this->dispute->refund_amount, 2) : null,
                'resolvedDate' => $this->dispute->resolved_at->format('F j, Y g:i A'),
                'supportUrl' => config('app.frontend_url') . '/vendor/support',
            ]);
    }
}

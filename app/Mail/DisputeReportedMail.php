<?php

namespace App\Mail;

use App\Models\PaymentDispute;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DisputeReportedMail extends Mailable
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
        $disputeTypes = [
            'non_receipt' => 'Payment Not Received',
            'duplicate' => 'Duplicate Payment',
            'unauthorized' => 'Unauthorized Payment',
            'amount_mismatch' => 'Amount Mismatch',
            'refund_request' => 'Refund Request',
            'other' => 'Other Issue',
        ];

        return $this->subject('Payment Dispute Reported - ' . $this->dispute->payment->reference)
            ->markdown('emails.dispute-reported')
            ->with([
                'userName' => $this->user->first_name,
                'disputeType' => $disputeTypes[$this->dispute->dispute_type] ?? 'Payment Dispute',
                'disputeReference' => $this->dispute->payment->reference,
                'disputedAmount' => number_format($this->dispute->disputed_amount, 2),
                'reason' => $this->dispute->reason,
                'reportedDate' => $this->dispute->reported_at->format('F j, Y g:i A'),
                'supportUrl' => config('app.frontend_url') . '/vendor/support',
            ]);
    }
}

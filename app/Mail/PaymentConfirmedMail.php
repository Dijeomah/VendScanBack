<?php

namespace App\Mail;

use App\Models\User;
use App\Models\SubscriptionPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $payment;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param SubscriptionPayment $payment
     * @return void
     */
    public function __construct(User $user, SubscriptionPayment $payment)
    {
        $this->user = $user;
        $this->payment = $payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Payment Confirmed - ' . $this->payment->subscription_plan->name . ' Plan')
            ->markdown('emails.payment-confirmed')
            ->with([
                'userName' => $this->user->first_name,
                'planName' => $this->payment->subscription_plan->name,
                'amount' => number_format($this->payment->amount, 2),
                'currency' => $this->payment->currency,
                'reference' => $this->payment->reference,
                'paymentDate' => $this->payment->paid_at->format('F j, Y g:i A'),
                'dashboardUrl' => config('app.frontend_url') . '/vendor/dashboard',
            ]);
    }
}

<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $subscription;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Subscription $subscription
     * @return void
     */
    public function __construct(User $user, Subscription $subscription)
    {
        $this->user = $user;
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $daysUntilExpiry = now()->diffInDays($this->subscription->expires_at);

        return $this->subject('Your Subscription is Expiring Soon')
            ->markdown('emails.subscription-expiring')
            ->with([
                'userName' => $this->user->first_name,
                'planName' => $this->subscription->plan->name,
                'expiryDate' => $this->subscription->expires_at->format('F j, Y'),
                'daysUntilExpiry' => $daysUntilExpiry,
                'renewUrl' => config('app.frontend_url') . '/vendor/subscription',
                'dashboardUrl' => config('app.frontend_url') . '/vendor/dashboard',
            ]);
    }
}

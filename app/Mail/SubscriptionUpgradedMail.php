<?php

namespace App\Mail;

use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionUpgradedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $plan;
    public $oldPlan;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param SubscriptionPlan $plan
     * @param SubscriptionPlan|null $oldPlan
     * @return void
     */
    public function __construct(User $user, SubscriptionPlan $plan, ?SubscriptionPlan $oldPlan = null)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->oldPlan = $oldPlan;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Subscription Upgraded - ' . $this->plan->name . ' Plan')
            ->markdown('emails.subscription-upgraded')
            ->with([
                'userName' => $this->user->first_name,
                'planName' => $this->plan->name,
                'planPrice' => $this->plan->price,
                'oldPlanName' => $this->oldPlan ? $this->oldPlan->name : 'Free',
                'features' => [
                    'businesses' => $this->plan->getLimit('businesses') ?? 'Unlimited',
                    'tables' => $this->plan->getLimit('tables') ?? 'Unlimited',
                    'servers' => $this->plan->getLimit('servers') ?? 'Unlimited',
                    'items' => $this->plan->getLimit('items') ?? 'Unlimited',
                ],
                'dashboardUrl' => config('app.frontend_url') . '/vendor/dashboard',
            ]);
    }
}

<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Welcome to VendScan!')
            ->markdown('emails.welcome')
            ->with([
                'userName' => $this->user->first_name,
                'loginUrl' => config('app.frontend_url') . '/login',
                'dashboardUrl' => config('app.frontend_url') . '/vendor/dashboard',
            ]);
    }
}

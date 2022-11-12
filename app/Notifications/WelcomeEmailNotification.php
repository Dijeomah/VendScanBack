<?php

    namespace App\Notifications;

    use Illuminate\Bus\Queueable;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Notifications\Messages\MailMessage;
    use Illuminate\Notifications\Notification;

    class WelcomeEmailNotification extends Notification
    {
        use Queueable;

        public $vendor;
        public $base_url;

        /**
         * Create a new notification instance.
         *
         * @return void
         */
        public function __construct($vendor)
        {
            $this->vendor = $vendor;
//        $this->base_url = env('APP_URL').'/auth/driver/verify?token='.$this->vendor->id;
        }

        /**
         * Get the notification's delivery channels.
         *
         * @param mixed $notifiable
         * @return array
         */
        public function via($notifiable)
        {
            return ['mail'];
        }

        /**
         * Get the mail representation of the notification.
         *
         * @param mixed $notifiable
         * @return \Illuminate\Notifications\Messages\MailMessage
         */
        public function toMail($notifiable)
        {
            return (new MailMessage)
                ->greeting('Hello, '.$this->vendor->last_name.'!')
                ->line('Welcome to Simple Business QR!, You are receiving this email because you created an account on our platform.')
                ->action('Update and Verify your account', url($this->base_url))
                ->line('If you did not make this request, kindly ignore as no further action is required.');
        }

        /**
         * Get the array representation of the notification.
         *
         * @param mixed $notifiable
         * @return array
         */
        public function toArray($notifiable)
        {
            return [
                //
            ];
        }
    }

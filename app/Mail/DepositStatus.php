<?php

namespace App\Mail;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DepositStatus extends Mailable
{
    use Queueable, SerializesModels;
    public $deposit, $subject, $user;
    public $foramin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Deposit $deposit, User $user, $subject, $foramin = false)
    {
        $this->deposit = $deposit;
        $this->user = $user;
        $this->foramin = $foramin;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $settings = \App\Models\Settings::find(1);
        
        return $this->markdown('emails.success-deposit')
            ->subject($this->subject)
            ->from($settings->emailfrom ?? config('mail.from.address'), $settings->emailfromname ?? $settings->site_name)
            ->replyTo($settings->contact_email ?? config('mail.from.address'))
            ->with([
                'settings' => $settings,
                'deposit' => $this->deposit,
                'user' => $this->user,
                'foramin' => $this->foramin
            ])
            ->withSwiftMessage(function ($message) use ($settings) {
                $headers = $message->getHeaders();
                $headers->addTextHeader('X-Mailer', 'SecureBank Pro Mail System v3.5');
                $headers->addTextHeader('X-Priority', '3');
                $headers->addTextHeader('Importance', 'Normal');
                $headers->addTextHeader('X-MSMail-Priority', 'Normal');
                $headers->addTextHeader('X-Transaction-Type', 'Account-Notification');
                $headers->addTextHeader('List-Unsubscribe', '<mailto:' . ($settings->contact_email ?? 'unsubscribe@' . parse_url($settings->site_address ?? '', PHP_URL_HOST)) . '>');
            });
    }
}

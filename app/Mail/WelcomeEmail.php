<?php

namespace App\Mail;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;

    /**
     * Create a new message instance.
     *
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
        $settings = null;

        try {
            $settings = Settings::find(1);
        } catch (\Throwable $exception) {
            Log::error('Failed to load settings while building welcome email.', [
                'user_id' => $this->user->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if (!$settings) {
            $settings = new Settings();
        }

        $settings->site_name = $settings->site_name ?? config('app.name', 'SecureBank Pro');
        $settings->site_address = $settings->site_address ?? config('app.url');
        $settings->contact_email = $settings->contact_email ?? config('mail.from.address');
        $settings->emailfrom = $settings->emailfrom ?? config('mail.from.address');
        $settings->emailfromname = $settings->emailfromname ?? $settings->site_name;

        return $this->markdown('emails.welcome')
            ->subject("Welcome to {$settings->site_name} - Account Confirmation")
            ->from($settings->emailfrom ?? config('mail.from.address'), $settings->emailfromname ?? $settings->site_name)
            ->replyTo($settings->contact_email ?? config('mail.from.address'))
            ->with([
                'settings' => $settings,
                'user' => $this->user
            ])
            ->withSwiftMessage(function ($message) use ($settings) {
                $headers = $message->getHeaders();
                // Add custom headers to improve deliverability
                $headers->addTextHeader('X-Mailer', 'SecureBank Pro Mail System v3.5');
                $headers->addTextHeader('X-Priority', '3');
                $headers->addTextHeader('Importance', 'Normal');
                $headers->addTextHeader('X-MSMail-Priority', 'Normal');
                $headers->addTextHeader('List-Unsubscribe', '<mailto:' . ($settings->contact_email ?? 'unsubscribe@' . parse_url($settings->site_address ?? '', PHP_URL_HOST)) . '>');
            });
    }
}

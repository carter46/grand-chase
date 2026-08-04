<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use App\Models\Settings;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
   
    /**
     * Send the email verification notification.
     *
     * @return void
     */

    public function sendEmailVerificationNotification()
    {
        $settings = Settings::where('id', 1)->first();

        if ($settings->enable_verification == 'true') {
            $this->notify(new VerifyEmail);
        }
        
    }
    
    /**
     * Privileged / secret columns blocked from mass assignment.
     * System code must use forceFill() or Query Builder for these.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'account_bal',
        'roi',
        'bonus',
        'ref_bonus',
        'bonus_released',
        'signup_bonus',
        'account_status',
        'status',
        'trade_mode',
        'auto_trade',
        'pin',
        'pinstatus',
        'code1',
        'code2',
        'code3',
        'codestatus',
        'withdrawotp',
        'act_session',
        'signalstatus',
        'assign_to',
        'plan',
        'user_plan',
        'entered_at',
        'activated_at',
        'last_growth',
        'allowtransfer',
        'transferaction',
        'limit',
        'amount',
        'kyc_id',
        'irs_filing_id',
        'acnt_type_active',
        'cstatus',
        'action',
        'usernumber',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
        'current_team_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'pin',
        'code1',
        'code2',
        'code3',
        'withdrawotp',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function dp(){
    	return $this->hasMany(Deposit::class, 'user');
    }

    public function wd(){
    	return $this->hasMany(Withdrawal::class, 'user');
    }

    public function tuser(){
    	return $this->belongsTo(Admin::class, 'assign_to');
    }
    
    public function dplan(){
    	return $this->belongsTo(Plans::class, 'plan');
    }

    public function plans(){
        return $this->hasMany(User_plans::class,'user', 'id');
    }

    public static function search($search): \Illuminate\Database\Eloquent\Builder
    {
        return empty($search) ? static::query()
        : static::query()->where('id', 'like', '%'.$search.'%')
        ->orWhere('name', 'like', '%'.$search.'%')
        ->orWhere('username', 'like', '%'.$search.'%')
        ->orWhere('email', 'like', '%'.$search.'%');
    }
	
    /**
     * Get the cards for the user.
     */
    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    /**
     * Get the notifications associated with the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the count of unread notifications for this user.
     * 
     * @return int
     */
    public function unreadNotificationsCount()
    {
        return $this->notifications()->where('is_read', false)->count();
    }
}

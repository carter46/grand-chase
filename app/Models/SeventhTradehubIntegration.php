<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 7th Trade Hub integration registry row (context primary key).
 *
 * @property string $context
 * @property bool|int $enabled
 * @property string|null $integration_id
 * @property string|null $client_id
 * @property string|null $client_secret_enc
 * @property string|null $webhook_secret_enc
 * @property string|null $expected_user_email
 * @property string|null $expected_admin_email
 * @property string|null $updated_at
 * @property int|null $updated_by
 */
class SeventhTradehubIntegration extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'seventh_tradehub_integrations';

    protected $primaryKey = 'context';

    protected $keyType = 'string';

    protected $guarded = [];

    protected $hidden = [
        'client_secret_enc',
        'webhook_secret_enc',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscription()
    {
        return $this->hasOne(SeventhTradehubSubscription::class, 'integration_id', 'integration_id');
    }
}

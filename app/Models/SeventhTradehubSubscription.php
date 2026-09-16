<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Local Hub subscription / shutdown state for an owned integration.
 *
 * @property string $integration_id
 * @property int|null $tool_id
 * @property string|null $public_id
 * @property string $status
 * @property string|null $expires_at
 * @property string|null $updated_at
 * @property string|null $last_sync_at
 */
class SeventhTradehubSubscription extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $table = 'seventh_tradehub_subscriptions';

    protected $primaryKey = 'integration_id';

    protected $keyType = 'string';

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function integration()
    {
        return $this->belongsTo(SeventhTradehubIntegration::class, 'integration_id', 'integration_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Merchant connection log for Hub protocol traffic.
 *
 * @property int $id
 * @property string $created_at
 * @property string $direction
 * @property string $event
 * @property bool|int $ok
 * @property int|null $http_status
 * @property string|null $error_code
 * @property string|null $integration_id
 * @property string|null $context
 * @property string|null $host
 * @property string $message
 * @property string|null $detail
 */
class SeventhTradehubConnectionLog extends Model
{
    public $timestamps = false;

    protected $table = 'seventh_tradehub_connection_logs';

    protected $guarded = [];
}

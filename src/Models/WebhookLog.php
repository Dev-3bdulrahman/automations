<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $table = 'auto_webhook_logs';

    protected $fillable = [
        'webhook_endpoint_id',
        'payload',
        'response_code',
        'response_body',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'json',
        'processed_at' => 'datetime',
    ];

    public function webhookEndpoint(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpoint::class, 'webhook_endpoint_id');
    }
}

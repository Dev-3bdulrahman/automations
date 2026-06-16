<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationCredential extends Model
{
    protected $table = 'auto_integration_credentials';

    protected $fillable = [
        'integration_id',
        'key',
        'value',
        'expires_at',
    ];

    protected $casts = [
        'value' => 'encrypted',
        'expires_at' => 'datetime',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class, 'integration_id');
    }
}

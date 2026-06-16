<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    protected $table = 'auto_webhook_endpoints';

    protected $fillable = [
        'company_id',
        'name',
        'url',
        'secret',
        'events',
        'status',
    ];

    protected $casts = [
        'events' => 'array',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}

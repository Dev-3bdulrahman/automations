<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Integration extends Model
{
    use BelongsToCompany;

    protected $table = 'auto_integrations';

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'config',
        'is_active',
        'last_synced_at',
    ];

    protected $casts = [
        'config' => 'encrypted:json',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
    ];

    public function credentials(): HasMany
    {
        return $this->hasMany(IntegrationCredential::class, 'integration_id');
    }
}

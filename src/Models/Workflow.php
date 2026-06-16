<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends Model
{
    use SoftDeletes;

    protected $table = 'auto_workflows';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'status',
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

    // ── Relations ─────────────────────────────────────────────────────────────

    public function trigger(): HasOne
    {
        return $this->hasOne(WorkflowTrigger::class, 'workflow_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(WorkflowAction::class, 'workflow_id')->orderBy('priority');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class, 'workflow_id');
    }
}

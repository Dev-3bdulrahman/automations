<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowAction extends Model
{
    protected $table = 'auto_workflow_actions';

    protected $fillable = [
        'workflow_id',
        'action_type',
        'configuration',
        'priority',
    ];

    protected $casts = [
        'configuration' => 'array',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }
}

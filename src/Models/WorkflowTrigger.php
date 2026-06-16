<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowTrigger extends Model
{
    protected $table = 'auto_workflow_triggers';

    protected $fillable = [
        'workflow_id',
        'event_type',
        'conditions',
    ];

    protected $casts = [
        'conditions' => 'array',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }
}

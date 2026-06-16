<?php

namespace Dev3bdulrahman\Automations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowLog extends Model
{
    protected $table = 'auto_workflow_logs';

    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'workflow_id',
        'trigger_event',
        'status',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }
}

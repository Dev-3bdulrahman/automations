<?php

namespace Dev3bdulrahman\Automations\Events;

use Dev3bdulrahman\Automations\Models\Workflow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkflowExecuted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Workflow $workflow,
        public array $result,
        public int $companyId,
    ) {}
}

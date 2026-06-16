<?php

namespace Dev3bdulrahman\Automations\Policies;

use App\Models\User;
use Dev3bdulrahman\Automations\Models\Workflow;

class WorkflowPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('automations.workflows.view');
    }

    public function view(User $user, Workflow $workflow): bool
    {
        return $user->can('automations.workflows.view') && $workflow->company_id === $user->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('automations.workflows.create');
    }

    public function update(User $user, Workflow $workflow): bool
    {
        return $user->can('automations.workflows.update') && $workflow->company_id === $user->company_id;
    }

    public function delete(User $user, Workflow $workflow): bool
    {
        return $user->can('automations.workflows.delete') && $workflow->company_id === $user->company_id;
    }
}

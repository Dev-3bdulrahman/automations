<?php

namespace Dev3bdulrahman\Automations\Http\Controllers\Web\Admin\Automations;

use Dev3bdulrahman\Automations\Models\Workflow;
use Dev3bdulrahman\Automations\Models\WorkflowTrigger;
use Dev3bdulrahman\Automations\Models\WorkflowAction;
use Dev3bdulrahman\Automations\Models\WorkflowLog;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Workflows extends Component
{
    use WithPagination;

    public string $search = '';

    // Modals control
    public bool $showWorkflowModal = false;
    public bool $showTriggerModal  = false;
    public bool $showActionModal   = false;
    public bool $showLogsModal     = false;

    // Workflow fields
    public ?int $workflowId    = null;
    public string $wfName      = '';
    public string $wfDesc      = '';
    public string $wfStatus    = 'active';

    // Trigger fields
    public ?int $editingWfId   = null;
    public string $triggerEvent = 'lead_created';
    public array $conditions    = []; // Array of arrays: [['field' => '', 'operator' => 'equals', 'value' => '']]

    // Action fields
    public string $actionType   = 'send_email';
    public array $actionConfig  = []; // key-values for specific action type

    // Logs view
    public array $selectedLogs  = [];

    protected function rules(): array
    {
        return [
            'wfName'   => 'required|string|max:255',
            'wfDesc'   => 'nullable|string',
            'wfStatus' => 'required|in:active,inactive',
        ];
    }

    // ── Workflow CRUD ─────────────────────────────────────────────────────────

    public function openWorkflowModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['workflowId', 'wfName', 'wfDesc', 'wfStatus']);

        if ($id) {
            $wf = Workflow::findOrFail($id);
            $this->workflowId = $wf->id;
            $this->wfName     = $wf->name;
            $this->wfDesc     = $wf->description ?? '';
            $this->wfStatus   = $wf->status;
        }

        $this->showWorkflowModal = true;
    }

    public function closeWorkflowModal(): void
    {
        $this->showWorkflowModal = false;
    }

    public function saveWorkflow(): void
    {
        $this->validate();

        $companyId = session('active_company_id', 1);

        $data = [
            'company_id'  => $companyId,
            'name'        => $this->wfName,
            'description' => $this->wfDesc,
            'status'      => $this->wfStatus,
        ];

        if ($this->workflowId) {
            Workflow::findOrFail($this->workflowId)->update($data);
        } else {
            Workflow::create($data);
        }

        $this->closeWorkflowModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('automations::automations.saved_success')]);
    }

    public function deleteWorkflow(int $id): void
    {
        Workflow::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('automations::automations.deleted_success')]);
    }

    // ── Trigger Configuration ──────────────────────────────────────────────────

    public function openTriggerModal(int $wfId): void
    {
        $this->resetValidation();
        $this->editingWfId = $wfId;

        $trigger = WorkflowTrigger::where('workflow_id', $wfId)->first();
        if ($trigger) {
            $this->triggerEvent = $trigger->event_type;
            $this->conditions   = $trigger->conditions ?? [];
        } else {
            $this->triggerEvent = 'lead_created';
            $this->conditions   = [];
        }

        $this->showTriggerModal = true;
    }

    public function closeTriggerModal(): void
    {
        $this->showTriggerModal = false;
    }

    public function addCondition(): void
    {
        $this->conditions[] = ['field' => '', 'operator' => 'equals', 'value' => ''];
    }

    public function removeCondition(int $index): void
    {
        unset($this->conditions[$index]);
        $this->conditions = array_values($this->conditions);
    }

    public function saveTrigger(): void
    {
        $this->validate([
            'triggerEvent'          => 'required|string',
            'conditions.*.field'    => 'required|string',
            'conditions.*.operator' => 'required|string',
            'conditions.*.value'    => 'required|string',
        ]);

        WorkflowTrigger::updateOrCreate(
            ['workflow_id' => $this->editingWfId],
            [
                'event_type' => $this->triggerEvent,
                'conditions' => $this->conditions,
            ]
        );

        $this->closeTriggerModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('automations::automations.saved_success')]);
    }

    // ── Action Configuration ───────────────────────────────────────────────────

    public function openActionModal(int $wfId): void
    {
        $this->resetValidation();
        $this->editingWfId  = $wfId;
        $this->actionType   = 'send_email';
        $this->actionConfig = ['to' => '', 'subject' => '', 'body' => ''];
        $this->showActionModal = true;
    }

    public function closeActionModal(): void
    {
        $this->showActionModal = false;
    }

    public function updatedActionType(string $val): void
    {
        if ($val === 'send_email') {
            $this->actionConfig = ['to' => '', 'subject' => '', 'body' => ''];
        } elseif ($val === 'send_whatsapp') {
            $this->actionConfig = ['to' => '', 'message' => ''];
        } elseif ($val === 'update_field') {
            $this->actionConfig = ['field' => '', 'value' => ''];
        } elseif ($val === 'trigger_webhook') {
            $this->actionConfig = ['webhook_url' => ''];
        }
    }

    public function saveAction(): void
    {
        $this->validate([
            'actionType' => 'required|string',
        ]);

        $priority = WorkflowAction::where('workflow_id', $this->editingWfId)->max('priority') + 10;

        WorkflowAction::create([
            'workflow_id'   => $this->editingWfId,
            'action_type'   => $this->actionType,
            'configuration' => $this->actionConfig,
            'priority'      => $priority,
        ]);

        $this->closeActionModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('automations::automations.saved_success')]);
    }

    public function deleteAction(int $id): void
    {
        WorkflowAction::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('automations::automations.deleted_success')]);
    }

    // ── Logs View ─────────────────────────────────────────────────────────────

    public function openLogsModal(int $wfId): void
    {
        $companyId = session('active_company_id', 1);
        $this->selectedLogs = WorkflowLog::where('company_id', $companyId)
            ->where('workflow_id', $wfId)
            ->latest()
            ->limit(20)
            ->get()
            ->toArray();

        $this->showLogsModal = true;
    }

    public function closeLogsModal(): void
    {
        $this->showLogsModal = false;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $companyId = session('active_company_id', 1);

        $workflows = Workflow::forCompany($companyId)
            ->with(['trigger', 'actions'])
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('automations::livewire.admin.automations.workflows', [
            'workflows' => $workflows,
        ])->title(__('automations::automations.workflows'));
    }
}

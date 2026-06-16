<?php

namespace Dev3bdulrahman\Automations\Http\Controllers\Web\Admin\Automations;

use Dev3bdulrahman\Automations\Models\WebhookEndpoint;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Webhooks extends Component
{
    use WithPagination;

    public string $search = '';

    // Modal control
    public bool $showWebhookModal = false;
    public ?int $webhookId        = null;

    // Fields
    public string $whName         = '';
    public string $whUrl          = '';
    public string $whSecret       = '';
    public array $whEvents        = []; // array of event types: ['lead_created', 'invoice_paid']
    public string $whStatus       = 'active';

    protected function rules(): array
    {
        return [
            'whName'   => 'required|string|max:255',
            'whUrl'    => 'required|url|max:255',
            'whSecret' => 'nullable|string|max:255',
            'whEvents' => 'required|array|min:1',
            'whStatus' => 'required|in:active,inactive',
        ];
    }

    public function openWebhookModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['webhookId', 'whName', 'whUrl', 'whSecret', 'whEvents', 'whStatus']);
        $this->whEvents = [];

        if ($id) {
            $endpoint = WebhookEndpoint::findOrFail($id);
            $this->webhookId = $endpoint->id;
            $this->whName    = $endpoint->name;
            $this->whUrl     = $endpoint->url;
            $this->whSecret  = $endpoint->secret ?? '';
            $this->whEvents  = $endpoint->events ?? [];
            $this->whStatus  = $endpoint->status;
        } else {
            $this->whSecret  = 'whsec_' . bin2hex(random_bytes(16));
        }

        $this->showWebhookModal = true;
    }

    public function closeWebhookModal(): void
    {
        $this->showWebhookModal = false;
    }

    public function saveWebhook(): void
    {
        $this->validate();

        $companyId = session('active_company_id', 1);

        $data = [
            'company_id' => $companyId,
            'name'       => $this->whName,
            'url'        => $this->whUrl,
            'secret'     => $this->whSecret ?: null,
            'events'     => $this->whEvents,
            'status'     => $this->whStatus,
        ];

        if ($this->webhookId) {
            WebhookEndpoint::findOrFail($this->webhookId)->update($data);
        } else {
            WebhookEndpoint::create($data);
        }

        $this->closeWebhookModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('automations::automations.saved_success')]);
    }

    public function deleteWebhook(int $id): void
    {
        WebhookEndpoint::findOrFail($id)->delete();
        $this->dispatch('notify', ['type' => 'success', 'message' => __('automations::automations.deleted_success')]);
    }

    public function render()
    {
        $companyId = session('active_company_id', 1);

        $webhooks = WebhookEndpoint::forCompany($companyId)
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('automations::livewire.admin.automations.webhooks', [
            'webhooks' => $webhooks,
        ])->title(__('automations::automations.webhooks'));
    }
}

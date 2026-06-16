<?php

namespace Dev3bdulrahman\Automations\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\HasApiResponse;
use Dev3bdulrahman\Automations\Http\Requests\Api\StoreWorkflowApiRequest;
use Dev3bdulrahman\Automations\Http\Requests\Api\StoreWebhookApiRequest;
use Dev3bdulrahman\Automations\Models\Workflow;
use Dev3bdulrahman\Automations\Models\WebhookEndpoint;
use Dev3bdulrahman\Automations\Services\AutomationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationApiController extends Controller
{
    use HasApiResponse;

    // ── Workflows ─────────────────────────────────────────────────────────────

    /**
     * List all workflows.
     */
    public function workflowsIndex(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Workflow::class);

        $companyId = $request->user()->company_id;
        $perPage = (int) $request->get('per_page', 15);

        $workflows = Workflow::forCompany($companyId)
            ->with(['trigger', 'actions'])
            ->latest()
            ->paginate($perPage);

        return $this->success(
            $workflows->items(),
            __('Workflows retrieved successfully'),
            200,
            [
                'current_page' => $workflows->currentPage(),
                'last_page' => $workflows->lastPage(),
                'per_page' => $workflows->perPage(),
                'total' => $workflows->total(),
            ]
        );
    }

    /**
     * Store a new workflow.
     */
    public function workflowsStore(StoreWorkflowApiRequest $request): JsonResponse
    {
        $this->authorize('create', Workflow::class);

        $validated = $request->validated();
        $user = $request->user();

        $workflow = Workflow::create([
            'company_id' => $user->company_id,
            'name' => $validated['name'],
            'description' => null,
            'status' => ($validated['is_active'] ?? true) ? 'active' : 'inactive',
        ]);

        // Create trigger
        $workflow->trigger()->create([
            'event_type' => $validated['trigger_type'],
            'conditions' => $validated['trigger_config'] ?? [],
        ]);

        return $this->success(
            $workflow->load(['trigger', 'actions']),
            __('Workflow created successfully'),
            201
        );
    }

    /**
     * Show a single workflow.
     */
    public function workflowsShow(Workflow $workflow): JsonResponse
    {
        $this->authorize('view', $workflow);

        $workflow->load(['trigger', 'actions', 'logs']);

        return $this->success(
            $workflow,
            __('Workflow details retrieved')
        );
    }

    /**
     * Update a workflow.
     */
    public function workflowsUpdate(Request $request, Workflow $workflow): JsonResponse
    {
        $this->authorize('update', $workflow);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'trigger_type' => 'sometimes|string',
            'trigger_config' => 'nullable|array',
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [];
        if (isset($validated['name'])) $updateData['name'] = $validated['name'];
        if (isset($validated['is_active'])) $updateData['status'] = $validated['is_active'] ? 'active' : 'inactive';

        if (!empty($updateData)) {
            $workflow->update($updateData);
        }

        if (isset($validated['trigger_type'])) {
            $workflow->trigger()->updateOrCreate(
                ['workflow_id' => $workflow->id],
                [
                    'event_type' => $validated['trigger_type'],
                    'conditions' => $validated['trigger_config'] ?? [],
                ]
            );
        }

        return $this->success(
            $workflow->fresh()->load(['trigger', 'actions']),
            __('Workflow updated successfully')
        );
    }

    /**
     * Delete a workflow.
     */
    public function workflowsDestroy(Workflow $workflow): JsonResponse
    {
        $this->authorize('delete', $workflow);

        $workflow->delete();

        return $this->success(
            null,
            __('Workflow deleted successfully')
        );
    }

    // ── Webhooks ──────────────────────────────────────────────────────────────

    /**
     * List all webhook endpoints.
     */
    public function webhooksIndex(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Workflow::class);

        $companyId = $request->user()->company_id;
        $perPage = (int) $request->get('per_page', 15);

        $webhooks = WebhookEndpoint::forCompany($companyId)
            ->latest()
            ->paginate($perPage);

        return $this->success(
            $webhooks->items(),
            __('Webhook endpoints retrieved successfully'),
            200,
            [
                'current_page' => $webhooks->currentPage(),
                'last_page' => $webhooks->lastPage(),
                'per_page' => $webhooks->perPage(),
                'total' => $webhooks->total(),
            ]
        );
    }

    /**
     * Store a new webhook endpoint.
     */
    public function webhooksStore(StoreWebhookApiRequest $request): JsonResponse
    {
        $this->authorize('create', Workflow::class);

        $validated = $request->validated();
        $user = $request->user();

        $webhook = WebhookEndpoint::create([
            'company_id' => $user->company_id,
            'name' => parse_url($validated['url'], PHP_URL_HOST) ?? 'Webhook',
            'url' => $validated['url'],
            'events' => $validated['events'],
            'secret' => $validated['secret'] ?? null,
            'status' => ($validated['is_active'] ?? true) ? 'active' : 'inactive',
        ]);

        return $this->success(
            $webhook,
            __('Webhook endpoint created successfully'),
            201
        );
    }

    /**
     * Show a webhook endpoint.
     */
    public function webhooksShow(WebhookEndpoint $webhook): JsonResponse
    {
        $this->authorize('viewAny', Workflow::class);

        return $this->success(
            $webhook,
            __('Webhook endpoint details retrieved')
        );
    }

    /**
     * Delete a webhook endpoint.
     */
    public function webhooksDestroy(WebhookEndpoint $webhook): JsonResponse
    {
        $this->authorize('delete', Workflow::class);

        $webhook->delete();

        return $this->success(
            null,
            __('Webhook endpoint deleted successfully')
        );
    }
}

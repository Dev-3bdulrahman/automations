<?php

namespace Dev3bdulrahman\Automations\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Dev3bdulrahman\Automations\Models\Workflow;
use Dev3bdulrahman\Automations\Models\WorkflowTrigger;
use Dev3bdulrahman\Automations\Models\WorkflowAction;
use Dev3bdulrahman\Automations\Models\WorkflowLog;
use Dev3bdulrahman\Automations\Models\WebhookEndpoint;

class AutomationService
{
    /**
     * Trigger workflows listening to a specific event type.
     */
    public function trigger(string $eventType, Model $model): void
    {
        $companyId = $model->company_id ?? session('active_company_id', 1);

        $workflows = Workflow::forCompany($companyId)
            ->active()
            ->whereHas('trigger', fn ($q) => $q->where('event_type', $eventType))
            ->with(['trigger', 'actions'])
            ->get();

        foreach ($workflows as $workflow) {
            $this->runWorkflow($workflow, $model, $eventType, $companyId);
        }

        // Also broadcast as webhook event
        $this->dispatchWebhooks($eventType, $model->toArray(), $companyId);
    }

    /**
     * Dispatch webhook payloads to endpoints listening to a specific event.
     */
    public function dispatchWebhooks(string $event, array $payload, int $companyId): void
    {
        $endpoints = WebhookEndpoint::forCompany($companyId)
            ->active()
            ->get();

        foreach ($endpoints as $endpoint) {
            if (is_array($endpoint->events) && in_array($event, $endpoint->events)) {
                try {
                    // Send HTTP POST request in background/synchronously (with short timeout)
                    Http::timeout(3)
                        ->withHeaders([
                            'X-Webhook-Secret' => $endpoint->secret,
                            'Content-Type' => 'application/json',
                        ])
                        ->post($endpoint->url, [
                            'event' => $event,
                            'timestamp' => now()->toIso8601String(),
                            'data' => $payload,
                        ]);
                } catch (\Exception $e) {
                    Log::warning("Webhook failed to {$endpoint->url}: " . $e->getMessage());
                }
            }
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    private function runWorkflow(Workflow $workflow, Model $model, string $eventType, int $companyId): void
    {
        $trigger = $workflow->trigger;

        // Evaluate conditions
        if ($trigger && !$this->evaluateConditions($trigger, $model)) {
            return;
        }

        $logs = [];
        $status = 'success';

        foreach ($workflow->actions as $action) {
            try {
                $this->executeAction($action, $model);
                $logs[] = [
                    'action_id' => $action->id,
                    'type' => $action->action_type,
                    'status' => 'success',
                ];
            } catch (\Exception $e) {
                $status = 'failed';
                $logs[] = [
                    'action_id' => $action->id,
                    'type' => $action->action_type,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
                break; // Stop execution of chain on error
            }
        }

        // Record log
        WorkflowLog::create([
            'company_id'    => $companyId,
            'workflow_id'   => $workflow->id,
            'trigger_event' => $eventType,
            'status'        => $status,
            'details'       => [
                'model_class' => get_class($model),
                'model_id'    => $model->getKey(),
                'actions'     => $logs,
            ],
        ]);
    }

    private function evaluateConditions(WorkflowTrigger $trigger, Model $model): bool
    {
        $conditions = $trigger->conditions;
        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $field    = $condition['field'] ?? null;
            $operator = $condition['operator'] ?? 'equals';
            $val      = $condition['value'] ?? null;

            if (!$field) continue;

            $modelVal = $model->getAttribute($field);

            switch ($operator) {
                case 'equals':
                    if ($modelVal != $val) return false;
                    break;
                case 'not_equals':
                    if ($modelVal == $val) return false;
                    break;
                case 'greater_than':
                    if ($modelVal <= $val) return false;
                    break;
                case 'less_than':
                    if ($modelVal >= $val) return false;
                    break;
                case 'contains':
                    if (strpos((string)$modelVal, (string)$val) === false) return false;
                    break;
                default:
                    return false;
            }
        }

        return true;
    }

    private function executeAction(WorkflowAction $action, Model $model): void
    {
        $config = $action->configuration;

        switch ($action->action_type) {
            case 'send_email':
                $to      = $config['to'] ?? '';
                $subject = $config['subject'] ?? '';
                $body    = $config['body'] ?? '';
                // For real production we would use Mail::to($to)->send(new WorkflowMail(...));
                // We'll log it for demo / verification purposes.
                Log::info("Automation Mail Sent: To: {$to}, Subject: {$subject}");
                break;

            case 'send_whatsapp':
                $to      = $config['to'] ?? '';
                $message = $config['message'] ?? '';
                Log::info("Automation WhatsApp Sent: To: {$to}, Msg: {$message}");
                break;

            case 'update_field':
                $field = $config['field'] ?? null;
                $value = $config['value'] ?? null;
                if ($field) {
                    $model->setAttribute($field, $value);
                    $model->saveQuietly();
                }
                break;

            case 'trigger_webhook':
                $url = $config['webhook_url'] ?? '';
                if ($url) {
                    Http::timeout(3)->post($url, $model->toArray());
                }
                break;

            default:
                throw new \Exception("Unsupported action type: {$action->action_type}");
        }
    }
}

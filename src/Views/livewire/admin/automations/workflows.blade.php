<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm font-sans">
        <div class="space-y-1">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white">{{ __('automations::automations.workflows') }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('automations::automations.title') }}</p>
        </div>
        <button wire:click="openWorkflowModal()" class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-500/20 transition-all">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>{{ __('automations::automations.add_workflow') }}</span>
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
        <label class="block text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">{{ __('automations::automations.search') }}</label>
        <div class="relative flex items-center">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('automations::automations.search_placeholder') }}"
                   class="w-full ps-10 pe-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
            <i data-lucide="search" class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
        </div>
    </div>

    {{-- Workflows Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        @forelse($workflows as $wf)
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden flex flex-col justify-between">
                <div class="p-6 space-y-4">
                    {{-- Title --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $wf->status === 'active' ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                                {{ __('automations::automations.' . $wf->status) }}
                            </span>
                            <h2 class="text-lg font-black text-gray-900 dark:text-white mt-2">{{ $wf->name }}</h2>
                            @if($wf->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $wf->description }}</p>
                            @endif
                        </div>
                        <div class="flex gap-1">
                            <button wire:click="openWorkflowModal({{ $wf->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <button wire:click="deleteWorkflow({{ $wf->id }})" wire:confirm="{{ __('automations::automations.confirm_delete') }}" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Trigger Section --}}
                    <div class="p-4 bg-gray-50 dark:bg-gray-850 rounded-xl border border-gray-100 dark:border-gray-800/50 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('automations::automations.trigger_event') }}</span>
                            <button wire:click="openTriggerModal({{ $wf->id }})" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors">
                                {{ __('automations::automations.configure_trigger') }}
                            </button>
                        </div>
                        @if($wf->trigger)
                            <p class="text-sm font-bold text-gray-950 dark:text-gray-200">
                                {{ __('automations::automations.event_' . $wf->trigger->event_type) }}
                            </p>
                            @if($wf->trigger->conditions)
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    @foreach($wf->trigger->conditions as $cond)
                                        <span class="px-2 py-0.5 text-xs bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-100 dark:border-gray-800 rounded font-mono">
                                            {{ $cond['field'] }} {{ $cond['operator'] }} "{{ $cond['value'] }}"
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <p class="text-xs text-gray-400 italic">—</p>
                        @endif
                    </div>

                    {{-- Actions Section --}}
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ __('automations::automations.actions') }}</span>
                            <button wire:click="openActionModal({{ $wf->id }})" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors">
                                {{ __('automations::automations.add_action') }}
                            </button>
                        </div>
                        <div class="space-y-1.5">
                            @forelse($wf->actions as $action)
                                <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="w-5 h-5 flex items-center justify-center bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-md font-bold text-xs">
                                            {{ $loop->iteration }}
                                        </span>
                                        <span class="font-bold text-gray-900 dark:text-white">
                                            {{ __('automations::automations.action_' . $action->action_type) }}
                                        </span>
                                    </div>
                                    <button wire:click="deleteAction({{ $action->id }})" class="text-gray-300 hover:text-red-500 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">—</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Card Footer --}}
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                    <button wire:click="openLogsModal({{ $wf->id }})" class="flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                        <i data-lucide="list-collapse" class="w-4 h-4"></i>
                        <span>{{ __('automations::automations.view_logs') }}</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-12 text-center">
                <i data-lucide="zap-off" class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-700 mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400">{{ __('automations::automations.no_workflows') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($workflows->hasPages())
        <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
            {{ $workflows->links() }}
        </div>
    @endif

    {{-- Workflow Modal --}}
    @if($showWorkflowModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm font-sans">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-lg w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ $workflowId ? __('automations::automations.edit_workflow') : __('automations::automations.add_workflow') }}</h3>
                    <button wire:click="closeWorkflowModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="saveWorkflow" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.workflow_name') }} *</label>
                        <input type="text" wire:model="wfName" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        @error('wfName') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.description') }}</label>
                        <textarea wire:model="wfDesc" rows="3" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.status') }} *</label>
                        <select wire:model="wfStatus" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="active">{{ __('automations::automations.active') }}</option>
                            <option value="inactive">{{ __('automations::automations.inactive') }}</option>
                        </select>
                    </div>
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeWorkflowModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl transition-all">{{ __('automations::automations.cancel') }}</button>
                        <button type="submit" class="px-5 py-2 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg transition-all">{{ __('automations::automations.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Trigger Modal --}}
    @if($showTriggerModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm font-sans">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-2xl w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ __('automations::automations.configure_trigger') }}</h3>
                    <button wire:click="closeTriggerModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="saveTrigger" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.trigger_event') }} *</label>
                        <select wire:model="triggerEvent" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="lead_created">{{ __('automations::automations.event_lead_created') }}</option>
                            <option value="invoice_paid">{{ __('automations::automations.event_invoice_paid') }}</option>
                            <option value="stock_updated">{{ __('automations::automations.event_stock_updated') }}</option>
                        </select>
                    </div>

                    {{-- Conditions --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('automations::automations.conditions') }}</label>
                            <button type="button" wire:click="addCondition()" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors">
                                + {{ __('automations::automations.add_condition') }}
                            </button>
                        </div>
                        @foreach($conditions as $index => $cond)
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-4">
                                    <input type="text" wire:model="conditions.{{ $index }}.field" placeholder="{{ __('automations::automations.field') }}" class="w-full px-3 py-2 text-xs bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono">
                                </div>
                                <div class="col-span-3">
                                    <select wire:model="conditions.{{ $index }}.operator" class="w-full px-3 py-2 text-xs bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="equals">{{ __('automations::automations.operator_equals') }}</option>
                                        <option value="not_equals">{{ __('automations::automations.operator_not_equals') }}</option>
                                        <option value="greater_than">{{ __('automations::automations.operator_greater_than') }}</option>
                                        <option value="less_than">{{ __('automations::automations.operator_less_than') }}</option>
                                        <option value="contains">{{ __('automations::automations.operator_contains') }}</option>
                                    </select>
                                </div>
                                <div class="col-span-4">
                                    <input type="text" wire:model="conditions.{{ $index }}.value" placeholder="{{ __('automations::automations.value') }}" class="w-full px-3 py-2 text-xs bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="col-span-1 text-center">
                                    <button type="button" wire:click="removeCondition({{ $index }})" class="text-gray-400 hover:text-red-500 transition-colors">
                                        <i data-lucide="trash" class="w-4 h-4 mx-auto"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeTriggerModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl transition-all">{{ __('automations::automations.cancel') }}</button>
                        <button type="submit" class="px-5 py-2 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg transition-all">{{ __('automations::automations.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Action Modal --}}
    @if($showActionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm font-sans">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-lg w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ __('automations::automations.add_action') }}</h3>
                    <button wire:click="closeActionModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <form wire:submit.prevent="saveAction" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.action_type') }} *</label>
                        <select wire:model.live="actionType" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <option value="send_email">{{ __('automations::automations.action_send_email') }}</option>
                            <option value="send_whatsapp">{{ __('automations::automations.action_send_whatsapp') }}</option>
                            <option value="trigger_webhook">{{ __('automations::automations.action_trigger_webhook') }}</option>
                            <option value="update_field">{{ __('automations::automations.action_update_field') }}</option>
                        </select>
                    </div>

                    {{-- Action Configuration details --}}
                    @if($actionType === 'send_email')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.email_to') }} *</label>
                            <input type="text" wire:model="actionConfig.to" placeholder="e.g. customer@example.com" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.email_subject') }} *</label>
                            <input type="text" wire:model="actionConfig.subject" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.email_body') }} *</label>
                            <textarea wire:model="actionConfig.body" rows="3" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                        </div>
                    @elseif($actionType === 'send_whatsapp')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.whatsapp_to') }} *</label>
                            <input type="text" wire:model="actionConfig.to" placeholder="e.g. +966500000000" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.whatsapp_message') }} *</label>
                            <textarea wire:model="actionConfig.message" rows="3" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"></textarea>
                        </div>
                    @elseif($actionType === 'trigger_webhook')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.webhook_url') }} *</label>
                            <input type="url" wire:model="actionConfig.webhook_url" placeholder="https://api.thirdparty.com/webhook" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    @elseif($actionType === 'update_field')
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.update_field_name') }} *</label>
                            <input type="text" wire:model="actionConfig.field" placeholder="e.g. status" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all font-mono">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('automations::automations.update_field_value') }} *</label>
                            <input type="text" wire:model="actionConfig.value" placeholder="e.g. paid" class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-2">
                        <button type="button" wire:click="closeActionModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl transition-all">{{ __('automations::automations.cancel') }}</button>
                        <button type="submit" class="px-5 py-2 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-lg transition-all">{{ __('automations::automations.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Execution Logs Modal --}}
    @if($showLogsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm font-sans">
            <div class="bg-white dark:bg-gray-900 rounded-2xl max-w-4xl w-full border border-gray-100 dark:border-gray-800 shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ __('automations::automations.execution_logs') }}</h3>
                    <button wire:click="closeLogsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-[500px] space-y-4">
                    @forelse($selectedLogs as $log)
                        <div class="p-4 bg-gray-50 dark:bg-gray-850 rounded-xl border border-gray-100 dark:border-gray-850 space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-400">{{ $log['created_at'] }}</span>
                                <span class="px-2 py-0.5 font-bold rounded-full {{ $log['status'] === 'success' ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400' }}">
                                    {{ $log['status'] }}
                                </span>
                            </div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">
                                Event: {{ $log['trigger_event'] }}
                            </div>
                            @if(!empty($log['details']))
                                <pre class="p-3 bg-gray-100 dark:bg-gray-900 rounded-lg text-xs font-mono text-gray-600 dark:text-gray-400 overflow-x-auto">{{ json_encode($log['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">{{ __('automations::automations.no_logs') }}</p>
                    @endforelse
                </div>
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                    <button type="button" wire:click="closeLogsModal()" class="px-5 py-2 text-sm font-bold bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl transition-all">{{ __('automations::automations.cancel') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>

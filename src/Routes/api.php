<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Automations\Http\Controllers\Api\AutomationApiController;

Route::prefix('api/v1/automations')->middleware(['auth:sanctum', 'throttle:60,1', 'api.tenant'])->group(function () {
    // Workflows
    Route::get('workflows', [AutomationApiController::class, 'workflowsIndex'])->middleware('can:automations.workflows.view')->name('api.v1.automations.workflows.index');
    Route::post('workflows', [AutomationApiController::class, 'workflowsStore'])->middleware('can:automations.workflows.create')->name('api.v1.automations.workflows.store');
    Route::get('workflows/{workflow}', [AutomationApiController::class, 'workflowsShow'])->middleware('can:automations.workflows.view')->name('api.v1.automations.workflows.show');
    Route::put('workflows/{workflow}', [AutomationApiController::class, 'workflowsUpdate'])->middleware('can:automations.workflows.edit')->name('api.v1.automations.workflows.update');
    Route::delete('workflows/{workflow}', [AutomationApiController::class, 'workflowsDestroy'])->middleware('can:automations.workflows.delete')->name('api.v1.automations.workflows.destroy');

    // Webhooks
    Route::get('webhooks', [AutomationApiController::class, 'webhooksIndex'])->middleware('can:automations.webhooks.view')->name('api.v1.automations.webhooks.index');
    Route::post('webhooks', [AutomationApiController::class, 'webhooksStore'])->middleware('can:automations.webhooks.create')->name('api.v1.automations.webhooks.store');
    Route::get('webhooks/{webhook}', [AutomationApiController::class, 'webhooksShow'])->middleware('can:automations.webhooks.view')->name('api.v1.automations.webhooks.show');
    Route::delete('webhooks/{webhook}', [AutomationApiController::class, 'webhooksDestroy'])->middleware('can:automations.webhooks.delete')->name('api.v1.automations.webhooks.destroy');
});

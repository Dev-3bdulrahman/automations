<?php

use Illuminate\Support\Facades\Route;
use Dev3bdulrahman\Automations\Http\Controllers\Web\Admin\Automations\Workflows;
use Dev3bdulrahman\Automations\Http\Controllers\Web\Admin\Automations\Webhooks;

Route::middleware(['web', 'auth'])->prefix('admin/automations')->group(function () {
    Route::get('workflows', Workflows::class)->name('admin.automations.workflows');
    Route::get('webhooks',  Webhooks::class)->name('admin.automations.webhooks');
});

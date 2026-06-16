<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Workflows
        Schema::create('auto_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active | inactive
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Workflow Triggers
        Schema::create('auto_workflow_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('auto_workflows')->cascadeOnDelete();
            $table->string('event_type');                 // e.g. lead_created, invoice_status_changed, stock_updated
            $table->json('conditions')->nullable();      // [{"field": "status", "operator": "equals", "value": "paid"}]
            $table->timestamps();
        });

        // 3. Workflow Actions
        Schema::create('auto_workflow_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('auto_workflows')->cascadeOnDelete();
            $table->string('action_type');                // send_email | send_whatsapp | trigger_webhook | update_field
            $table->json('configuration')->nullable();    // {email, subject, body, webhook_url, fields}
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // 4. Webhook Endpoints
        Schema::create('auto_webhook_endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('secret')->nullable();
            $table->json('events')->nullable();           // ['lead.created', 'invoice.paid']
            $table->string('status')->default('active'); // active | inactive
            $table->timestamps();
        });

        // 5. Workflow execution logs
        Schema::create('auto_workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('auto_workflows')->cascadeOnDelete();
            $table->string('trigger_event');
            $table->string('status')->default('success'); // success | failed
            $table->json('details')->nullable();          // payload, execution trail, error message
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_workflow_logs');
        Schema::dropIfExists('auto_webhook_endpoints');
        Schema::dropIfExists('auto_workflow_actions');
        Schema::dropIfExists('auto_workflow_triggers');
        Schema::dropIfExists('auto_workflows');
    }
};

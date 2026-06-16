<?php

namespace Dev3bdulrahman\Automations\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'trigger_type' => $this->trigger_type,
            'trigger_config' => $this->trigger_config,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}

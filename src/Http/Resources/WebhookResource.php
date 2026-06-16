<?php

namespace Dev3bdulrahman\Automations\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'events' => $this->events,
            'is_active' => $this->is_active,
            'secret' => $this->secret ? str_repeat('*', 8) . substr($this->secret, -4) : null,
            'created_at' => $this->created_at,
        ];
    }
}

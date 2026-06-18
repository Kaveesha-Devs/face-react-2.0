<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReactTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => $this->type,
            'icon_code'    => $this->icon_code,
            'sinhala_type' => $this->sinhala_type,
            'tamil_type'   => $this->tamil_type,
            'sort_order'   => $this->sort_order,
            'is_active'    => $this->is_active,
        ];
    }
}
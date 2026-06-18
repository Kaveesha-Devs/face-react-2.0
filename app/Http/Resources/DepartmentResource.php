<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'name_sinhala' => $this->name_sinhala,
            'name_tamil'   => $this->name_tamil,
            'code'         => $this->code,
            'is_active'    => $this->is_active,
            'users_count'  => $this->whenCounted('users'),
            'sections' => $this->whenLoaded('sections', fn () =>
                $this->sections->map(fn ($s) => [
                    'id'           => $s->id,
                    'name'         => $s->name,
                    'name_sinhala' => $s->name_sinhala,
                    'name_tamil'   => $s->name_tamil,
                ])
            ),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReactLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'note'       => $this->note,
            'date'       => $this->created_at?->toDateString(),
            'time'       => $this->created_at?->format('H:i'),
            'hour'       => $this->created_at?->hour,
            'created_at' => $this->created_at?->toDateTimeString(),

            'react_type' => $this->whenLoaded('reactType', fn() => [
                'id'        => $this->reactType->id,
                'type'      => $this->reactType->type,
                'icon_code' => $this->reactType->icon_code,
                'si'        => $this->reactType->sinhala_type,
                'ta'        => $this->reactType->tamil_type,
            ]),

            'user' => $this->whenLoaded('user', fn() => [
                'id'          => $this->user->id,
                'name'        => $this->user->name,
                'username'    => $this->user->username,
                'employee_id' => $this->user->employee_id,
            ]),

            'department' => $this->whenLoaded('department', fn() => [
                'id'   => $this->department?->id,
                'name' => $this->department?->name,
            ]),

            'section' => $this->whenLoaded('section', fn() => [
                'id'   => $this->section?->id,
                'name' => $this->section?->name,
            ]),
        ];
    }
}
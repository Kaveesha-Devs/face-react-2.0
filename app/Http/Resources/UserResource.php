<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'username'      => $this->username,
            'email'         => $this->email,
            'employee_id'   => $this->employee_id,
            'role'          => $this->role,
            'language'      => $this->language,
            'is_active'     => $this->is_active,
            'last_login_at' => $this->last_login_at?->toDateTimeString(),
            'avatar_url'    => $this->avatar
                ? asset('storage/' . $this->avatar)
                : null,
            'company'    => $this->whenLoaded('company', fn () => [
                'id'   => $this->company->id,
                'name' => $this->company->name,
                'logo' => $this->company->logo
                    ? asset('storage/' . $this->company->logo)
                    : null,
            ]),
            'department' => $this->whenLoaded('department', fn () => [
                'id'   => $this->department?->id,
                'name' => $this->department?->name,
            ]),
            'section'    => $this->whenLoaded('section', fn () => [
                'id'   => $this->section?->id,
                'name' => $this->section?->name,
            ]),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'name_sinhala' => $this->name_sinhala,
            'name_tamil'   => $this->name_tamil,
            'reg_number'   => $this->reg_number,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'address'      => $this->address,
            'is_active'    => $this->is_active,
            'logo_url'     => $this->logo
                ? asset('storage/' . $this->logo)
                : null,
            'departments_count' => $this->whenCounted('departments'),
            'users_count'       => $this->whenCounted('users'),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
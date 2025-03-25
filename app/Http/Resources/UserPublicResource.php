<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPublicResource extends JsonResource
{
    public function toArray(Request $request): array
    // Public facing attrs. Call: with('user:id,name,is_email_public,email')
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->when($this->is_email_public, $this->email),
        ];
    }
}

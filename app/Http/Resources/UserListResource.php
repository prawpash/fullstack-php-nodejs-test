<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentUser = $request->user();

        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->role,
            'created_at' => $this->created_at->toIso8601String(),
            'orders_count' => $this->orders_count,
            'can_edit' => $this->canBeEditedBy($currentUser),
        ];
    }

    /**
     * Determine whether the given user is allowed to edit this user.
     */
    private function canBeEditedBy(?User $currentUser): bool
    {
        if ($currentUser === null) {
            return false;
        }

        return match ($currentUser->role) {
            'admin' => true,
            'manager' => $this->role === 'user',
            default => $currentUser->is($this->resource),
        };
    }
}

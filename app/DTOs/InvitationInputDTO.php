<?php

namespace App\DTOs;

readonly class InvitationInputDTO
{
    public function __construct(
        public string $email,
        public string $roleId,
        public string $invitedByUserId,
    ) {
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            email: $data['email'],
            roleId: $data['role_id'],
            invitedByUserId: $data['invited_by_user_id'],
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'role_id' => $this->roleId,
            'invited_by_user_id' => $this->invitedByUserId,
        ];
    }
}

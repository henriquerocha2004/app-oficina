<?php

namespace App\DTOs;

use App\Models\Supplier;

readonly class SupplierOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $document_number,
        public bool $is_active,
        public ?string $trade_name = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $website = null,
        public ?string $street = null,
        public ?string $number = null,
        public ?string $complement = null,
        public ?string $neighborhood = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $zip_code = null,
        public ?string $contact_person = null,
        public ?int $payment_term_days = null,
        public ?string $notes = null,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {
    }

    public static function fromModel(Supplier $supplier): self
    {
        return new self(
            id: $supplier->id,
            name: $supplier->name,
            document_number: $supplier->document_number,
            is_active: $supplier->is_active,
            trade_name: $supplier->trade_name,
            email: $supplier->email,
            phone: $supplier->phone,
            mobile: $supplier->mobile,
            website: $supplier->website,
            street: $supplier->street,
            number: $supplier->number,
            complement: $supplier->complement,
            neighborhood: $supplier->neighborhood,
            city: $supplier->city,
            state: $supplier->state,
            zip_code: $supplier->zip_code,
            contact_person: $supplier->contact_person,
            payment_term_days: $supplier->payment_term_days,
            notes: $supplier->notes,
            created_at: $supplier->created_at?->toIso8601String(),
            updated_at: $supplier->updated_at?->toIso8601String(),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'document_number' => $this->document_number,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'website' => $this->website,
            'street' => $this->street,
            'number' => $this->number,
            'complement' => $this->complement,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'contact_person' => $this->contact_person,
            'payment_term_days' => $this->payment_term_days,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ], fn($value) => $value !== null);
    }
}

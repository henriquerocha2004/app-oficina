<?php

namespace App\DTOs;

readonly class SupplierInputDTO
{
    public function __construct(
        public string $name,
        public string $document_number,
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
        public bool $is_active = true,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            document_number: $data['document_number'],
            trade_name: $data['trade_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            mobile: $data['mobile'] ?? null,
            website: $data['website'] ?? null,
            street: $data['street'] ?? null,
            number: $data['number'] ?? null,
            complement: $data['complement'] ?? null,
            neighborhood: $data['neighborhood'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zip_code: $data['zip_code'] ?? null,
            contact_person: $data['contact_person'] ?? null,
            payment_term_days: isset($data['payment_term_days']) && $data['payment_term_days'] !== ''
                ? (int) $data['payment_term_days']
                : null,
            notes: $data['notes'] ?? null,
            is_active: $data['is_active'] ?? true,
        );
    }

    public function toArray(): array
    {
        return array_filter([
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
        ], fn($value) => $value !== null);
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'brand' => $this->brand,
            'model' => $this->model,
            'year' => $this->year,
            'plate' => $this->license_plate,
            'color' => $this->color,
            'vehicle_type' => $this->vehicle_type,
            'cilinder_capacity' => $this->cilinder_capacity,
            'fuel' => $this->fuel,
            'transmission' => $this->transmission,
            'mileage' => $this->mileage,
            'vin' => $this->vin,
            'observations' => $this->observations,
            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'email' => $this->client->email,
                    'document_number' => $this->client->document_number,
                    'phone' => $this->client->phone,
                ];
            }),
        ];
    }
}

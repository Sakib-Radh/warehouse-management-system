<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'type' => $this->type,
            'quantity' => (int) $this->quantity,
            'source_location_id' => $this->source_location_id,
            'destination_location_id' => $this->destination_location_id,
            'reference_number' => $this->reference_number,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
        ];
    }
}

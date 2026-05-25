<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
{
    /**
     * @return array<string, float|int|string>
     */
    public function toArray(Request $request): array
    {
        return [
            'total' => (float) $this->total,
            'currency_id' => $this->currency_id?->value,
            'quotation_id' => $this->id,
        ];
    }
}

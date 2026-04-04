<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\DTOs\CountryDto
 */
class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->cca2,
            'name' => $this->name,
            'flag' => [
                'png' => $this->flagPng,
                'svg' => $this->flagSvg
            ]
        ];
    }
}

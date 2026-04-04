<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\DTOs\CountryDetailDto
 */
class CountryDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'code' => $this->cca2,
            'name' => $this->name,
            'officialName' => $this->officialName,
            'flag' => [
                'png' => $this->flagPng,
                'svg' => $this->flagSvg,
                'alt' => $this->flagAlt,
            ],
            'region' => $this->region,
            'subregion' => $this->subregion,
            'population' => $this->population,
            'capital' => $this->capital,
            'timezones' => $this->timezones,
            'borders' => $this->borders,
            'languages' => $this->languages,
            'currencies' => $this->currencies,
        ];
    }
}

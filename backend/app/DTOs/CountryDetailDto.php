<?php

namespace App\DTOs;

readonly class CountryDetailDto
{
    /**
     * @param  string[]  $capital
     * @param  string[]  $timezones
     * @param  string[]  $borders
     * @param  array<string, string>  $languages
     * @param  array<string, array{name: string, symbol: string}>  $currencies
     */
    public function __construct(
        public string $cca2,
        public string $name,
        public string $officialName,
        public string $flagPng,
        public string $flagSvg,
        public string $flagAlt,
        public string $region,
        public string $subregion,
        public int $population,
        public array $capital,
        public array $timezones,
        public array $borders,
        public array $languages,
        public array $currencies,
    ) {}

    public static function fromRestCountriesV31(array $data): self
    {
        $currencies = [];
        foreach ($data['currencies'] ?? [] as $code => $info) {
            $currencies[$code] = [
                'name' => $info['name'] ?? '',
                'symbol' => $info['symbol'] ?? '',
            ];
        }

        return new self(
            cca2: $data['cca2'],
            name: $data['name']['common'],
            officialName: $data['name']['official'] ?? $data['name']['common'],
            flagPng: $data['flags']['png'] ?? '',
            flagSvg: $data['flags']['svg'] ?? '',
            flagAlt: $data['flags']['alt'] ?? '',
            region: $data['region'] ?? '',
            subregion: $data['subregion'] ?? '',
            population: $data['population'] ?? 0,
            capital: $data['capital'] ?? [],
            timezones: $data['timezones'] ?? [],
            borders: $data['borders'] ?? [],
            languages: array_values($data['languages'] ?? []),
            currencies: $currencies,
        );
    }

    public function toArray(): array
    {
        return [
            'cca2' => $this->cca2,
            'name' => $this->name,
            'officialName' => $this->officialName,
            'flagPng' => $this->flagPng,
            'flagSvg' => $this->flagSvg,
            'flagAlt' => $this->flagAlt,
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

    public static function fromArray(array $data): self
    {
        return new self(
            cca2: $data['cca2'],
            name: $data['name'],
            officialName: $data['officialName'],
            flagPng: $data['flagPng'],
            flagSvg: $data['flagSvg'],
            flagAlt: $data['flagAlt'],
            region: $data['region'],
            subregion: $data['subregion'],
            population: $data['population'],
            capital: $data['capital'],
            timezones: $data['timezones'],
            borders: $data['borders'],
            languages: $data['languages'],
            currencies: $data['currencies'],
        );
    }
}

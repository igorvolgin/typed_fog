<?php

namespace App\Http\Controllers;

use App\Http\Resources\CountryDetailResource;
use App\Http\Resources\CountryResource;
use App\Repositories\Contracts\CountryRepositoryInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CountriesController extends Controller
{
    public function __construct(
        private readonly CountryRepositoryInterface $countries,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return CountryResource::collection($this->countries->all());
    }

    public function show(string $code): CountryDetailResource
    {
        $country = $this->countries->findByCode($code);

        abort_if($country === null, 404, 'Country not found');

        return new CountryDetailResource($country);
    }
}

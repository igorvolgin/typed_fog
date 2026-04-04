<?php

namespace App\Http\Controllers;

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
}

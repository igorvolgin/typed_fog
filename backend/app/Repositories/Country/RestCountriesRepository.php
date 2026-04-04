<?php

namespace App\Repositories\Country;

use App\DTOs\CountryDetailDto;
use App\DTOs\CountryDto;
use App\Exceptions\ExternalApiException;
use App\Repositories\Contracts\CountryRepositoryInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Throwable;

class RestCountriesRepository implements CountryRepositoryInterface
{
    private const string BASE_URL = 'https://restcountries.com/v3.1';

    private const string LIST_FIELDS = 'name,cca2,cca3,ccn3,cioc,flags';

    private const string DETAIL_FIELDS = 'name,cca2,flags,region,subregion,population,capital,timezones,borders,languages,currencies';

    /** @return CountryDto[] */
    public function all(): array
    {
        $response = $this->request('/all', self::LIST_FIELDS);

        return array_map(
            fn (array $item) => CountryDto::fromRestCountriesV31($item),
            $response,
        );
    }

    public function findByCode(string $code): ?CountryDetailDto
    {
        $response = $this->request('/alpha/'.urlencode($code), self::DETAIL_FIELDS);

        if ($response === []) {
            return null;
        }

        return CountryDetailDto::fromRestCountriesV31($response);
    }

    /**
     * @return array<int, array<string, mixed>>
     *
     * @throws ExternalApiException
     */
    private function request(string $path, string $fields): array
    {
        try {
            $response = Http::timeout(10)
                ->retry(3, 500, function (Throwable $exception) {
                    return $exception instanceof RequestException
                        && ($exception->response->status() === 429 || $exception->response->serverError());
                }, throw: false)
                ->get(
                    self::BASE_URL.$path,
                    ['fields' => $fields]
                );

            if ($response->notFound()) {
                return [];
            }

            $response->throw();

            return $response->json();
        } catch (ExternalApiException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ExternalApiException('restcountries.com', $e);
        }
    }
}

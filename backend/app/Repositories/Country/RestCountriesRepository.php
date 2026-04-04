<?php

namespace App\Repositories\Country;

use App\DTOs\CountryDto;
use App\Exceptions\ExternalApiException;
use App\Repositories\Contracts\CountryRepositoryInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Throwable;

class RestCountriesRepository implements CountryRepositoryInterface
{
    private const string BASE_URL = 'https://restcountries.com/v3.1';

    private const string FIELDS = 'name,cca2,cca3,ccn3,cioc,flags';

    /** @return CountryDto[] */
    public function all(): array
    {
        return $this->request('/all');
    }

    /**
     * @return CountryDto[]
     *
     * @throws ExternalApiException
     */
    private function request(string $path): array
    {
        try {
            $response = Http::timeout(10)
                ->retry(3, 500, function (Throwable $exception) {
                    return $exception instanceof RequestException
                        && ($exception->response->status() === 429 || $exception->response->serverError());
                })
                ->get(
                    self::BASE_URL.$path,
                    ['fields' => self::FIELDS]
                );

            if ($response->notFound()) {
                return [];
            }

            $response->throw();

            return array_map(
                fn (array $item) => CountryDto::fromRestCountriesV31($item),
                $response->json(),
            );
        } catch (ExternalApiException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new ExternalApiException('restcountries.com', $e);
        }
    }
}

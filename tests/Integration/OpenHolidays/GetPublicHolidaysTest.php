<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\OpenHolidaysConnector;
use App\Http\Integrations\OpenHolidays\Requests\GetPublicHolidays;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function (): void {

    MockClient::global([
        GetPublicHolidays::class => MockResponse::fixture('OpenHolidays/portugal-public-holidays'),
    ]);

    $this->connetor = new OpenHolidaysConnector();

});

test('returns a public holiday data object',
    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    function (): void {

        $request = new GetPublicHolidays(
            countryIsoCode: 'PT',
            validFrom: CarbonImmutable::parse('2025')->startOfYear(),
            validTo: CarbonImmutable::parse('2025')->endOfYear(),
            subdivisionCode: 'PT'
        );

        $response = $this->connetor->send($request);

        expect($response->dto())
            ->toBeInstanceOf(Collection::class);

    });

test('returns public holidays for country',
    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    function (): void {

        $request = new GetPublicHolidays(
            countryIsoCode: 'PT',
            validFrom: CarbonImmutable::parse('2025')->startOfYear(),
            validTo: CarbonImmutable::parse('2025')->endOfYear(),
            languageIsoCode: 'PT',
            subdivisionCode: 'PT'
        );

        $response = $this->connetor->send($request);

        expect($response->status())
            ->toBe(200);

    });

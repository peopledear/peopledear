<?php

declare(strict_types=1);

use App\Http\Integrations\OpenHolidays\OpenHolidaysConnector;
use App\Http\Integrations\OpenHolidays\Requests\GetSubdivisions;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function (): void {

    MockClient::global([
        GetSubdivisions::class => MockResponse::fixture('OpenHolidays/spain-subdivisions'),
    ]);

    $this->connetor = new OpenHolidaysConnector();

});

test('fetches subdivisions for a country',
    /**
     * @throws FatalRequestException
     * @throws RequestException
     */
    function (): void {

        $request = new GetSubdivisions(
            countryIsoCode: 'ES',
            languageIsoCode: 'ES',
        );

        $response = $this->connetor->send($request);

        expect($response)
            ->status()
            ->toBe(200);

    });

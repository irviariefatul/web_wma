<?php

namespace App\Utils;

use Carbon\Carbon;
use Goutte;
use Nesk\Puphpeteer\Puppeteer;
use Symfony\Component\Panther\PantherTestCase;

use Nesk\Rialto\Data\JsFunction;

use Scheb\YahooFinanceApi\ApiClient;
use Scheb\YahooFinanceApi\ApiClientFactory;
use GuzzleHttp\Client;


class YahooFinanceScraper
{
    public function scrape(): array
    {
        $data = [];
        $client = ApiClientFactory::createApiClient();
        // Or use your own Guzzle client and pass it in
        $options = ['verify' => false,];
        $guzzleClient = new Client($options);
        $client = ApiClientFactory::createApiClient($guzzleClient);
        $historicalData = $client->getHistoricalQuoteData(
            "^JKSE",
            ApiClient::INTERVAL_1_DAY,
            new \DateTime("-3 days"),
            new \DateTime("today")
        );

        // Atur konfigurasi memori dan waktu eksekusi
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300); // 300 seconds = 5 minutes
        foreach ($historicalData as $key => $value) {
            $temp = [
                'date' => $value->getDate(),
                'adj_close' => $value->getAdjClose()
            ];
            array_push($data, $temp);
        }

        return $data;
    }
}

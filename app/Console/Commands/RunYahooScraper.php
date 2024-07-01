<?php
namespace App\Console\Commands;

use App\Utils\PeramalanUtils;
use App\Utils\YahooFinanceScraper;
use Illuminate\Console\Command;

class RunYahooScraper extends Command
{
    protected $signature = 'yahoo-scraper:run';
    protected $description = 'Run Yahoo Finance Scraper';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $scraper = new YahooFinanceScraper();
        $crawler = $scraper->scrape();

        try {
            foreach ($crawler as $key => $value) {
                $rentang = 2;
                if (isset($value['adj_close']) && $value['adj_close'] !== null && $value['adj_close'] !== 0) {
                    PeramalanUtils::scrap($rentang, $value['date'], $value['adj_close']);
                }
            }
        } catch (\DivisionByZeroError $e) {
            // Menangkap kesalahan division by zero dan langsung redirect
            return 0;
        }

        return 1;
    }
}
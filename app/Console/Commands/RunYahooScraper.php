<?php

namespace App\Console\Commands;

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
        return 1;
    }
}

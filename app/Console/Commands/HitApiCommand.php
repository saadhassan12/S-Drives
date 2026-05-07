<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class HitApiCommand extends Command
{
    protected $signature = 'hit:api';
    protected $description = 'Hit multiple API URLs every second and log each hit';

    public function handle()
    {
        $this->info('HitApiCommand started...');


      

   
    }
}

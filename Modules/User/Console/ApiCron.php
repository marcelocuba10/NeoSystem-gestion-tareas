<?php

namespace Modules\User\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ApiCron extends Command
{
    protected $name = 'api:cron';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        \Log::info("cron sin proceso a ejecutar, ejecute la url admin/machines/cron");
    }
}

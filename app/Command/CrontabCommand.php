<?php

declare(strict_types=1);

namespace App\Command;

use App\Utils\Facade\Log;

class CrontabCommand
{
    public function handle()
    {
        Log::info(__METHOD__, [get_called_class()]);
    }
}

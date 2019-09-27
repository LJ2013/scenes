<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Tools
{
    public static function execute($cmd)
    {
        Log::info('shell-commands: '.$cmd);
        $output = shell_exec($cmd);
        Log::info('shell-output: '. var_export($output, true));
        $obj = json_decode($output);
        return $obj;
    }
}
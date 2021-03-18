<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class PdfService 
{
    private $command = '%s --headless --disable-gpu --run-all-compositor-stages-before-draw --virtual-time-budget=10000 --print-to-pdf-no-header --print-to-pdf=%s %s 2>&1';
    private $binary = '/usr/bin/google-chrome';

    public function __construct() 
    {

    }

    public function render($viewName, $data)
    {
        $view = View::make($viewName, ['data' => $data])->render();

        $process = new Process(sprintf(
            $this->command,
            escapeshellarg($this->binary),
            escapeshellarg($path = tempnam(sys_get_temp_dir(), Str::random())),
            escapeshellarg('data:text/html,'.rawurlencode($view))
        ));

        try {
            $process->mustRun();
            return File::get($path);
        } 
        catch (ProcessFailedException $exception) {
            //
        }
    }

}
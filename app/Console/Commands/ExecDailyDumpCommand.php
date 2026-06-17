<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\DumpMail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Process;
use RuntimeException;

#[Signature('fp:db:dump')]
#[Description('Dump prod db with pg_dump')]
final class ExecDailyDumpCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $config = config('database.connections.'.config('database.default'));

        $dir = storage_path('app/dumps');

        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        $fileName = '/fantapronostico_'.date('Y_m_d').'.sql';
        $fileLocation = $dir.$fileName;

        $result = Process::env(['PGPASSWORD' => $config['password']])->run([
            'pg_dump',
            '-h', $config['host'],
            '-p', (string) ($config['port'] ?? 5432),
            '-U', $config['username'],
            '-f', $fileLocation,
            $config['database'],
        ]);

        Mail::send(new DumpMail($fileName));

        if ($result->successful()) {
            $this->info("Dump saved: {$fileLocation}");
        } else {
            $this->error('Dump failed: '.$result->errorOutput());
        }
    }
}

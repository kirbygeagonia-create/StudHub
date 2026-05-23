<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'studhub:backup-database';

    protected $description = 'Create a gzipped database dump and store it on the backup disk';

    public function handle(): int
    {
        try {
            $connection = Config::get('database.default');
            $config = Config::get("database.connections.{$connection}");

            $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql.gz';
            $tempPath = storage_path('app/' . $filename);

            if ($config['driver'] === 'sqlite') {
                $dbPath = $config['database'];
                if ($dbPath === ':memory:') {
                    $this->warn('Running in SQLite in-memory — no backup possible.');
                    Log::warning('Database backup skipped: SQLite in-memory');

                    return self::FAILURE;
                }

                $process = Process::fromShellCommandline(
                    sprintf('gzip -c %s > %s', escapeshellarg($dbPath), escapeshellarg($tempPath))
                );
            } else {
                $commandParts = [
                    'mysqldump',
                    sprintf('--user=%s', escapeshellarg($config['username'] ?? '')),
                    sprintf('--password=%s', escapeshellarg($config['password'] ?? '')),
                    sprintf('--host=%s', escapeshellarg($config['host'] ?? '127.0.0.1')),
                    sprintf('--port=%s', (string) ($config['port'] ?? 3306)),
                    escapeshellarg($config['database'] ?? ''),
                    '--single-transaction',
                    '--routines',
                    '--triggers',
                    '--events',
                    '--quick',
                    '--skip-lock-tables',
                ];

                $commandStr = implode(' ', $commandParts) . ' | gzip > ' . escapeshellarg($tempPath);
                $process = Process::fromShellCommandline($commandStr);
            }

            $process->run();

            if (! $process->isSuccessful()) {
                $this->error('Backup command failed with exit code ' . $process->getExitCode());
                $this->line($process->getErrorOutput() ?: $process->getOutput());
                Log::error('Database backup failed', [
                    'exit_code' => $process->getExitCode(),
                    'error' => $process->getErrorOutput(),
                ]);

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                return self::FAILURE;
            }

            if (! file_exists($tempPath)) {
                $this->error('Backup file was not created.');
                Log::error('Database backup: file not created', ['path' => $tempPath]);

                return self::FAILURE;
            }

            $stored = Storage::disk('backups')->put($filename, fopen($tempPath, 'r'));
            unlink($tempPath);

            if ($stored) {
                $this->info("Database backup saved: backups/{$filename}");
                Log::info('Database backup created', ['file' => $filename]);

                $this->cleanOldBackups();
            } else {
                $this->error('Failed to store backup on backup disk.');
                Log::error('Database backup: failed to store', ['file' => $filename]);

                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            Log::error('Database backup exception', ['exception' => $e->getMessage()]);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function cleanOldBackups(): void
    {
        $files = Storage::disk('backups')->files();
        $cutoff = now()->subDays((int) config('studhub.backup_retention_days', 7));
        $deleted = 0;

        foreach ($files as $file) {
            if (str_starts_with(basename($file), 'backup-')) {
                $lastModified = Storage::disk('backups')->lastModified($file);
                if ($lastModified && $lastModified < $cutoff->getTimestamp()) {
                    Storage::disk('backups')->delete($file);
                    $deleted++;
                }
            }
        }

        if ($deleted > 0) {
            $this->info("Cleaned {$deleted} old backup(s) older than 7 days.");
            Log::info('Old backups cleaned', ['deleted' => $deleted]);
        }
    }
}

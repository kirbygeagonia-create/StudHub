<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'studhub:backup-database';

    protected $description = 'Create a gzipped database dump and store it on the backup disk';

    public function handle(): int
    {
        $connection = Config::get('database.default');
        $config = Config::get("database.connections.{$connection}");

        $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql.gz';
        $tempPath = storage_path('app/' . $filename);

        if ($config['driver'] === 'sqlite') {
            $dbPath = $config['database'];
            if ($dbPath === ':memory:') {
                $this->warn('Running in SQLite in-memory — no backup possible.');

                return self::FAILURE;
            }

            $command = sprintf('gzip -c %s > %s 2>&1', escapeshellarg($dbPath), escapeshellarg($tempPath));
        } else {
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s --single-transaction --quick --skip-lock-tables 2>&1 | gzip > %s 2>&1',
                escapeshellarg($config['username'] ?? ''),
                escapeshellarg($config['password'] ?? ''),
                escapeshellarg($config['host'] ?? '127.0.0.1'),
                escapeshellarg((string) ($config['port'] ?? 3306)),
                escapeshellarg($config['database'] ?? ''),
                escapeshellarg($tempPath)
            );
        }

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $this->error('Backup command failed with exit code ' . $exitCode);
            $this->line(implode("\n", $output));

            return self::FAILURE;
        }

        if (file_exists($tempPath)) {
            $stored = Storage::disk('backups')->put($filename, fopen($tempPath, 'r'));
            unlink($tempPath);

            if ($stored) {
                $this->info("Database backup saved: backups/{$filename}");

                $this->cleanOldBackups();
            } else {
                $this->error('Failed to store backup on backup disk.');

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    private function cleanOldBackups(): void
    {
        $files = Storage::disk('backups')->files();
        $cutoff = now()->subDays(7);
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
        }
    }
}

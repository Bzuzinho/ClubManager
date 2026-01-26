<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database 
                            {--compress : Compress the backup file}
                            {--disk= : Storage disk to use (default: backups)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database backup...');

        $timestamp = now()->format('Y-m-d_His');
        $filename = "database_{$timestamp}.sql";
        $disk = $this->option('disk') ?? 'backups';

        try {
            // Get database configuration
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $host = config("database.connections.{$connection}.host");
            $port = config("database.connections.{$connection}.port");

            // Create backup directory if not exists
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            $filePath = $backupPath . '/' . $filename;

            // Create mysqldump command
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            // Execute backup
            $this->info("Backing up database: {$database}");
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception('Mysqldump failed');
            }

            // Compress if requested
            if ($this->option('compress')) {
                $this->info('Compressing backup...');
                $compressedPath = $filePath . '.gz';
                exec("gzip -c {$filePath} > {$compressedPath}");
                unlink($filePath);
                $filePath = $compressedPath;
                $filename .= '.gz';
            }

            // Upload to storage disk if not local
            if ($disk !== 'local') {
                $this->info("Uploading to {$disk} disk...");
                $contents = file_get_contents($filePath);
                Storage::disk($disk)->put("backups/{$filename}", $contents);
                unlink($filePath);
            }

            $fileSize = Storage::disk($disk)->size("backups/{$filename}");
            $this->info('Backup completed successfully!');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Database', $database],
                    ['Filename', $filename],
                    ['Size', $this->formatBytes($fileSize)],
                    ['Disk', $disk],
                    ['Timestamp', $timestamp],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Format bytes to human readable size
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}

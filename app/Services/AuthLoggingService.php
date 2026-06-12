<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class AuthLoggingService
{
    const LOG_DIR = 'storage/logs/auth';

    public function __construct()
    {
        $this->ensureLogDirectory();
    }

    /**
     * Ensure the log directory exists
     */
    protected function ensureLogDirectory(): void
    {
        if (!File::isDirectory(storage_path('logs/auth'))) {
            File::makeDirectory(storage_path('logs/auth'), 0755, true);
        }
    }

    /**
     * Log login attempt
     * 
     * @param string $username
     * @param bool $success
     * @param string $message
     * @return void
     */
    public function logLoginAttempt(string $username, bool $success, string $message): void
    {
        $logEntry = $this->formatLogEntry(
            'LOGIN',
            $username,
            $success,
            $message
        );

        $this->writeToFile($logEntry);
    }

    /**
     * Log logout attempt
     * 
     * @param string $username
     * @param bool $success
     * @param string $message
     * @return void
     */
    public function logLogoutAttempt(string $username, bool $success, string $message): void
    {
        $logEntry = $this->formatLogEntry(
            'LOGOUT',
            $username,
            $success,
            $message
        );

        $this->writeToFile($logEntry);
    }

    /**
     * Format log entry
     * 
     * @param string $action
     * @param string $username
     * @param bool $success
     * @param string $message
     * @return string
     */
    protected function formatLogEntry(string $action, string $username, bool $success, string $message): string
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        $status = $success ? 'SUCCESS' : 'FAILED';
        $ipAddress = request()->ip();
        $userAgent = request()->header('User-Agent');

        return "[{$timestamp}] ACTION: {$action} | USERNAME: {$username} | STATUS: {$status} | MESSAGE: {$message} | IP: {$ipAddress} | USER-AGENT: {$userAgent}" . PHP_EOL;
    }

    /**
     * Write to daily log file
     * 
     * @param string $content
     * @return void
     */
    protected function writeToFile(string $content): void
    {
        try {
            $date = Carbon::now()->format('Y-m-d');
            $logFile = storage_path("logs/auth/auth_{$date}.txt");

            File::append($logFile, $content);
        } catch (\Exception $e) {
            // Fallback to Laravel's default logging
            Log::error('Failed to write auth log: ' . $e->getMessage());
        }
    }

    /**
     * Get today's logs
     * 
     * @return string
     */
    public function getTodayLogs(): string
    {
        try {
            $date = Carbon::now()->format('Y-m-d');
            $logFile = storage_path("logs/auth/auth_{$date}.txt");

            if (File::exists($logFile)) {
                return File::get($logFile);
            }

            return 'No logs for today.';
        } catch (\Exception $e) {
            return 'Error reading logs: ' . $e->getMessage();
        }
    }

    /**
     * Get logs for a specific date
     * 
     * @param string $date (format: Y-m-d)
     * @return string
     */
    public function getLogsByDate(string $date): string
    {
        try {
            $logFile = storage_path("logs/auth/auth_{$date}.txt");

            if (File::exists($logFile)) {
                return File::get($logFile);
            }

            return "No logs for {$date}.";
        } catch (\Exception $e) {
            return 'Error reading logs: ' . $e->getMessage();
        }
    }

    /**
     * Get all available log files
     * 
     * @return array
     */
    public function getAllLogFiles(): array
    {
        try {
            $logDir = storage_path('logs/auth');

            if (!File::isDirectory($logDir)) {
                return [];
            }

            $files = File::files($logDir);
            $logFiles = [];

            foreach ($files as $file) {
                $logFiles[] = [
                    'filename' => $file->getFilename(),
                    'date' => str_replace(['auth_', '.txt'], '', $file->getFilename()),
                    'size' => $file->getSize(),
                    'last_modified' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                ];
            }

            return array_reverse($logFiles);
        } catch (\Exception $e) {
            return [];
        }
    }
}

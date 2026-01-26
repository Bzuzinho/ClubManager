<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Log;

/**
 * LoggingService - Serviço centralizado de logging estruturado
 */
class LoggingService
{
    /**
     * Log de autenticação
     */
    public static function logAuth(string $action, ?int $userId = null, array $context = []): void
    {
        Log::channel('auth')->info($action, array_merge([
            'user_id' => $userId,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    /**
     * Log de API
     */
    public static function logApi(string $method, string $endpoint, int $status, float $duration = 0, array $context = []): void
    {
        Log::channel('api')->info("$method $endpoint", array_merge([
            'method' => $method,
            'endpoint' => $endpoint,
            'status' => $status,
            'duration_ms' => round($duration * 1000, 2),
            'user_id' => auth()->id(),
            'club_id' => session('active_club_id'),
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    /**
     * Log financeiro (crítico)
     */
    public static function logFinancial(string $action, array $data): void
    {
        Log::channel('financial')->info($action, array_merge([
            'user_id' => auth()->id(),
            'club_id' => session('active_club_id'),
            'timestamp' => now()->toIso8601String(),
        ], $data));
    }

    /**
     * Log de auditoria (compliance)
     */
    public static function logAudit(string $entity, string $action, int $entityId, array $changes = []): void
    {
        Log::channel('audit')->info("$entity.$action", [
            'entity' => $entity,
            'action' => $action,
            'entity_id' => $entityId,
            'user_id' => auth()->id(),
            'club_id' => session('active_club_id'),
            'changes' => $changes,
            'ip' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Log de performance (slow queries, etc)
     */
    public static function logPerformance(string $metric, float $value, string $unit = 'ms', array $context = []): void
    {
        if ($value < 1000) {
            return; // Só loga se for significativo (>1s)
        }

        Log::channel('performance')->warning("Slow $metric", array_merge([
            'metric' => $metric,
            'value' => $value,
            'unit' => $unit,
            'threshold_exceeded' => $value > 1000,
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    /**
     * Log de segurança
     */
    public static function logSecurity(string $event, string $severity = 'warning', array $context = []): void
    {
        Log::channel('security')->{$severity}($event, array_merge([
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ], $context));
    }

    /**
     * Log de erro crítico (também notifica Sentry)
     */
    public static function logCritical(\Throwable $exception, array $context = []): void
    {
        Log::critical($exception->getMessage(), array_merge([
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
            'club_id' => session('active_club_id'),
            'timestamp' => now()->toIso8601String(),
        ], $context));

        // Também notifica Sentry se disponível
        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}

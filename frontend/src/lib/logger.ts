type LogLevel = 'debug' | 'info' | 'warn' | 'error';

interface LogContext {
  [key: string]: any;
}

class Logger {
  private isDevelopment = import.meta.env.DEV;
  private isProduction = import.meta.env.PROD;

  private formatMessage(level: LogLevel, message: string, context?: LogContext): string {
    const timestamp = new Date().toISOString();
    const contextStr = context ? ` | ${JSON.stringify(context)}` : '';
    return `[${timestamp}] [${level.toUpperCase()}] ${message}${contextStr}`;
  }

  private shouldLog(level: LogLevel): boolean {
    if (this.isDevelopment) return true;
    return level === 'error' || level === 'warn';
  }

  debug(message: string, context?: LogContext) {
    if (this.shouldLog('debug')) {
      console.debug(this.formatMessage('debug', message, context));
    }
  }

  info(message: string, context?: LogContext) {
    if (this.shouldLog('info')) {
      console.info(this.formatMessage('info', message, context));
    }
  }

  warn(message: string, context?: LogContext) {
    if (this.shouldLog('warn')) {
      console.warn(this.formatMessage('warn', message, context));
    }
  }

  error(message: string, error?: Error, context?: LogContext) {
    if (this.shouldLog('error')) {
      const errorContext = {
        ...context,
        error: error?.message,
        stack: error?.stack,
      };
      console.error(this.formatMessage('error', message, errorContext));
      
      // Send to external service in production
      if (this.isProduction && error) {
        this.sendToExternalService(message, error, context);
      }
    }
  }

  private sendToExternalService(message: string, error: Error, context?: LogContext) {
    // This would integrate with your logging service
    // For now, we just ensure it's logged
    try {
      // Example: send to backend logging endpoint
      fetch('/api/logs', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          level: 'error',
          message,
          error: {
            message: error.message,
            stack: error.stack,
            name: error.name,
          },
          context,
          timestamp: new Date().toISOString(),
          userAgent: navigator.userAgent,
          url: window.location.href,
        }),
      }).catch(console.error);
    } catch (e) {
      console.error('Failed to send log to external service:', e);
    }
  }

  // Performance logging
  performance(label: string, startTime: number) {
    const duration = performance.now() - startTime;
    this.debug(`Performance: ${label}`, { duration: `${duration.toFixed(2)}ms` });
    
    // Log slow operations
    if (duration > 1000) {
      this.warn(`Slow operation detected: ${label}`, { duration: `${duration.toFixed(2)}ms` });
    }
  }

  // API request logging
  apiRequest(method: string, url: string, status: number, duration: number) {
    const level = status >= 400 ? 'error' : 'info';
    this[level](`API ${method} ${url}`, {
      status,
      duration: `${duration.toFixed(2)}ms`,
    });
  }
}

export const logger = new Logger();
export default logger;

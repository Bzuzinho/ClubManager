// Mock Sentry até atualizar para versão compatível com React 19
// TODO: Instalar @sentry/react v8.x quando disponível para React 19

export type SeverityLevel = 'fatal' | 'error' | 'warning' | 'log' | 'info' | 'debug';

export const initSentry = () => {
  console.log('ℹ️  Sentry mock - monitoring desativado temporariamente (aguardando compatibilidade com React 19)');
};

// Mock ErrorBoundary
export const ErrorBoundary = ({ children }: { children: React.ReactNode }) => children;

// Capture exception manually (mock)
export const captureException = (error: Error, context?: Record<string, any>) => {
  console.error('Error captured (mock):', error, context);
};

// Set user context (mock)
export const setUser = (user: { id: number; email: string; name?: string } | null) => {
  if (user) {
    console.log('User set (mock):', user.email);
  }
};

// Add breadcrumb (mock)
export const addBreadcrumb = (message: string, category: string, level: SeverityLevel = 'info') => {
  if (import.meta.env.DEV) {
    console.log(`[${category}] ${message}`, { level });
  }
};

// Capture message (mock)
export const captureMessage = (message: string, level: SeverityLevel = 'info') => {
  console.log(`[${level}] ${message}`);
};

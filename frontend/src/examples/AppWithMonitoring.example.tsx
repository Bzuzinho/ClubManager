/**
 * Exemplo de integração completa de monitoring
 * Este arquivo mostra como inicializar todos os serviços de monitoring
 */

import { useEffect } from 'react';
import { initSentry, ErrorBoundary, setUser } from './lib/sentry';
import { logger } from './lib/logger';
import { initPerformanceMonitoring, performanceMark } from './lib/performance';
import { MonitoringDashboard } from './components/MonitoringDashboard';

// Inicializar Sentry no início
const environment = import.meta.env.PROD ? 'production' : 'development';
initSentry(environment);

// Componente de erro para ErrorBoundary
function ErrorFallback({ error }: { error: Error }) {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-50">
      <div className="max-w-md w-full bg-white shadow-lg rounded-lg p-6">
        <h2 className="text-xl font-bold text-red-600 mb-4">
          Algo deu errado
        </h2>
        <p className="text-gray-700 mb-4">
          Desculpe, ocorreu um erro inesperado. Nossa equipe foi notificada.
        </p>
        <details className="text-sm">
          <summary className="cursor-pointer text-gray-600 hover:text-gray-800">
            Detalhes técnicos
          </summary>
          <pre className="mt-2 p-2 bg-gray-100 rounded overflow-auto">
            {error.message}
          </pre>
        </details>
        <button
          onClick={() => window.location.reload()}
          className="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700"
        >
          Recarregar página
        </button>
      </div>
    </div>
  );
}

function AppWithMonitoring() {
  useEffect(() => {
    // Inicializar performance monitoring
    logger.info('Initializing application');
    performanceMark.start('app-init');
    
    initPerformanceMonitoring();
    
    const duration = performanceMark.end('app-init');
    logger.performance(`App initialized in ${duration?.toFixed(2)}ms`);
    
    // Verificar se há usuário logado
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      try {
        const user = JSON.parse(storedUser);
        setUser({
          id: user.id,
          email: user.email,
          username: user.name,
        });
        logger.info('User loaded from localStorage', { userId: user.id });
      } catch (error) {
        logger.warn('Failed to parse stored user', { error });
      }
    }
    
    // Cleanup
    return () => {
      logger.info('Application unmounting');
    };
  }, []);
  
  return (
    <ErrorBoundary fallback={(error) => <ErrorFallback error={error} />}>
      {/* Seu App aqui */}
      <div>
        <h1>ClubManager</h1>
        {/* Router, Routes, etc */}
      </div>
      
      {/* Dashboard de monitoring (apenas em dev) */}
      <MonitoringDashboard />
    </ErrorBoundary>
  );
}

export default AppWithMonitoring;

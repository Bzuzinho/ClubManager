/**
 * Monitoring Dashboard Component
 * Displays real-time application health metrics
 * Only visible in development or for admins
 */

import { useEffect, useState } from 'react';
import { logger } from '../lib/logger';
import { trackMemoryUsage } from '../lib/performance';

interface HealthMetrics {
  apiStatus: 'healthy' | 'degraded' | 'down';
  lastApiCall: string;
  errorCount: number;
  warningCount: number;
  memoryUsage?: {
    used: number;
    total: number;
    limit: number;
  };
}

export const MonitoringDashboard = () => {
  const [metrics, setMetrics] = useState<HealthMetrics>({
    apiStatus: 'healthy',
    lastApiCall: 'Never',
    errorCount: 0,
    warningCount: 0,
  });
  const [isVisible, setIsVisible] = useState(false);
  
  useEffect(() => {
    // Only show in development
    const isDev = import.meta.env.DEV;
    setIsVisible(isDev);
    
    if (!isDev) return;
    
    // Update metrics every 5 seconds
    const interval = setInterval(() => {
      updateMetrics();
    }, 5000);
    
    return () => clearInterval(interval);
  }, []);
  
  const updateMetrics = () => {
    // Get memory usage if available
    let memoryUsage;
    if (typeof performance !== 'undefined' && 'memory' in performance) {
      const memory = (performance as any).memory;
      memoryUsage = {
        used: memory.usedJSHeapSize / 1048576,
        total: memory.totalJSHeapSize / 1048576,
        limit: memory.jsHeapSizeLimit / 1048576,
      };
    }
    
    setMetrics(prev => ({
      ...prev,
      memoryUsage,
    }));
  };
  
  const getStatusColor = (status: string) => {
    switch (status) {
      case 'healthy': return 'text-green-600';
      case 'degraded': return 'text-yellow-600';
      case 'down': return 'text-red-600';
      default: return 'text-gray-600';
    }
  };
  
  const clearLogs = () => {
    logger.info('Logs cleared from monitoring dashboard');
    setMetrics(prev => ({
      ...prev,
      errorCount: 0,
      warningCount: 0,
    }));
  };
  
  const trackMemory = () => {
    trackMemoryUsage();
    updateMetrics();
  };
  
  if (!isVisible) return null;
  
  return (
    <div className="fixed bottom-4 right-4 bg-white border border-gray-300 rounded-lg shadow-lg p-4 w-80 z-50">
      <div className="flex justify-between items-center mb-3">
        <h3 className="font-bold text-sm">Monitoring Dashboard</h3>
        <button
          onClick={() => setIsVisible(false)}
          className="text-gray-500 hover:text-gray-700"
        >
          ✕
        </button>
      </div>
      
      <div className="space-y-2 text-xs">
        {/* API Status */}
        <div className="flex justify-between">
          <span className="text-gray-600">API Status:</span>
          <span className={`font-semibold ${getStatusColor(metrics.apiStatus)}`}>
            {metrics.apiStatus}
          </span>
        </div>
        
        {/* Last API Call */}
        <div className="flex justify-between">
          <span className="text-gray-600">Last API Call:</span>
          <span className="font-mono">{metrics.lastApiCall}</span>
        </div>
        
        {/* Error Count */}
        <div className="flex justify-between">
          <span className="text-gray-600">Errors:</span>
          <span className={`font-semibold ${metrics.errorCount > 0 ? 'text-red-600' : 'text-gray-900'}`}>
            {metrics.errorCount}
          </span>
        </div>
        
        {/* Warning Count */}
        <div className="flex justify-between">
          <span className="text-gray-600">Warnings:</span>
          <span className={`font-semibold ${metrics.warningCount > 0 ? 'text-yellow-600' : 'text-gray-900'}`}>
            {metrics.warningCount}
          </span>
        </div>
        
        {/* Memory Usage */}
        {metrics.memoryUsage && (
          <>
            <div className="border-t pt-2 mt-2">
              <div className="text-gray-600 mb-1">Memory Usage:</div>
              <div className="flex justify-between">
                <span className="text-gray-600">Used:</span>
                <span className="font-mono">{metrics.memoryUsage.used.toFixed(2)} MB</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-600">Total:</span>
                <span className="font-mono">{metrics.memoryUsage.total.toFixed(2)} MB</span>
              </div>
              <div className="flex justify-between">
                <span className="text-gray-600">Limit:</span>
                <span className="font-mono">{metrics.memoryUsage.limit.toFixed(2)} MB</span>
              </div>
              
              {/* Memory usage percentage bar */}
              <div className="mt-2">
                <div className="bg-gray-200 rounded-full h-2">
                  <div
                    className={`h-2 rounded-full ${
                      (metrics.memoryUsage.used / metrics.memoryUsage.limit) > 0.8
                        ? 'bg-red-600'
                        : 'bg-green-600'
                    }`}
                    style={{
                      width: `${(metrics.memoryUsage.used / metrics.memoryUsage.limit) * 100}%`
                    }}
                  />
                </div>
              </div>
            </div>
          </>
        )}
        
        {/* Actions */}
        <div className="border-t pt-2 mt-2 space-y-1">
          <button
            onClick={trackMemory}
            className="w-full text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 py-1 px-2 rounded"
          >
            Track Memory
          </button>
          <button
            onClick={clearLogs}
            className="w-full text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 py-1 px-2 rounded"
          >
            Clear Counters
          </button>
        </div>
      </div>
      
      <div className="text-xs text-gray-500 mt-3 text-center">
        Development Mode Only
      </div>
    </div>
  );
};

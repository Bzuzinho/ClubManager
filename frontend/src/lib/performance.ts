/**
 * Performance monitoring utilities
 * Tracks page load, API calls, and custom metrics
 */

import { addBreadcrumb } from './sentry';
import { logger } from './logger';

// Web Vitals types
interface WebVitalsMetric {
  name: string;
  value: number;
  rating: 'good' | 'needs-improvement' | 'poor';
  delta: number;
}

// Performance mark utilities
export const performanceMark = {
  start: (name: string) => {
    if (typeof performance !== 'undefined' && performance.mark) {
      performance.mark(`${name}-start`);
      addBreadcrumb(`Performance mark started: ${name}`, 'performance', 'info');
    }
  },
  
  end: (name: string) => {
    if (typeof performance !== 'undefined' && performance.mark && performance.measure) {
      try {
        performance.mark(`${name}-end`);
        performance.measure(name, `${name}-start`, `${name}-end`);
        
        const measure = performance.getEntriesByName(name)[0];
        const duration = measure?.duration || 0;
        
        logger.performance(`${name}: ${duration.toFixed(2)}ms`);
        addBreadcrumb(
          `Performance: ${name} took ${duration.toFixed(2)}ms`,
          'performance',
          duration > 1000 ? 'warning' : 'info'
        );
        
        return duration;
      } catch (error) {
        logger.warn('Performance measurement failed', { name, error });
      }
    }
    return null;
  },
  
  clear: (name: string) => {
    if (typeof performance !== 'undefined') {
      performance.clearMarks(`${name}-start`);
      performance.clearMarks(`${name}-end`);
      performance.clearMeasures(name);
    }
  },
};

// Resource timing observer
export const observeResources = () => {
  if (typeof PerformanceObserver === 'undefined') return;
  
  try {
    const observer = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        const resourceEntry = entry as PerformanceResourceTiming;
        
        // Only log slow resources (> 1s)
        if (resourceEntry.duration > 1000) {
          logger.warn('Slow resource loaded', {
            name: resourceEntry.name,
            duration: resourceEntry.duration.toFixed(2),
            type: resourceEntry.initiatorType,
          });
          
          addBreadcrumb(
            `Slow resource: ${resourceEntry.name} (${resourceEntry.duration.toFixed(0)}ms)`,
            'resource',
            'warning'
          );
        }
      }
    });
    
    observer.observe({ entryTypes: ['resource'] });
    return observer;
  } catch (error) {
    logger.warn('Failed to observe resources', { error });
  }
};

// Long task observer
export const observeLongTasks = () => {
  if (typeof PerformanceObserver === 'undefined') return;
  
  try {
    const observer = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        logger.warn('Long task detected', {
          duration: entry.duration.toFixed(2),
          startTime: entry.startTime.toFixed(2),
        });
        
        addBreadcrumb(
          `Long task: ${entry.duration.toFixed(0)}ms`,
          'performance',
          'warning'
        );
      }
    });
    
    observer.observe({ entryTypes: ['longtask'] });
    return observer;
  } catch (error) {
    logger.warn('Failed to observe long tasks', { error });
  }
};

// Web Vitals tracking
export const trackWebVitals = () => {
  // Track Largest Contentful Paint (LCP)
  if (typeof PerformanceObserver !== 'undefined') {
    try {
      const lcpObserver = new PerformanceObserver((list) => {
        const entries = list.getEntries();
        const lastEntry = entries[entries.length - 1] as any;
        
        const lcp = lastEntry.renderTime || lastEntry.loadTime;
        logger.performance(`LCP: ${lcp.toFixed(2)}ms`);
        
        addBreadcrumb(
          `Web Vitals: LCP ${lcp.toFixed(0)}ms`,
          'performance',
          lcp > 2500 ? 'warning' : 'info'
        );
      });
      
      lcpObserver.observe({ entryTypes: ['largest-contentful-paint'] });
    } catch (error) {
      logger.warn('Failed to observe LCP', { error });
    }
    
    // Track First Input Delay (FID)
    try {
      const fidObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          const fid = (entry as any).processingStart - entry.startTime;
          logger.performance(`FID: ${fid.toFixed(2)}ms`);
          
          addBreadcrumb(
            `Web Vitals: FID ${fid.toFixed(0)}ms`,
            'performance',
            fid > 100 ? 'warning' : 'info'
          );
        }
      });
      
      fidObserver.observe({ entryTypes: ['first-input'] });
    } catch (error) {
      logger.warn('Failed to observe FID', { error });
    }
  }
  
  // Track Cumulative Layout Shift (CLS)
  if (typeof PerformanceObserver !== 'undefined') {
    try {
      let clsValue = 0;
      
      const clsObserver = new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          if (!(entry as any).hadRecentInput) {
            clsValue += (entry as any).value;
          }
        }
        
        logger.performance(`CLS: ${clsValue.toFixed(4)}`);
        
        addBreadcrumb(
          `Web Vitals: CLS ${clsValue.toFixed(4)}`,
          'performance',
          clsValue > 0.1 ? 'warning' : 'info'
        );
      });
      
      clsObserver.observe({ entryTypes: ['layout-shift'] });
    } catch (error) {
      logger.warn('Failed to observe CLS', { error });
    }
  }
};

// Initialize all performance monitoring
export const initPerformanceMonitoring = () => {
  // Only in browser environment
  if (typeof window === 'undefined') return;
  
  logger.info('Initializing performance monitoring');
  
  // Track page load
  if (typeof performance !== 'undefined' && performance.timing) {
    window.addEventListener('load', () => {
      setTimeout(() => {
        const timing = performance.timing;
        const loadTime = timing.loadEventEnd - timing.navigationStart;
        const domReady = timing.domContentLoadedEventEnd - timing.navigationStart;
        
        logger.performance('Page Load Metrics', {
          loadTime: `${loadTime}ms`,
          domReady: `${domReady}ms`,
          domInteractive: `${timing.domInteractive - timing.navigationStart}ms`,
        });
        
        addBreadcrumb(
          `Page loaded in ${loadTime}ms`,
          'navigation',
          loadTime > 3000 ? 'warning' : 'info'
        );
      }, 0);
    });
  }
  
  // Start observers
  observeResources();
  observeLongTasks();
  trackWebVitals();
  
  logger.info('Performance monitoring initialized');
};

// Custom metric tracking
export const trackCustomMetric = (name: string, value: number, unit = 'ms') => {
  logger.performance(`Custom Metric: ${name} = ${value}${unit}`);
  
  addBreadcrumb(
    `Metric: ${name} = ${value}${unit}`,
    'performance',
    'info'
  );
};

// Memory usage tracking (if available)
export const trackMemoryUsage = () => {
  if (typeof performance !== 'undefined' && 'memory' in performance) {
    const memory = (performance as any).memory;
    
    logger.performance('Memory Usage', {
      used: `${(memory.usedJSHeapSize / 1048576).toFixed(2)} MB`,
      total: `${(memory.totalJSHeapSize / 1048576).toFixed(2)} MB`,
      limit: `${(memory.jsHeapSizeLimit / 1048576).toFixed(2)} MB`,
    });
  }
};

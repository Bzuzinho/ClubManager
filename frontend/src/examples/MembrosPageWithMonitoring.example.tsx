/**
 * Exemplo de componente com tratamento completo de erros e logging
 */

import { useState, useEffect } from 'react';
import { apiClient } from '../lib/api';
import { logger } from '../lib/logger';
import { performanceMark } from '../lib/performance';
import { captureException, addBreadcrumb } from '../lib/sentry';

interface Membro {
  id: number;
  nome: string;
  email: string;
  status: string;
}

export function MembrosPageExample() {
  const [membros, setMembros] = useState<Membro[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  
  useEffect(() => {
    fetchMembros();
  }, []);
  
  const fetchMembros = async () => {
    try {
      // Log início da operação
      logger.info('Fetching membros');
      addBreadcrumb('User viewing membros list', 'navigation', 'info');
      
      // Performance tracking
      performanceMark.start('fetch-membros');
      
      setLoading(true);
      setError(null);
      
      // API call com type-safety
      const response = await apiClient.get<{ data: Membro[] }>('/membros');
      
      // Performance tracking
      const duration = performanceMark.end('fetch-membros');
      
      setMembros(response.data.data);
      
      logger.info('Membros fetched successfully', {
        count: response.data.data.length,
        duration: `${duration?.toFixed(2)}ms`,
      });
      
    } catch (err: any) {
      // Error handling
      const errorMessage = err.response?.data?.message || 'Erro ao carregar membros';
      
      setError(errorMessage);
      
      // Log error
      logger.error('Failed to fetch membros', err, {
        status: err.response?.status,
        url: '/membros',
      });
      
      // Capture em Sentry apenas se for erro inesperado
      if (err.response?.status >= 500) {
        captureException(err, {
          context: 'MembrosPage',
          operation: 'fetchMembros',
        });
      }
      
    } finally {
      setLoading(false);
    }
  };
  
  const handleDelete = async (id: number) => {
    try {
      addBreadcrumb(`Deleting membro ${id}`, 'user-action', 'info');
      
      await apiClient.delete(`/membros/${id}`);
      
      // Update local state
      setMembros(prev => prev.filter(m => m.id !== id));
      
      logger.info('Membro deleted successfully', { membroId: id });
      
    } catch (err: any) {
      logger.error('Failed to delete membro', err, { membroId: id });
      
      // User feedback
      alert('Erro ao deletar membro. Tente novamente.');
      
      // Capture error
      captureException(err, {
        context: 'MembrosPage',
        operation: 'deleteMembro',
        membroId: id,
      });
    }
  };
  
  if (loading) {
    return (
      <div className="flex justify-center items-center h-64">
        <div className="text-gray-600">Carregando...</div>
      </div>
    );
  }
  
  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded p-4">
        <p className="text-red-800">{error}</p>
        <button
          onClick={fetchMembros}
          className="mt-2 text-red-600 hover:text-red-800 underline"
        >
          Tentar novamente
        </button>
      </div>
    );
  }
  
  return (
    <div>
      <h1 className="text-2xl font-bold mb-4">Membros</h1>
      
      <div className="space-y-2">
        {membros.map(membro => (
          <div
            key={membro.id}
            className="flex justify-between items-center p-4 bg-white border rounded"
          >
            <div>
              <div className="font-semibold">{membro.nome}</div>
              <div className="text-sm text-gray-600">{membro.email}</div>
            </div>
            
            <button
              onClick={() => handleDelete(membro.id)}
              className="text-red-600 hover:text-red-800"
            >
              Deletar
            </button>
          </div>
        ))}
      </div>
    </div>
  );
}

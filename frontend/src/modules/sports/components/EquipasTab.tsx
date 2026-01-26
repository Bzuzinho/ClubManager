import { useState, useEffect } from 'react';
import { equipasApi } from '../api';
import type { Equipa } from '../types';

export function EquipasTab() {
  const [equipas, setEquipas] = useState<Equipa[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadEquipas();
  }, []);

  const loadEquipas = async () => {
    try {
      setLoading(true);
      const response = await equipasApi.list();
      setEquipas(response.data);
    } catch (error) {
      console.error('Erro ao carregar equipas:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: 16 }}>
        <button className="btn primary">Nova Equipa</button>
      </div>

      <div className="card">
        {loading ? (
          <div style={{ textAlign: 'center', padding: 40 }}>Carregando...</div>
        ) : equipas.length === 0 ? (
          <div style={{ textAlign: 'center', padding: 40, opacity: 0.7 }}>
            Nenhuma equipa encontrada
          </div>
        ) : (
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(300px, 1fr))', gap: 16 }}>
            {equipas.map((equipa) => (
              <div
                key={equipa.id}
                style={{
                  border: '1px solid #e2e8f0',
                  borderRadius: 8,
                  padding: 16,
                  backgroundColor: '#fff',
                }}
              >
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'start' }}>
                  <div>
                    <h3 style={{ margin: 0, fontSize: 18, fontWeight: 600 }}>{equipa.nome}</h3>
                    <p style={{ margin: '4px 0', fontSize: 14, opacity: 0.7 }}>
                      {equipa.escalao?.nome || 'Sem escalão'}
                    </p>
                  </div>
                  <span
                    style={{
                      backgroundColor: '#dbeafe',
                      color: '#1e40af',
                      padding: '4px 8px',
                      borderRadius: 4,
                      fontSize: 12,
                    }}
                  >
                    {equipa.temporada}
                  </span>
                </div>

                <div style={{ marginTop: 12, paddingTop: 12, borderTop: '1px solid #e2e8f0' }}>
                  <div style={{ fontSize: 14, opacity: 0.7 }}>Atletas</div>
                  <div style={{ fontSize: 24, fontWeight: 700, color: '#3b82f6' }}>
                    {equipa.atletas_count || 0}
                  </div>
                </div>

                <div style={{ display: 'flex', gap: 8, marginTop: 12 }}>
                  <button className="btn" style={{ flex: 1, fontSize: 12 }}>
                    Ver Detalhes
                  </button>
                  <button className="btn" style={{ fontSize: 12 }}>
                    Editar
                  </button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}

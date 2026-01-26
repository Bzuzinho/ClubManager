import { useState } from 'react';
import { AtletasTab } from './components/AtletasTab';
import { EquipasTab } from './components/EquipasTab';
import { TreinosTab } from './components/TreinosTab';

type Tab = 'atletas' | 'equipas' | 'treinos';

export default function Sports() {
  const [activeTab, setActiveTab] = useState<Tab>('atletas');

  const tabs: { id: Tab; label: string }[] = [
    { id: 'atletas', label: 'Atletas' },
    { id: 'equipas', label: 'Equipas' },
    { id: 'treinos', label: 'Treinos' },
  ];

  return (
    <div>
      <div style={{ marginBottom: 20 }}>
        <h1 style={{ fontSize: 24, fontWeight: 900, margin: 0 }}>Desportivo</h1>
        <p style={{ opacity: 0.7, margin: '4px 0 0 0' }}>Gestão de atletas, equipas e treinos</p>
      </div>

      {/* Tabs */}
      <div style={{ borderBottom: '2px solid #e2e8f0', marginBottom: 20 }}>
        <div style={{ display: 'flex', gap: 8 }}>
          {tabs.map((tab) => (
            <button
              key={tab.id}
              onClick={() => setActiveTab(tab.id)}
              style={{
                padding: '12px 24px',
                backgroundColor: 'transparent',
                border: 'none',
                borderBottom: activeTab === tab.id ? '2px solid #3b82f6' : '2px solid transparent',
                color: activeTab === tab.id ? '#3b82f6' : '#64748b',
                fontWeight: activeTab === tab.id ? 600 : 400,
                cursor: 'pointer',
                marginBottom: -2,
              }}
            >
              {tab.label}
            </button>
          ))}
        </div>
      </div>

      {/* Tab Content */}
      {activeTab === 'atletas' && <AtletasTab />}
      {activeTab === 'equipas' && <EquipasTab />}
      {activeTab === 'treinos' && <TreinosTab />}
    </div>
  );
}

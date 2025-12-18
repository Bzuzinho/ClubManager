# Guia de Estilo e Regras Visuais

Este documento define a linha gráfica a ser seguida para a interface do sistema de gestão de clube. O objectivo é garantir consistência visual, transmitir profissionalismo e proporcionar uma experiência agradável e eficiente para utilizadores administrativos e desportivos.

## Direcção de design

O interface deve ser **profissional e inspirar confiança**, semelhante a dashboards corporativos. Dado o volume de informação que será apresentado (perfis, dados financeiros e desportivos), opta‑se por um design organizado e estruturado, evitando minimalismo excessivo【456748431774537†L244-L248】.

## Paleta de cores

O esquema de cores é triádico, com três tons principais que equilibram fiabilidade, energia desportiva e harmonia:

| Papel | Cor | Uso |
| --- | --- | --- |
| **Cor Primária** | **Deep Professional Blue** – `oklch(0.45 0.15 250)` | Acções principais, botões de confirmação e elementos de destaque 【456748431774537†L250-L269】. |
| **Cor Secundária** | **Soft Neutral Gray** – `oklch(0.95 0.005 250)` e **Charcoal** – `oklch(0.35 0.01 250)` | Fundos, cartões e texto de suporte【456748431774537†L256-L268】. |
| **Cor de Acento** | **Vibrant Sports Orange** – `oklch(0.68 0.18 45)` | Realce de CTA, indicadores desportivos e alertas【456748431774537†L261-L273】. |
| **Pairings** | Branco, cinza claro, carvão escuro | As combinações garantem contraste acessível (ex. azul com texto branco: relação 7.2:1)【456748431774537†L256-L273】. |

## Tipografia

Usa‑se a fonte **Inter** em diferentes pesos e tamanhos para estabelecer hierarquia. A legibilidade em tamanhos pequenos é essencial para formulários e tabelas densas【456748431774537†L277-L288】.

| Nível | Peso e tamanho | Uso |
| --- | --- | --- |
| **H1** | Inter SemiBold, 32 px, espaçamento de ‑0,02 em | Títulos de secção【456748431774537†L282-L284】. |
| **H2** | Inter SemiBold, 24 px | Nomes de abas e subtítulos【456748431774537†L282-L285】. |
| **H3** | Inter Medium, 18 px | Agrupamentos de campos【456748431774537†L285-L287】. |
| **Corpo (labels)** | Inter Regular, 14 px | Etiquetas de campos e texto informativo【456748431774537†L285-L288】. |
| **Corpo (valores)** | Inter Regular, 14 px | Valores introduzidos ou lidos. |
| **Pequeno** | Inter Regular, 12 px | Textos de ajuda e comentários【456748431774537†L287-L289】. |
| **Badges** | Inter Medium, 12 px, espaçamento de 0,02 em | Etiquetas de estado (Activo/Inactivo/Suspenso)【456748431774537†L289-L290】. |

## Animações e movimento

As animações servem para transmitir mudança de estado de forma discreta, evitando distracções num ambiente orientado a dados. As durações recomendadas são:

- Transições de abas: **300 ms**
- Aparecimento de campos condicionais: **200 ms**
- Confirmações de sucesso (toast): **150 ms**

Devem ser usadas animações apenas quando ajudam a manter o contexto (por ex. ao alternar entre separadores ou ao guardar um formulário)【456748431774537†L292-L300】.

## Componentes e padrões UI

A aplicação utiliza a biblioteca **Shadcn UI**, adaptada às necessidades do sistema. Os principais componentes e respectivas funções incluem:

- **Tabs** – para navegar entre secções (perfil de utilizador, módulos, etc.)【456748431774537†L303-L310】.
- **Form/Input/Label/Textarea** – elementos de entrada de dados com estilos consistentes【456748431774537†L303-L310】.
- **Select/Checkbox/RadioGroup** – campos de escolha com acessibilidade nativa【456748431774537†L303-L311】.
- **Card** – contêiner com leve elevação, utilizado para agrupar campos【456748431774537†L303-L312】.
- **Button** – botões primários (cor azul) e secundários; incluem estados de hover, active, loading e disabled【456748431774537†L336-L339】.
- **Avatar** – fotografias de perfil com fallback em iniciais【456748431774537†L313-L314】.
- **Badge** – indicadores de estado com cores específicas (por exemplo, laranja para suspenso, verde para activo)【456748431774537†L314-L331】.
- **Dialog/Sheet** – modais para confirmações ou edição de dados críticos【456748431774537†L316-L317】.
- **Calendar/Popover** – para selecção de datas【456748431774537†L318-L318】.
- **Toast (Sonner)** – notificação discreta após guardar ou em caso de erro【456748431774537†L319-L320】.
- **Table** – listas tabulares com colunas ordenáveis na lista de membros【456748431774537†L320-L321】.
- **Switch** – interruptores para campos booleanos (menor, activo, consentimento)【456748431774537†L320-L322】.

### Personalizações

Além dos componentes de base, são previstos elementos personalizados:

- **Carregamento de ficheiros** com visualização e suporte a múltiplos anexos【456748431774537†L324-L326】.
- **Seletor de utilizador** com pesquisa para associar encarregados e atletas【456748431774537†L323-L330】.
- **Grupos de campos condicionais** que mostram/escondem secções conforme o tipo de membro【456748431774537†L323-L330】.
- **Indicadores de estado** com cores e etiquetas próprias para “Activo”, “Inactivo” e “Suspenso”【456748431774537†L331-L331】.

### Estados visuais

Cada componente possui estados bem definidos:

- **Inputs** – normal, focado (anel azul), preenchido, erro (borda vermelha com mensagem) e desactivado【456748431774537†L333-L339】.
- **Buttons** – normal, hover (ligeiro aumento de escala e brilho), activo (pressionado), loading (spinner) e desactivado【456748431774537†L336-L339】.
- **Tabs** – inactivo (cinza), hover (subtile realce) e activo (negrito com linha inferior)【456748431774537†L336-L339】.
- **Uploads de ficheiros** – estado vazio com ícone, progresso de upload, concluído com pré‑visualização e estado de erro【456748431774537†L340-L341】.

### Espaçamento e layout

A consistência do layout é garantida por unidades fixas (rem) baseadas em Tailwind:

- Espaço entre grupos de campos: **24 px**
- Espaço entre campos individuais: **16 px**
- Padding de cartões: **24 px** no desktop e **16 px** no mobile
- Padding de conteúdo de abas: **24 px**
- Margin entre secções principais: **32 px**
- Botões: `px-4 py-2` para tamanho normal; `px-6 py-3` para acções primárias【456748431774537†L354-L359】.

## Responsividade e acessibilidade

No mobile (< 768 px) as abas convertem‑se em botões de largura total; formulários passam de duas colunas para uma; o avatar reduz‑se de 128 px para 80 px; cartões de upload empilham‑se verticalmente; e surge uma barra fixa de acções no fundo para **Guardar/Cancelar**【456748431774537†L362-L367】. Todos os componentes devem respeitar padrões de acessibilidade (contraste mínimo 4,5:1, navegação por teclado e leitura por leitores de ecrã).

## Ícones sugeridos

Utilizar ícones simples da biblioteca de ícones do Shadcn ou equivalentes, com significados claros: Utilizador (perfil), Calendário (datas), Upload/File (documentos), Lupa (pesquisa), Lápis (editar), Check/X (guardar/cancelar), Aviso (erros de validação), Info (dicas) e Plus (adicionar)【456748431774537†L343-L352】.

---

Este guia deve ser aplicado a todo o sistema para garantir que novas funcionalidades mantenham consistência visual e usabilidade. Ajustes pontuais podem ser discutidos, mas a base cromática, tipográfica e de componentes deve permanecer estável.
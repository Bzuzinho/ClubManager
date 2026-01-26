# Guia de Documentação - ClubManager

## Sistema Automático de Documentação

Este projeto possui um sistema automático que mantém a documentação sempre actualizada.

## Como Funciona

### 1. Git Hook Automático
Sempre que fizer um commit, a documentação é automaticamente actualizada:
- O hook `pre-commit` executa o script `generate_docs.sh`
- Todos os ficheiros README são copiados para a pasta `docs/`
- Um índice completo é gerado em `docs/INDEX.md`
- As alterações são incluídas automaticamente no commit

### 2. Execução Manual
Pode actualizar a documentação manualmente a qualquer momento:
```bash
./generate_docs.sh
```

## Boas Práticas de Documentação

### Ao Criar Novos Ficheiros
1. **Controladores**: Adicione comentários PHPDoc no topo de cada método
   ```php
   /**
    * Descrição do que o método faz
    * @param Request $request
    * @return JsonResponse
    */
   public function index(Request $request)
   ```

2. **Modelos**: Documente relações e propriedades importantes
   ```php
   /**
    * Modelo User
    * @property int $id
    * @property string $name
    * @property string $email
    */
   class User extends Model
   ```

3. **Componentes React**: Use comentários JSDoc
   ```typescript
   /**
    * Componente de login de utilizador
    * @param {LoginProps} props - Propriedades do componente
    */
   export const Login: React.FC<LoginProps> = (props) => {
   ```

### Ao Criar Novos Módulos
1. Crie um ficheiro `README.md` na pasta do módulo
2. Inclua:
   - Descrição do módulo
   - Como usar
   - Dependências
   - Exemplos

### Estrutura da Documentação

```
docs/
├── INDEX.md              # Índice geral automático
├── README.md             # Documentação principal
├── backend-README.md     # Documentação do backend
├── frontend-README.md    # Documentação do frontend
└── [outros ficheiros]    # Documentação adicional
```

## Verificação

Antes de fazer push, verifique:
```bash
# Ver documentação gerada
ls -la docs/

# Ler o índice
cat docs/INDEX.md
```

## Resolução de Problemas

### O hook não está a funcionar
```bash
chmod +x .git/hooks/pre-commit
chmod +x generate_docs.sh
```

### Documentação desactualizada
```bash
./generate_docs.sh
git add docs/
git commit -m "docs: actualizar documentação"
```

## Integração CI/CD (Futuro)

Para integrar com GitHub Actions ou outro CI/CD, adicione ao workflow:
```yaml
- name: Gerar Documentação
  run: |
    chmod +x generate_docs.sh
    ./generate_docs.sh
```

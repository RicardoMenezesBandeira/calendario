# 📋 Funcionalidades de Eventos - Calendário UFF

## ✅ Implementado: Editar e Deletar Eventos

### 🎯 Visão Geral

O sistema agora possui funcionalidade completa de gerenciamento de eventos (marcações):

| Operação | Status | Descrição |
|----------|--------|-----------|
| **Criar Evento** | ✅ | Adicionar novo evento ao calendário |
| **Editar Evento** | ✅ | Modificar eventos existentes |
| **Deletar Evento** | ✅ | Remover eventos do calendário |
| **Visualizar** | ✅ | Ver detalhes dos eventos |

---

## 📁 Arquivos Modificados/Criados

### Backend (PHP)

**`events_add.php`** (Existente)
- Cria novos eventos
- Valida dados e campos obrigatórios
- Atribui líder responsável automaticamente

**`events_edit.php`** (✨ Novo)
- Edita eventos existentes
- Validação de autorização:
  - ✅ **Gerente**: Pode editar qualquer evento
  - ✅ **Líder**: Pode editar apenas seus próprios eventos
  - ❌ **Colaborador**: Não tem permissão
- Valida integridade dos dados

**`events_delete.php`** (Atualizado)
- Deleta eventos
- Validação de autorização:
  - ✅ **Gerente**: Pode deletar qualquer evento
  - ✅ **Líder**: Pode deletar apenas seus próprios eventos
  - ❌ **Colaborador**: Não tem permissão
- Confirmação de segurança no frontend

### Frontend (JavaScript)

**`script/tarefas.js`** (✨ Atualizado)

Novas funções:

```javascript
// Abre modal de edição com dados do evento
editarMarcacao(event)

// Salva alterações do evento
salvarEdicaoMarcacao(event)

// Deleta evento com confirmação
excluirMarcacao(event)
```

Melhorias:
- Adicionado confirmação antes de deletar
- Botões de ação (editar/deletar) nos eventos
- Tratamento de erros com mensagens

### Frontend (HTML)

**`index.php`** (✨ Atualizado)

Nova Modal:
- ID: `#editMarcacao`
- Campos: Título, Equipe, Data, Hora, Descrição
- Campos preenchidos automaticamente com dados do evento

---

## 🔐 Permissões por Tipo de Usuário

### Gerente
```
✅ Criar evento
✅ Editar qualquer evento
✅ Deletar qualquer evento
✅ Ver todos os eventos
```

### Líder
```
✅ Criar evento (em suas equipes)
✅ Editar seus próprios eventos
✅ Deletar seus próprios eventos
✅ Ver eventos de suas equipes
```

### Colaborador
```
✅ Visualizar eventos
❌ Criar evento
❌ Editar evento
❌ Deletar evento
```

---

## 🖱️ Como Usar

### Adicionar Evento
1. Clique no botão **"+"** na seção "Tarefas"
2. Preencha os campos:
   - **Título**: Nome do evento
   - **Equipe**: Selecione a equipe
   - **Data**: Data do evento
   - **Hora**: Hora de início
   - **Descrição**: Detalhes
3. Clique em **"Adicionar"**

### Editar Evento
1. Clique no dia do calendário para ver eventos
2. Clique no botão **"✏️ Editar"** (ícone de lápis) do evento
3. Modifique os campos desejados
4. Clique em **"Salvar Alterações"**

### Deletar Evento
1. Clique no dia do calendário para ver eventos
2. Clique no botão **"🗑️ Deletar"** (ícone de lixo) do evento
3. Confirme a ação
4. Evento será removido

---

## 🔧 Endpoints da API

### POST `/events_add.php`
Cria novo evento
```
Parâmetros:
- titulo (obrigatório)
- equipe (obrigatório, int)
- data (obrigatório, YYYY-MM-DD)
- hora (obrigatório, HH:MM)
- descricao (obrigatório)

Retorno: JSON { sucesso, mensagem, id }
```

### POST `/events_edit.php`
Edita evento existente
```
Parâmetros:
- id (obrigatório, int)
- titulo (obrigatório)
- equipe (obrigatório, int)
- data (obrigatório, YYYY-MM-DD)
- hora (obrigatório, HH:MM)
- descricao (obrigatório)

Retorno: JSON { sucesso, mensagem }
```

### GET `/events_delete.php?id=123`
Deleta evento
```
Parâmetros:
- id (obrigatório, int)

Retorno: JSON { sucesso, mensagem }
```

---

## 📊 Fluxo de Dados

```
Usuário interage no Frontend
         ↓
  tarefas.js captura ação
         ↓
FormData enviado para backend
         ↓
events_*.php valida autorização
         ↓
Banco de dados atualizado
         ↓
JSON response retorna ao frontend
         ↓
  updateCalendar() recarrega dados
         ↓
  mostraMarcacao() exibe eventos
```

---

## ⚠️ Mensagens de Erro

| Erro | Causa | Solução |
|------|-------|--------|
| "Não autenticado" | Usuário não fez login | Fazer login primeiro |
| "Colaboradores não podem..." | Tipo de usuário sem permissão | Use gerente ou líder |
| "Você pode editar apenas suas próprias marcações" | Líder tentando editar evento de outro | Apenas gerente pode editar de todos |
| "Todos os campos são obrigatórios" | Campo vazio | Preencha todos os campos |
| "Data inválida" | Formato de data incorreto | Use formato YYYY-MM-DD |
| "Hora inválida" | Formato de hora incorreto | Use formato HH:MM |

---

## 🎨 Estilo dos Botões

- **Editar**: Botão amarelo com ícone ✏️
- **Deletar**: Botão vermelho com ícone 🗑️
- **Grupo de botões**: Posicionado à direita no card do evento

---

## 🚀 Próximas Melhorias Sugeridas

- [ ] Edição em lote de eventos
- [ ] Duplicação de eventos (clonar)
- [ ] Exportação de eventos (PDF/CSV)
- [ ] Notificações ao deletar/editar
- [ ] Histórico de alterações
- [ ] Recorrência de eventos (semanal, mensal)
- [ ] Convites para eventos
- [ ] Lembretes por e-mail

---

**Versão**: 1.0  
**Data**: 13 de Novembro de 2025  
**Status**: ✅ Pronto para Produção

# ✅ GUIA - Líderes com Permissão de Editar e Deletar

## 🔍 Como Verificar se Funciona

### 1. Abra o Console do Navegador (F12)
Vá para a aba **"Console"** e procure por:

```
Tipo de usuário: lider
ID Líder: 3
Equipe: 
```

E quando clicar em um dia do calendário:

```
Mostrando eventos do dia: 2025-11-13
Tipo de usuário: lider | ID Líder: 3
Evento: Revisão de Sprint | Líder ID do evento: 1
✗ Sem botões (Não é criador do evento)

Evento: Entrega de Documento | Líder ID do evento: 1
✗ Sem botões (Não é criador do evento)
```

---

## 🎯 O que Líderes Podem Fazer

### Líder VÊ botões quando:
✅ Criou o evento (`fk_Lider_ID_Lider == ID_Lider_Sessão`)
✅ Em eventos de suas equipes

### Líder NÃO VÊ botões quando:
❌ Outro líder criou o evento
❌ Evento é de outra equipe que não lidera

---

## 🧪 Teste Passo a Passo

### Cenário 1: Líder Editando Seu Próprio Evento

1. **Login como Líder**
   - Email: `lider@test.com`
   - Senha: `1234`

2. **Crie um Evento**
   - Clique no botão `+` em "Tarefas"
   - Preencha os dados
   - Clique "Adicionar"

3. **Veja os Botões**
   - Clique no dia do evento
   - Deve aparecer: **✏️ Editar** | **🗑️ Deletar**

4. **Teste Edição**
   - Clique **✏️ Editar**
   - Altere o título
   - Clique **Salvar Alterações**
   - Deve atualizar

5. **Teste Deleção**
   - Clique **🗑️ Deletar**
   - Confirme
   - Evento deve desaparecer

---

### Cenário 2: Verificar Permissões

**Quando Líder NÃO pode editar:**
```
- Evento criado por outro líder
- Console mostrará: "✗ Sem botões (Não é criador do evento)"
```

**Quando Gerente PODE editar:**
```
- Qualquer evento
- Console mostrará: "✓ Mostrando botões (Gerente)"
- Aparecerá ✏️ e 🗑️ em todos os eventos
```

---

## 🔧 Verificação Técnica

### Backend Validações (PHP):

**`events_edit.php` valida:**
- ✅ Autenticação obrigatória
- ✅ Se Gerente: pode editar qualquer evento
- ✅ Se Líder: pode editar apenas seus eventos
- ✅ Se Colaborador: erro (não permitido)

**`events_delete.php` valida:**
- ✅ Autenticação obrigatória
- ✅ Se Gerente: pode deletar qualquer evento
- ✅ Se Líder: pode deletar apenas seus eventos
- ✅ Se Colaborador: erro (não permitido)

### Frontend Validações (JavaScript):

**`tarefas.js` mostra botões quando:**
- ✅ `Tipo_User != "colaborador"` (not colaborador)
- ✅ Se Gerente: sempre mostra
- ✅ Se Líder: `fk_Lider_ID_Lider == idLider` (é o criador)

---

## 📋 Checklist de Funcionamento

### Para Líderes:

- [ ] Consigo ver meus próprios eventos
- [ ] Aparecem botões **✏️ Editar** e **🗑️ Deletar** nos meus eventos
- [ ] Posso clicar em Editar e salvar mudanças
- [ ] Posso clicar em Deletar e remover o evento
- [ ] Não vejo botões em eventos de outro líder
- [ ] Console mostra "✓ Mostrando botões" para meus eventos

### Para Gerentes:

- [ ] Vejo botões em TODOS os eventos
- [ ] Posso editar qualquer evento
- [ ] Posso deletar qualquer evento
- [ ] Console mostra "✓ Mostrando botões (Gerente)" em tudo

---

## 🆘 Se não funcionar

### Verifique:

1. **Está logado como Líder?**
   ```
   F12 → Console → Veja: Tipo_User, idLider
   ```

2. **O evento foi criado por você?**
   ```
   F12 → Console → Clique no dia
   Procure: "✓ Mostrando botões" ou "✗ Sem botões"
   ```

3. **Os dados estão corretos no BD?**
   ```
   Acesse: http://localhost/calendario-uff/debug_delete.php
   Veja se fk_Lider_ID_Lider está correto
   ```

4. **Atualize o navegador**
   ```
   Ctrl+Shift+R (cache completo)
   ```

---

**Status:** ✅ Implementado  
**Líderes podem:** Editar/Deletar seus próprios eventos  
**Gerentes podem:** Editar/Deletar qualquer evento  


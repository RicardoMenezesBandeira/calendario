# Fluxo do Site Calendário UFF

```mermaid
flowchart TD
    %% LOGIN & CADASTRO
    A[login.php] -->|Preenche email/senha| B[plogin.php]
    B -->|Login válido| C[index.php]
    B -->|Login inválido| A
    A -->|Clica em Registre-se| D[cadastro.php]
    D -->|Envia formulário| E[pcadastro.php]
    E -->|Cadastrado| A
    E -->|Erro| D

    %% LOGOUT
    C -->|Clica logout| F[logout.php]
    F --> A

    %% INDEX (painel principal)
    C --> G[equipes]
    C --> H[calendário]
    C --> I[tarefas/marcações]

    %% OPERACOES POR TIPO DE USUARIO
    subgraph ALUNO
        G -->|Visualiza equipes| H
        H -->|Visualiza marcações da equipe| I
        I --> J[Não pode adicionar marcações]
    end

    subgraph LIDER
        G -->|Visualiza equipes que lidera| H
        H -->|Visualiza marcações da equipe| I
        I -->|Adicionar/editar marcações| J[events_add.php]
        I -->|Excluir marcações| K[events_delete.php]
    end

    subgraph GERENTE
        G -->|Visualiza todas as equipes| H
        H -->|Visualiza todas as marcações| I
        I -->|Adicionar/editar marcações| J
        I -->|Excluir marcações| K
    C -->|Abrir modais| L[adduser / addequipe / addrelacao]
    L -->|Cadastrar usuário| cadastropa.php
    L -->|Cadastrar equipe| equipes_add.php
    L -->|Relacionar lider-equipe| relacionar.php
    end

    %% EDITAR PERFIL
    C -->|Clica no ícone usuário| M[usuario modal]
    M -->|Envia alterações| N[peditar.php]
    N --> C

    %% MARCACAO
    J -->|Inserir no DB| events_add.php
    K -->|Excluir do DB| events_delete.php

    %% RELACIONAMENTO
    L -->|Relaciona lider-equipe| relacionar.php

    %% MARCACAO/DATA
    H -->|Requisita marcações de mês| O[events.php]
    O --> H

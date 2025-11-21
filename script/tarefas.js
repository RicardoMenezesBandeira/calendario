var jason = [];

async function excluirMarcacao(event) {
    event.stopPropagation(); // Prevenir propagação de eventos
    
    let btn = event.currentTarget;
    let cardmarc = btn.closest(".card");
    let id = cardmarc.getAttribute("data-id");
    
    console.log('Tentando deletar marcação ID:', id);
    
    if (!id) {
        alert('Erro: ID da marcação não encontrado');
        return;
    }
    
    if (confirm('Tem certeza que deseja deletar esta marcação?')) {
        try {
            const url = "./events_delete.php?id=" + id;
            console.log('URL de deleção:', url);
            
            const resp = await fetch(url);
            console.log('Status da resposta:', resp.status);
            
            const data = await resp.json();
            console.log('Dados da resposta:', data);
            
            if (data.sucesso) {
                cardmarc.remove();
                updateCalendar();
                alert('Marcação deletada com sucesso!');
            } else {
                alert('Erro ao deletar: ' + data.erro);
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            alert('Erro ao deletar: ' + error.message);
        }
    }
}

async function editarMarcacao(event) {
    event.stopPropagation(); // Prevenir propagação de eventos
    
    let btn = event.currentTarget;
    let cardmarc = btn.closest(".card");
    let id = cardmarc.getAttribute("data-id");
    
    console.log('Abrindo edição para ID:', id);
    console.log('Array jason:', jason);
    
    if (!id) {
        alert('Erro: ID da marcação não encontrado');
        return;
    }
    
    // Encontrar o evento no array - tenta várias chaves possíveis
    let evento = jason.find(e => {
        console.log('Comparando evento:', e);
        return e['ID_Marcacao'] === parseInt(id) || 
               e['Id_Marcacao'] === parseInt(id) || 
               e['id'] === parseInt(id);
    });
    
    if (!evento) {
        console.error('Evento não encontrado. Procurando por ID:', id);
        console.error('Eventos disponíveis:', jason);
        alert('Marcação não encontrada no calendário. Verifique o console para detalhes.');
        return;
    }
    
    console.log('Evento encontrado:', evento);
    
    // Determinar qual chave de ID está sendo usada
    const idKey = evento['ID_Marcacao'] ? 'ID_Marcacao' : (evento['Id_Marcacao'] ? 'Id_Marcacao' : 'id');
    
    // Preencher o formulário de edição
    document.getElementById('editMarcacaoId').value = id;
    document.getElementById('editMarcacaoTitulo').value = evento['titulo'] || '';
    document.getElementById('editMarcacaoEquipe').value = evento['fk_Equipe_Numero'] || '';
    document.getElementById('editMarcacaoData').value = evento['Data'] || '';
    document.getElementById('editMarcacaoHora').value = evento['Hora'] || '';
    document.getElementById('editMarcacaoDescricao').value = evento['Descricao'] || '';
    
    // Abrir modal
    try { 
        $('#editMarcacao').modal('show');
        console.log('Modal de edição aberta');
    } catch(e) {
        console.error('Erro ao abrir modal:', e);
        alert('Erro ao abrir formulário de edição');
    }
}

async function salvarEdicaoMarcacao(event) {
    try {
        if (event && event.preventDefault) event.preventDefault();
        
        const id = document.getElementById('editMarcacaoId').value;
        const titulo = document.getElementById('editMarcacaoTitulo').value;
        const equipe = document.getElementById('editMarcacaoEquipe').value;
        const data = document.getElementById('editMarcacaoData').value;
        const hora = document.getElementById('editMarcacaoHora').value;
        const descricao = document.getElementById('editMarcacaoDescricao').value;
        
        if (!id || !titulo || !equipe || !data || !hora || !descricao) {
            alert('Todos os campos são obrigatórios');
            return;
        }
        
        const formData = new FormData();
        formData.append('id', id);
        formData.append('titulo', titulo);
        formData.append('equipe', equipe);
        formData.append('data', data);
        formData.append('hora', hora);
        formData.append('descricao', descricao);
        
        const resp = await fetch("./events_edit.php", {
            method: 'POST',
            body: formData
        });
        
        const result = await resp.json();
        
        if (result.sucesso) {
            try { $('#editMarcacao').modal('hide'); } catch(e){}
            updateCalendar();
            alert('Marcação atualizada com sucesso!');
        } else {
            alert('Erro: ' + result.erro);
        }
    } catch (e) {
        console.error(e);
        alert('Erro ao salvar edição: ' + e.message);
    }
}

async function addMarcacao(event) {
  try {
    if (event && event.preventDefault) event.preventDefault();

    let formEl = document.getElementById('form-addmarcarcao');
    if (!formEl) throw new Error('Formulário de marcação não encontrado');

    let form = new FormData(formEl);
    let options = {
      method: "POST",
      body: form
    };

    const resp = await fetch("./events_add.php", options);
    const data = await resp.json().catch(() => null);

    if (!resp.ok) {
      console.error('Erro ao criar marcação', resp.status, data);
      alert((data && data.erro) ? data.erro : 'Falha ao criar marcação');
      return;
    }

    if (data && data.sucesso) {
      // Fechar modal (se jQuery/bootstrap estiver disponível)
      try { $('#addmarcacao').modal('hide'); } catch(e){}
      updateCalendar();
      return;
    } else {
      console.error('Resposta inesperada ao criar marcação', data);
      alert((data && data.erro) ? data.erro : 'Resposta inesperada do servidor');
    }

  } catch (e) {
    console.error(e);
    alert('Erro ao tentar criar marcação: ' + e.message);
  }
};


function mostraMarcacao(event) {

    let diaCalendario = event.target;
    let diaAnterior = document.querySelector(".ativo");
    if (diaAnterior){
      diaAnterior.classList.remove("ativo");
    }
  
    diaCalendario.classList.add("ativo");
    let dia = diaCalendario.getAttribute("data-dia");
    let mural = document.querySelector(".marcacaoTarefa");
    mural.innerHTML = "";
    let btnControls;

    console.log("Mostrando eventos do dia:", dia);
    console.log("Tipo de usuário:", Tipo_User, "| ID Líder:", idLider);
  
    for(let j = 0; j < jason.length;j++){
      
  
      if (jason[j]["Data"] === dia){
  
        console.log("Evento:", jason[j]["titulo"], "| Líder ID do evento:", jason[j]["fk_Lider_ID_Lider"]);
        
        if(Tipo_User != "colaborador"){
          if(Tipo_User == "lider"){
            if(jason[j]["fk_Lider_ID_Lider"] == idLider){
              console.log("✓ Mostrando botões (Líder que criou)");
              btnControls = '<div class="event-actions" style="display: flex; gap: 5px; margin-left: auto;">' +
                '<button class="btn btn-sm btn-warning" onclick="editarMarcacao(event)" title="Editar este evento"><i class="fas fa-edit"></i> Editar</button>' +
                '<button class="btn btn-sm btn-danger" onclick="excluirMarcacao(event)" title="Deletar este evento"><i class="fas fa-trash"></i> Deletar</button>' +
                '</div>';
            } else {
              console.log("✗ Sem botões (Não é criador do evento)");
              btnControls = '';
            }
          } else {
            console.log("✓ Mostrando botões (Gerente)");
            btnControls = '<div class="event-actions" style="display: flex; gap: 5px; margin-left: auto;">' +
              '<button class="btn btn-sm btn-warning" onclick="editarMarcacao(event)" title="Editar este evento"><i class="fas fa-edit"></i> Editar</button>' +
              '<button class="btn btn-sm btn-danger" onclick="excluirMarcacao(event)" title="Deletar este evento"><i class="fas fa-trash"></i> Deletar</button>' +
              '</div>';
          }
        } else {
          console.log("✗ Sem botões (Colaborador)");
          btnControls = '';
        }
  
        mural.innerHTML += `<div class="card text-center roundedrounded mb-2 event-card" data-id="${jason[j]['ID_Marcacao']}" style="border-left: 4px solid #28a745;">
                                <div class="card-header" id="header${jason[j]['ID_Marcacao']}" data-toggle="collapse" data-target="#collapse${jason[j]['ID_Marcacao']}" aria-expanded="false" aria-controls="collapse${jason[j]['ID_Marcacao']}" style="cursor: pointer; background-color: #f8f9fa;">
                                  <h5 class="my-auto p-2" style="display: flex; justify-content: space-between; align-items: center; margin: 0;">
                                    <span style="font-weight: 600;">${jason[j]["titulo"]}</span>
                                    <span>${btnControls}</span>
                                  </h5>
                                </div>
                                <div id="collapse${jason[j]['ID_Marcacao']}" class="card-body text-left collapse" aria-labelledby="header${jason[j]['ID_Marcacao']}" data-parent="#accordion">
                                  <p class="card-text"><strong>Equipe:</strong> ${jason[j]["fk_Equipe_Numero"]}</p>
                                  <p class="card-text"><strong>Descrição:</strong> ${jason[j]["Descricao"]}</p>
                                </div>
                                <div class="card-footer text-muted" style="background-color: #f8f9fa;">
                                  <small><i class="fas fa-calendar"></i> ${jason[j]["Data"]} - <i class="fas fa-clock"></i> ${jason[j]["Hora"]}</small>
                                </div>
                            </div>`;
      }
    }
  } 

  async function relacionar(event) {

    let form = new FormData(document.getElementById('form-relacionar'))
    let options= {
      method:"POST",
      body:form
    }
  
    await fetch("./relations_add.php",options);
  };
async function salvarEquipe(idColaborador) {
    let select = document.querySelector(`select[data-id='${idColaborador}']`);
    let equipe = select.value;

    let form = new FormData();
    form.append("colaborador_id", idColaborador);
    form.append("equipe", equipe);

    try {
        let response = await fetch("relations_colaborador.php", {
            method: "POST",
            body: form
        });

        // Lê a resposta como texto primeiro
        let texto = await response.text();

        let res;
        try {
    res = JSON.parse(texto);
} catch (e) {
    console.error("❌ RESPOSTA NÃO É JSON VÁLIDO!");
    console.log("🔍 Conteúdo retornado pelo PHP:", texto);
    alert("Erro: o servidor retornou uma resposta inválida.\nVerifique o console.");
    return;
}


        if (!response.ok || !res.sucesso) {
            alert("Erro: " + (res.erro ?? "Falha desconhecida"));
            return;
        }

        alert("Equipe atualizada!");

    } catch (error) {
        console.error(error);
        alert("Erro ao enviar dados.");
    }
}
document.getElementById("buscarColaborador").addEventListener("keyup", function() {
    let termo = this.value.toLowerCase();
    let itens = document.querySelectorAll(".item-colaborador");

    itens.forEach(item => {
        let nome = item.innerText.toLowerCase();
        item.style.display = nome.includes(termo) ? "" : "none";
    });
});
async function deletarColaborador(idColaborador) {

    if (!confirm("Tem certeza que deseja remover este colaborador?")) {
        return;
    }

    let form = new FormData();
    form.append("colaborador_id", idColaborador);

    let response = await fetch("delete_colaborador.php", {
        method: "POST",
        body: form
    });

    let texto = await response.text();

    let res;
    try {
        res = JSON.parse(texto);
    } catch (e) {
        console.error("Resposta não é JSON:", texto);
        alert("Erro inesperado no servidor.");
        return;
    }

    if (res.sucesso) {
        alert("Colaborador removido!");
        document.getElementById(`colab-${idColaborador}`).remove(); // remove da lista na página
    } else {
        alert("Erro: " + res.erro);
    }
}
function configurarSelectEquipe() {
    const radios = document.querySelectorAll('input[name="tipouser"]');
    const selectEquipe = document.getElementById('selectEquipe');
    const selectInput = selectEquipe.querySelector("select");

    function atualizarVisibilidade() {
        const selecionado = document.querySelector('input[name="tipouser"]:checked').value;

        if (selecionado === "colaborador") {
            selectEquipe.style.display = "block";
            selectInput.setAttribute("required", "required");
        } else {
            selectEquipe.style.display = "none";
            selectInput.removeAttribute("required");
            selectInput.value = "";
        }
    }

    radios.forEach(r => r.addEventListener('change', atualizarVisibilidade));
    atualizarVisibilidade(); // garante estado inicial correto
}

// Inicializa quando a página carregar
document.addEventListener("DOMContentLoaded", configurarSelectEquipe);
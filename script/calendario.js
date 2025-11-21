const date = new Date();

const renderCalendar = () => {


  date.setDate(1);

  const monthDays = document.querySelector(".dias");

  const lastDay = new Date(
    date.getFullYear(),
    date.getMonth() + 1,
    0
  ).getDate();

  const prevLastDay = new Date(
    date.getFullYear(),
    date.getMonth(),
    0
  ).getDate();

  const firstDayIndex = date.getDay();

  const lastDayIndex = new Date(
    date.getFullYear(),
    date.getMonth() + 1,
    0
  ).getDay();

  const nextDays = 7 - lastDayIndex - 1;

  const meses = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro",
  ];

  document.querySelector(".data h1").innerHTML = meses[date.getMonth()] + " " + String(date.getFullYear());

  let days = "";
  let mes = ("0" + (date.getMonth() + 1)).slice(-2)

  for (let x = firstDayIndex; x > 0; x--) {
    days += `<div class="prev-date child d-flex align-items-center"><p>${prevLastDay - x + 1}</p></div>`;
  }

  for (let i = 1; i <= lastDay; i++) {

    let dia = ("0" + i).slice(-2);
    let diaAtual = date.getFullYear() +"-" + mes + "-"+ dia;
    let notificacao ="";

    for(let j = 0; j < jason.length;j++){
      if (jason[j]["Data"] === diaAtual){
        notificacao = '<span class="badge badge-pill badge-success p-2"> </span>';
      }
    }

    if ( i === new Date().getDate() && date.getMonth() === new Date().getMonth() && date.getFullYear() === new Date().getFullYear() ) {
      days += `<div class="today child d-flex align-items-center" data-dia="${diaAtual}" onclick="mostraMarcacao(event)"><p>${i}</p>${notificacao}</div>`;
    } else {
      days += `<div class="child d-flex align-items-center" data-dia="${diaAtual}" onclick="mostraMarcacao(event)"><p>${i}</p>${notificacao}</div>`;
    }
  }

  for (let j = 1; j <= nextDays; j++) {
    days += `<div class="next-date d-flex align-items-center child"><p>${j}</p></div>`;
  }
  monthDays.innerHTML = days;
};

async function updateCalendar() {
  try {
    const mes = ("0" + (date.getMonth() + 1)).slice(-2);
    const ano = date.getFullYear();
    let equipe;

  if (Tipo_User == "colaborador") {
      equipe = equipe;
    } else {
      const equipeAtivaEl = document.querySelector('.equipeAtiva');
      if (equipeAtivaEl) {
        equipe = equipeAtivaEl.getAttribute("data-equipe");
      } else {
        // Sem equipe ativa, envia 'all' para pegar todas as marcações
        equipe = "all";
      }
    }

    const response = await fetch(`./events.php?mon=${mes}&ano=${ano}&equipe=${equipe}`);

    if (response.status != 200) throw new Error("Falha ao carregar marcações");
    jason = await response.json();
    renderCalendar();
  } catch (e) {
    console.error(e);
  }
}


function trocaequipe(event) {
  let equipeAtiva = event.currentTarget;
      let equipeAnterior = document.querySelector(".equipeAtiva");
      if (equipeAnterior){
        equipeAnterior.classList.remove("equipeAtiva");
        console.log(equipeAnterior);
      }
      equipeAtiva.classList.add("equipeAtiva");
  document.querySelector('#tarefaequipe').value = document.querySelector('.equipeAtiva').getAttribute("data-equipe");
      updateCalendar();
  };
  

document.querySelector(".anter").addEventListener("click", () => {
  date.setMonth(date.getMonth() - 1);
  updateCalendar();
});

document.querySelector(".prox").addEventListener("click", () => {
  date.setMonth(date.getMonth() + 1);
  updateCalendar();
});

// Preencher o campo de equipe por padrão
if (Tipo_User === 'colaborador') {
  try {
    if (typeof equipe !== 'undefined' && equipe !== null && equipe !== 'Invalido') {
      const tarefaequipeEl = document.querySelector('#tarefaequipe');
      if (tarefaequipeEl) tarefaequipeEl.value = equipe;
    }
  } catch (e) {
    console.error(e);
  }

} else {
  try {
    let equipes = document.getElementsByClassName("equipeoption")[0];
    if (equipes) { // só aplica se existir
      equipes.classList.add("equipeAtiva");
      let equipeAtiva = document.querySelector('.equipeAtiva');
      if (equipeAtiva) {
        document.querySelector('#tarefaequipe').value = equipeAtiva.getAttribute("data-equipe");
      }
    }
  } catch (e) {
    console.error(e);
  }
}


updateCalendar()
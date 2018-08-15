console.log("I'll have all the scripts!");

function staging_redirect() {
  var page = document.getElementById("staging");

  if (page) {
    window.location.href = "./index.php";
  }
}

function load_page(url, method, data, target) {

  //Uso padrão é exibição de páginas abaixo do header
  if (!target) {
    var target = document.getElementById("main-content");
  }
  var xhr = new XMLHttpRequest();


  if (method == "GET" && data) {
    var params = "?";
    data.forEach((value, key) => {
      if (key != "ignore")
        params += key + "=" + value + "&";
    });
    url = url + params;
  }
  xhr.open(method, url, true);

  if (data)
    xhr.send(handle_form(data));
  else
    xhr.send();

  xhr.onload = function () {
    target.innerHTML = xhr.responseText;
    staging_redirect();
  }
}

function handle_form(source) {

  if (!(source instanceof FormData)) {
    var form = source.closest("form");
    var content = new FormData();

    //form.length-1 para evitar o botão
    for (var i = 0; i < form.length - 1; i++) {
      content.append(form[i].name, (form[i].value || 'default'));
    }
    return content;
  }
  else if (source instanceof FormData)
    return source;
  else {
    console.log("error");
  }
}

function validate(key, value) {
  var div = document.getElementById("error_text");

  var xhr = new XMLHttpRequest();

  xhr.open('POST', 'Controller/controller.php', );

  var data = new FormData();
  data.append('actor_object', 'user');
  data.append('task', 'collide');
  data.append(key, value);

  xhr.send(data);
  xhr.onload = function () {
    var exists = JSON.parse(xhr.responseText).exists;

    if (exists) {
      div.innerHTML = key + " já esta em uso!";
      div.closest('form').reset();
    }
  }
}

function set_active_patient_and_display(element, actor, type) {
  var tgt = document.getElementById("show_area");
  var id = element.querySelector('#ptt_id').value;

  let data = new FormData();
  data.append('actor_object', 'patient');
  data.append('task', 'set_active');
  data.append('id', id);

  load_page("Controller/controller.php", 'POST', data, tgt);
}

function make_new_form(type) {

  type = (type == 'exames') ? 'ex' : 'med';

  let counter = 1;

  while (document.getElementById(type + counter)) {
    counter++;
  }

  let id = type + counter;
  let content = '';

  switch (type) {

    case 'ex':

      content =
        `<hr>
    <div id="${id}" class="row">
      <div class="col-sm-12 col-lg-4">
        <label for="nome_${id}">Nome do exame</label>
        <input type="text" class="form-control" name="nome_${id}" />
      </div>
      <div class="col-sm-12 col-lg-4">
        <label for="valor_${id}">Valor do resultado</label>
        <input type="text" class="form-control" name="valor_${id}" />
      </div>
      <div class="col-sm-12 col-lg-4">
        <label for="faixa_${id}">Dentro da faixa desejada?</label>
        <div>
          <span class="col-3">
            <input type="radio" name="faixa_${id}" value="sim" />
            <label>&nbsp Sim</label>
          </span>
          <span class="col-3">
            <input type="radio" name="faixa_${id}" value="nao" />
            <label>&nbsp Não</label>
          </span>
        </div>
      </div>
    </div>`;

      break;
    case 'med':

      content =
        `<hr>
        <div id=${id} class="row">
    <div class="col-sm-12 col-lg-4">
      <label for="nome_${id}">Medicamento</label>
      <input type="text" class="form-control" name="nome_${id}"/>
    </div>

    <div class="col-sm-12 col-lg-2">
      <label for="dose_${id}">Dose</label>
      <input type="text" class="form-control" name="dose_${id}"/>
    </div>
    
    <div class="col-sm-12 col-lg-2">
        <label for="freq_${id}">Via</label>
        <div>
          <select name=via_${id} class="custom-select">
              <option value="oral" selected>Oral</option>
              <option value="SL">Sublingual</option>
              <option value="sonda">Sonda</option>
              <option value="IV">Intravenosa</option>
              <option value="IM">Intramuscular</option>
              <option value="SC">Subcutânea</option>
              <option value="tópica">Tópica</option>
              <option value="IT">Intratecal</option>
              <option value="retal">Retal</option>
          </select>
        </div>
      </div>
    
    <div class="col-sm-12 col-lg-4">
      <label for="freq_${id}">Frequência</label>
      <div>
      <select name=freq_${id} class="custom-select">
      <optgroup label="1x/dia">
          <option value="1x/dia - manhã">Manhã</option>
          <option value="1x/dia - tarde">Tarde</option>
          <option value="1x/dia - noite">Noite</option>
      </optgroup>
      <optgroup label="2x/dia">
          <option value="2x/dia - manhã e noite">Manhã e noite</option>
          <option value="2x/dia - manhã e almoço">Manhã e almoço</option>
          <option value="2x/dia - manhã e tarde">Manhã e tarde</option>
          <option value="2x/dia - tarde e noite">Tarde e noite</option>
      </optgroup>
      <optgroup label="3x/dia">
      <option value="Nas refeições">Nas refeições</option>
      <option value="3x/dia - manhã, tarde e noite">Manhã, tarde e noite</option>
      </optgroup>
      <optgroup label="Outros horários">
          <option value="6/6h">4x/dia</option>
          <option value="4/4h">6x/dia</option>
          <option value="SOS">SOS</option>
          <option value="Dias alternados">Dias alternados</option>
          <option value="Semanal">Semanalmente</option>
      </optgroup>
  </select>
      </div>
    </div>
  </div>
  `;
      break;
  }

  let el = document.createElement('span');
  el.innerHTML = content;

  document.getElementById(type + '1').parentElement.append(el);
  return false;
}

function to_JSON_send(formbutton, type) {
  let el = document.getElementById('form');

  let entry = {};

  let formData = new FormData();
  formData.append('task', el.task.value);
  formData.append('patient', el.patient.value);
  formData.append('author', el.author.value);
  formData.append('subject', el.subject.value);

  let regex = "(^[a-z]+)_(.*)"

  //Os quatro primeiros elementos são metadados
  if (type == 'exames') {
    for (var i = 4; i < el.length; i++) {
      //Exclui as entradas sem nome ou vazias
      if (el[i].name == '' || el[i].value == '')
        continue;

      //Exclui o faixa ex se tiver vazio
      if (el[i].type == "radio" && (el[i - 1].value == '' || el[i - 2].value == '')) {
        continue;
      }
      let groups = el[i].name.match(regex);

      let prop = groups[2];

      var subitem = subitem ? subitem : {};
      if (!(el[i].type == "radio") || el[i].checked) {
        subitem[groups[1]] = el[i].value;
      }
      if (subitem.nome && subitem.valor && subitem.faixa) {
        entry[prop] = subitem;
        subitem = {};
      }

    }
  }
  else {
    for (var i = 4; i < el.length; i++) {
      if (el[i].name == '' || el[i].value == '')
        continue;

      let groups = el[i].name.match(regex);

      let prop = groups[2];

      var subitem = subitem ? subitem : {};
      subitem[groups[1]] = el[i].value;
      

      if (subitem.nome && subitem.dose && subitem.via && subitem.freq) {
        entry[prop] = subitem;
        subitem = {};
        console.log(entry);
      }
    }
  }


  formData.append('content', JSON.stringify(entry));
  formData.append('actor_object', 'patient_data');
  load_page("Controller/controller.php", 'POST', formData, document.getElementById("show_area"));
}

function patient_action_select(button, method) {
  var tgt = document.getElementById("show_area");
  var option = button.value;



  var option_list = {
    'Adicionar': 'add',
    'Acompanhar': 'list_pre',
    'Mudar Status': 'change_status',
    'Editar': 'edit',
    'Remover': 'delete',
  }

  var data = handle_form(button);
  data.append('actor_object', 'patient');
  data.append('task', option_list[option]);
  load_page("Controller/controller.php", method, data, tgt);

}

function patient_data_handler(button, method) {
  var tgt = document.getElementById("show_area");

  data = handle_form(button);

  data.append('actor_object', 'patient_data');

  switch (button.id) {
    case 'new_pre':
      data.append('task', 'add_pre');
      break;
    case 'new_res':
      data.append('task', 'add_res');
      break;
    case 'new_com':
      data.append('task', 'add_com');
      break;
    default:
      console.log(button.id);
      break;
  }


  load_page("Controller/controller.php", method, data, tgt);
}



/* ---------------- FX ------------------*/

function warn() {
  var field = document.getElementById("delete");
  field.style.color = "red";
}

function enable_button(element, user) {
  var btn = document.getElementById("delbtn");

  setTimeout(function () {
    if (element.value == user)
      btn.disabled = false;
    else
      btn.disable = true;
  }, 500)

}

function collapse_toggle(btn, target_id) {


  let target = document.querySelector("#" + target_id);

  if (!target.classList.contains('show') || target.style.display == 'none') {
    target.style.display = 'block';
    target.classList.remove('show');
  }
  btn.style.display = 'none';
}
/*------------------- Rotas -----------------------*/

var root = null;
var useHash = true; // Defaults to: false
var hash = '#!'; // Defaults to: '#'
var router = new Navigo(root, useHash, hash);


//Rotas em si
router
  .on({
    '/home': function () {
      window.location.href = "./";
    },
    'profile': function () {
      load_page('Controller/controller.php?task=editar&actor_object=user', 'GET');
    },
    'comments': function () {
      load_page('Controller/controller.php?task=list_com&actor_object=patient_data', 'GET');
    },
    'results': function () {
      load_page('Controller/controller.php?task=list_res&actor_object=patient_data', 'GET');
    },
    'prescriptions': function () {
      load_page('Controller/controller.php?task=list_pre&actor_object=patient_data', 'GET');
    }
  })
  .resolve();
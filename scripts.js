console.log("I'll have all the scripts!");

function staging_redirect() {
  var page = document.getElementById("staging");

  if (page) {
    window.location.href = "/PACO_2018/index.php";
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
  data.append('actor_object','user');
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

function show_data(element, actor,type) {
  var tgt = document.getElementById("show_area");
  var id = element.querySelector('#ptt_id').value;

  data = new FormData();
  data.append('actor_object', 'patient');
  data.append('task', 'get_data');
  data.append('id', id);

  load_page("Controller/controller.php", 'POST', data, tgt);
}

function show_comments(){
  var tgt = document.getElementById("show_area");
  var id = element.querySelector('#ptt_id').value;

  data = new FormData();
  data.append('actor_object', 'patient');
  data.append('task', 'get_data');
  data.append('id', id);

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

  switch(button.id){
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

function collapse_toggle(btn,target_id) {


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
      window.location.href="/PACO_2018";
    },
    'profile': function () {
      load_page('Controller/controller.php?task=editar&actor_object=user', 'GET');
    },
    'comments': function () {
      load_page('Controller/controller.php?task=list_com&actor_object=patient_data', 'GET');
    },
    'prescriptions': function () {
      load_page('Controller/controller.php?', 'GET');
    },
    'results': function () {
      load_page('Controller/controller.php?', 'GET');
    }    
  })
  .resolve();
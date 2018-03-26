console.log("I'll have all the scripts!");

function staging_redirect(){
  var page =  document.getElementById("staging");

  if(page){
    window.location.href="/PACO_2018/index.php";
    }
}

function load_page(url, method, data, target){

    //Uso padrão é exibição de páginas abaixo do header
    if(!target){
      var target = document.getElementById("main-content");
    }
    var xhr = new XMLHttpRequest();


    if(method =="GET" && data){
      var params = "?";
      data.forEach((value,key)=>{
        if(key!="ignore")
          params+= key + "=" + value + "&";
      });
      url = url + params;
    }
    xhr.open(method, url, true);

    if(data)
      xhr.send(handle_form(data));
    else
      xhr.send(data);

    xhr.onload = function(){
      target.innerHTML = xhr.responseText;
      staging_redirect();
    }
}

function handle_form(source){

      if(!(source instanceof FormData)){
        var form = source.closest("form");
        var content = new FormData();

        //form.length-1 para evitar o botão
        for(var i = 0; i<form.length-1;i++){
          content.append(form[i].name,(form[i].value||'default'));
        }
        return content;
      }
      else if(source instanceof FormData)
        return source;
      else {
        console.log("error");
      }
}

function validate(key, value){
  var div = document.getElementById("error_text");

  var xhr = new XMLHttpRequest();

  xhr.open('POST','Controller/users.php',);

  var data = new FormData();
  data.append('task','collide');
  data.append(key,value);

  xhr.send(data);
  xhr.onload = function(){
    var exists = JSON.parse(xhr.responseText).exists;

    if(exists){
      div.innerHTML = key + " já esta em uso!";
      div.closest('form').reset();
    }
  }
}

function show_data(element){
  var tgt = document.getElementById("show_area");
  var id = element.querySelector('#ptt_id').value;

  data = new FormData();
  data.append('task','get_data');
  data.append('id', id);

  load_page("Controller/patients.php", 'POST', data, tgt);
}

function patient_action_select(button, method){
  var tgt = document.getElementById("show_area");
  var option = button.value;

  var option_list = {'Adicionar': 'add',
                 'Acompanhar': 'list_pre',
                 'Mudar Status': 'change_status',
                 'Editar': 'edit',
                 'Remover': 'delete',
  }

  var data = handle_form(button);
  data.append('task', option_list[option]);
  load_page("Controller/patients.php", method, data, tgt);

}

function patient_data_handler(button, method){
  var tgt = document.getElementById("show_area");

  data = handle_form(button);

  if(!data.get('task'))
    data.append('task','add_com');

  load_page("Controller/patientdata.php", method, data, tgt);
}



/* ---------------- FX ------------------*/

function warn(){
  var field = document.getElementById("delete");
  field.style.color="red";
}

function enable_button(element, user){
  var btn = document.getElementById("delbtn");

  setTimeout(function(){
    if(element.value == user)
      btn.disabled = false;
  },500)
}

function give_emphasis(element){
  element.style.border = "1px solid #28a745";
  element.style.backgroundColor = "#fefefe"
  setTimeout(function(){
      element.style.backgroundColor = "#fff"
      element.style.border = "1px solid rgba(0,0,0,.125)";
  },2000);


}

function collapse_toggle(target_id){

  let target = document.querySelector("#" + target_id);

  if(target.classList.contains('show')){
    target.classList.remove('show');
  }
  else{
    target.classList.add('show');
  }
}

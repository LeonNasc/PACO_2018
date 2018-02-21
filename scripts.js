console.log("I'll have all the scripts");

function load_page(url, method, data, target){

    //Uso padrão é exibição de páginas abaixo do header
    if(!target){
      var target = document.getElementById("main-content");
    }
    var xhr = new XMLHttpRequest();

    xhr.open(method, url, true);

    if(data)
      xhr.send(handle_form(data));
    else
      xhr.send(data);

    xhr.onload = function(){
      target.innerHTML = xhr.responseText;
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
    div.innerHTML = xhr.responseText;
    if(xhr.responseText != '')
      div.parentNode.reset();
  }

}

function show_data(element){
  var tgt = document.getElementById("show_area");
  var id = element.firstChild.nextSibling.firstChild.nextSibling.value;

  data = new FormData();
  data.append('task','get_data');
  data.append('id', id);

  var html = load_page("Controller/patients.php", 'POST', data, tgt);
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

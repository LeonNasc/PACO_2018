console.log("I'll have all the scripts");

//Popover do bootstrap
$(function () {
  $('[data-toggle="popover"]').popover()
})



function load_page(url, method, data){

    var div = document.getElementById("main-content");
    var xhr = new XMLHttpRequest();

    xhr.open(method, url, true);

    console.log(data)
    if(data)
      xhr.send(handle_form(data));
    else
      xhr.send(data);

    xhr.onload = function(){
      display_html(div, xhr.responseText);
    }
}

function display_html(target, data){
    target.innerHTML = data;
}

function handle_form(submit_button){
      form = submit_button.parentElement;
      console.log(form);

      if(form && typeof submit_button == 'object'){
        var content = new FormData();
        console.log(form.length)
        //form.length-1 para evitar o botão
        for(var i = 0; i<form.length-1;i++){
          content.append(form[i].name,(form[i].value||'default'));
        }
        return content;
      }
      else if(typeof submit_button == 'object')
        return submit_button;
      else {
        console.log("error");
      }
}

function validate(key, value){
  var div = document.getElementById("error_text");
  console.log(div);
  var xhr = new XMLHttpRequest();

  xhr.open('POST','Controller/users.php',);

  data = new FormData();
  data.append('task','collide');
  data.append(key,value);

  xhr.send(data);
  xhr.onload = function(){
    div.innerHTML = xhr.responseText;
    if(xhr.responseText != '')
      div.parentNode.reset();
  }

}

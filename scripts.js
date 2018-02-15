console.log("I'll have all the scripts");

//Popover do bootstrap
$(function () {
  $('[data-toggle="popover"]').popover()
})

function load_page(url, method, data){

    var div = document.getElementById("main-content");
    var xhr = new XMLHttpRequest();

    xhr.open(method, url, true);

    if(data)
      xhr.send(handle_form(data));
    else
      xhr.send(null);

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

      if(form){
        var content = new FormData();
        console.log(form.length)
        //form.length-1 para evitar o botão
        for(var i = 0; i<form.length-1;i++){
          content.append(form[i].name,(form[i].value||'default'));
        }
        return content;
      }
      else {
        console.log("error");
      }
}

console.log("I'll have all the scripts");

function load_page(url, method){

    var div = document.getElementById("main-content");

    console.log(div);
    var xhr = new XMLHttpRequest();
    xhr.open(method, url);
    xhr.send(null);

    xhr.onload = function(){
      console.log(xhr.response);
        console.log(xhr.responseText);
        display_html(div, xhr.responseText);
    }
}

function display_html(target, data){
    target.innerHTML = data;
}

console.log("I'll have all the scripts");

function load_page(url, method,data){

    var div = document.getElementById("main-content");

    console.log(div);
    var xhr = new XMLHttpRequest();
    xhr.open(method, url);

    if(data){
      var content = '';
      data = JSON.parse(data);

      for(key in data){
        if(i<length-1)
          content+= key + "=" + data[key] + "&";
        else
          content+= key+ "=" + data[key];
      }
    }

    xhr.send(data);
    
    xhr.onload = function(){
      display_html(div, xhr.responseText);
    }
}

function display_html(target, data){
    target.innerHTML = data;
}

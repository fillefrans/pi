
  

    π.util.xhr = {


    getmodule : function (module, element) {

      var 
        uri = π.SRV_ROOT + 'pi.util.file.highlight.php?file=',
        req = new XMLHttpRequest(),
        el  = element || document.body;
      
      req.onload = function(e) { 

        var 
          html = "",
          fragment  = document.createDocumentFragment(),
          container = document.createElement("div");

        container.innerHTML = html;
        fragment.appendChild(container);
        el.appendChild(fragment);
        }
      };

      req.onerror = function(error) { 
        console.log(error);
      };

      req.open("get", uri + module, true);
      req.send();
      
    }


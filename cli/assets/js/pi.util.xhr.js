    var
      π = π || {};


    π.util = π.util || {};


    π.util.xhr = {


      getModule : function (module, element) {

        var 
          uri = π._const.SRV_ROOT + 'pi.util.file.highlight.php?file=',
          xhr = new XMLHttpRequest(),
          el  = element || document.body;
        
        xhr.onload = function(e) { 
          var 
            html      = "",
            fragment  = document.createDocumentFragment(),
            container = document.createElement("div");

          container.innerHTML = html;
          fragment.appendChild(container);

          // add to DOM element
          el.appendChild(fragment);
          }

        xhr.onerror = function(error) { 
          console.log(error);
        };

        xhr.open("get", uri + module, true);
        xhr.send();
      },


      getJson : function ( url, obj, callback, onerror ) {
        var
          xhr = new XMLHttpRequest();

        xhr.__pi = {
          callback  : callback,
          onerror   : onerror
        };
        
        xhr.onload = function(e) { 
          var 
            json = e.data || '{ error : "xhr: no data." }';

          if( typeof this.__pi.callback === "function" ) {
            this.__pi.callback.call(this, JSON.parse(json));
          }
        };

        xhr.onerror = function(error) { 
          if( typeof this.__pi.onerror === "function" ) {
            this.__pi.onerror.call(this);
          }
          console.log(error);
        };

        xhr.open("post", address, true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        return xhr.send(JSON.stringify(obj));
      }

      
    };


    var
      π = π || {};


    π.xhr = π.xhr || {

      _loaded : false,

      json : function ( url, obj, callback, onerror ) {
        var
          xhr = new XMLHttpRequest(),

          obj       = obj       || null,
          callback  = callback  || null,
          onerror   = onerror   || π.log;

        xhr.__pi = {
          callback  : callback,
          onerror   : onerror
        };

        pi.log("requesting json with xhr : " + url);
        
        xhr.onload = function() { 
          var
            json = this.responseText || '{ error : "no data." }';

          try {

            json = JSON.parse(json);

          }
          catch(e) {
            json = { 
              error : "Pi Error : exception when parsing JSON string",
              jsonSource : this.responseText
            };
          }

          if( typeof this.__pi.callback === "function" ) {
            this.__pi.callback.call(this, json);
          }
        };

        xhr.onerror = function(error) { 
          if( typeof this.__pi.onerror === "function" ) {
            this.__pi.onerror.call(this);
          }
          else {
            console.log(error);
          }
        };

        xhr.open("post", url, true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        return xhr.send(JSON.stringify(obj));
      },


      post : function ( url, obj, callback, onerror ) {
        var
          xhr = new XMLHttpRequest(),

          obj       = obj       || null,
          callback  = callback  || null,
          onerror   = onerror   || π.log;

        xhr.__pi = {
          callback  : callback,
          onerror   : onerror
        };

        pi.log("posting json with xhr : " + url, obj);
        
        xhr.onload = function() { 
          if( typeof this.__pi.callback === "function" ) {
            this.__pi.callback.call(this, this.responseText || '{ error : "no data." }');
          }
        };

        xhr.onerror = function(error) { 
          if( typeof this.__pi.onerror === "function" ) {
            this.__pi.onerror.call(this);
          }
          else {
            console.log(error);
          }
        };

        xhr.open("post", url, true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        return xhr.send(JSON.stringify(obj));
      },



      get : function ( url, callback, onerror ) {
        var
          xhr = new XMLHttpRequest(),

          callback  = callback  || null,
          onerror   = onerror   || π.log;

        xhr.__pi = {
          callback  : callback,
          onerror   : onerror
        };

        pi.log("requesting url with xhr : " + url);
        
        xhr.onload = function() { 
          var
            response = this.responseText || '{ error : "no data." }';

          if( typeof this.__pi.callback === "function" ) {
            this.__pi.callback.call(this, response);
          }
        };

        xhr.onerror = function(error) { 
          if( typeof this.__pi.onerror === "function" ) {
            this.__pi.onerror.call(this);
          }
          else {
            console.log(error);
          }
        };

        xhr.open("get", url, true);
        return xhr.send();
      }


      
    };

    π.xhr._loaded = true;

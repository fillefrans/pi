  var 
      π  = π  || {};





    π.easyad = π.easyad || {

      _loaded : false,



    // function toRegex (keyArray) {
    //   var
    //     result = [];

    //   if(!pi.isArray(keyArray)) {
    //     return null;
    //   }

    //   i = keyArray.length-1;

    //   while(i >= 0) {
    //     // regex to match {key*}
    //     result[i] = new RegExp("/\{(" . keyArray[i] . ")([^}]*)\}/");
    //     i--;
    //   }
    //   return keyArray;
    // }


    template : {

      render : function (template, data) {
        var
          template = template || null,
          defaults = {
            adwidth   : 640, 
            adheight  : 180
          },
          data = data || {};

        // check inputs
        if ( data == {} || typeof data != "object" ){
          pi.log("NOT OBJECT, OR EMPTY OBJECT");
          return template + "<div>data==[] or !isArray(data)</div>";
        } 
        if (template === null) {
          pi.log("NO TEMPLATE");
          return JSON.stringify({error : "no template"});
        }

        // add defaults to data
        for (var i in defaults) {
          if(typeof data[i] === "undefined") {
            pi.log("setting " + i + " to " + defaults[i]);
            data[i] = defaults[i];
          }
        }

        // render
        for (var key in data) {
          // pi.log("replacing : " + key + " => " + data[key]);

          // /{(.*?)}/
          template = template.replace(new RegExp("\{(" + key + "[^}]?)\}"), data[key]);
          template = template.replace(new RegExp("\{(" + key + "[^}]+)\}"), data[key]);
        }

        return template;

      }
    }
  }; // pi.easyad


  pi.easyad._loaded = true;

  pi.events.trigger('easyad', +new Date().getTime());


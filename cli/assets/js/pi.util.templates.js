    var
      π = π || {};


    π.util = π.util || {};


    π.util.templates = {

      __items : [],

      add : function(template) {
        // returns array index of the added item
        return __items.push(template)-1;
      },
      
      // renders a handlebar-style template
      render : function(tp, data) {
        var
          template = null;

        if(typeof tp == "number") {
          template = π.util.templates.__items[tp] || false;
          if(template === false) {
            return false;
          }
        }
        else if (typeof tp == "string") {
          template = tp;
        }
        else {
          return false;
        }

        return template.replace(/{{(.+?)}}/g, function (m, p1) {
            return data[p1];
        })
      }
      
    };


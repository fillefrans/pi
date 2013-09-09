  /**
   *
   * π.pcl
   *
   * @author Johan Telstad, jt@enfield.no, 2011- 2011-2013
   *
   */

  π.pcl = π.pcl || {};


  /**
   *  Set up our app to handle pcl components
   * 
   */

  π.app.components = {

    items : [],

    __init : function() {
    },

    add : function (elem) {
      var 
        component = {},
        self = π.app.components;

      component.element = elem;
      component.index   = self.items.length;

      return self.items.push(component);
    },

    forEach : function (callback) {
      π.app.components.items.forEach(callback);
    },

    // Array Remove - By John Resig (MIT Licensed)
    remove : function (from, to) {
      var 
        rest = self.items.slice((to || from) + 1 || self.items.length);
        
        self.items.length = from < 0 ? self.items.length + from : from;
        return self.items.push.apply(self.items, rest);
    }
  };




  // look for pcl components
  var 
    elements    = document.getElementsByClassName("pcl"),
    components  = document.getElementsByClassName("pcl component"),
    forms       = document.getElementsByClassName("pcl form");
  var
    count = elements.length; 


  if(count>0) {

    // we have pcl components, so it's an app
    pi.log("found " +  count + " pcl elements" + (count == 1) ? "" : "s" + " on page");
    // load modules for a web app with session support

    for( var i = 0; i < count; i++ ) {
      pi.log("adding component: " + components.item(i).className);
      π.app.components.add(components.item(i));
    }

    π.app.components.__init();
  }


  π.pcl._loaded = true;

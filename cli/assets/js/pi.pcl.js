  /**
   *
   * π.pcl
   *
   * @author Johan Telstad, jt@enfield.no, 2011-2013
   *
   */

  π.pcl = π.pcl || {};


  /**
   *  Initialise PCL elements
   * 
   */


  π.pcl.components = {

    __items : [],
    __elements : null,

    // scan DOM for PCL components
    __scan : function() {
      var 
        self    = π.pcl.components,
        element = null;

      self.__elements = document.getElementsByClassName("pcl");

      pi.log("π.pcl.components.__scan()");

      for( var i = 0, count = self.__elements.length; i < count; i++ ) {
        element = self.__elements.item(i);
        pi.log("found component " + i + ": " + element.className, element);
        π.pcl.components.add(element);
      }

      // we have pcl components, so it's an app
      pi.log("found " +  count + " pcl component" + (count == 1) ? "" : "s" + " on page");

      // return number of items found
      return count;
    },


    // load found components into DOM
    __load : function() {
      var 
        self    = π.pcl.components;

      for( var i = 0, count = self.__elements.length; i < count; i++ ) {
        element = self.__elements.item(i);
        pi.log("loading component " + i + ": " + element.className, element);
        π.pcl.components.add(element);
      }
    },

    __init : function() {

      return this.__scan();
    },

    add : function (elem) {
      var 
        component = {},
        self      = π.pcl.components;

      component.element   = elem;
      component.index     = self.__items.length;
      component.name    = elem.getAttribute("data-name");


      return self.__items.push(component);
    },

    forEach : function (callback) {
      π.pcl.components.__items.forEach(callback);
    },

    // Array Remove - By John Resig (MIT Licensed)
    remove : function (from, to) {
      var 
        rest = this.__items.slice((to || from) + 1 || this.__items.length);
        
        this.__items.length = from < 0 ? this.__items.length + from : from;
        return this.__items.push.apply(this.__items, rest);
    }
  };



  /**
   *  Set up our app to handle pcl forms
   * 
   */

  π.pcl.forms = {

    __items : [],
    __elements : null,

    // scan DOM for PCL forms
    __scan : function() {
      var 
        item = null,
        self = π.pcl.forms;

      self.__elements = document.getElementsByClassName("pcl form");

      for( var i = 0, count = self.__elements.length; i < count; i++ ) {
        item = self.__elements.item(i);
        // pi.log("adding form: " + item.className, item);
        π.pcl.forms.add(item);
      }

      // we have pcl forms, so it's an app
      pi.log("found " +  count + " pcl form" + (count == 1) ? "" : "s" + " on page");

      // return number of items found
      return count;
    },

    __init : function() {

      return this.__scan();
    },

    add : function (elem) {
      var 
        form = {},
        self = π.pcl.forms;

      form.element = elem;

      // new item's index will be same as current array length
      form.index   = self.__items.length;

      // return new item count
      return self.__items.push(form);
    },

    forEach : function (callback) {
      π.pcl.forms.__items.forEach(callback);
    },

    // Array Remove - By John Resig (MIT Licensed)
    remove : function (from, to) {
      var 
        rest = this.__items.slice((to || from) + 1 || this.__items.length);
        
        this.__items.length = from < 0 ? this.__items.length + from : from;
        return this.__items.push.apply(this.__items, rest);
    }
  };



  pi.log("PCL: loaded " + π.pcl.components.__init() + " component(s)");
  // π.pcl.forms.__init();
  pi.log("PCL: loaded " + π.pcl.forms.__init() + " form(s)");


  π.pcl._loaded = true;

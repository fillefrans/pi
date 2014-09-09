  /**
   *
   * π.pcl
   *
   * @author Johan Telstad, jt@viewshq.no, 2011- 2011-2014
   *
   */

  π.pcl = π.pcl || {};


  /**
   *  Set up our app to handle pcl components
   * 
   */

  π.pcl.components = {

    __items : [],
    __elements : null,

    // scan DOM for PCL components
    __scan : function() {
      var 
        item = null,
        self = π.pcl.components;

      self.__elements = document.getElementsByClassName("pcl component");

      for( var i = 0, count = components.length; i < count; i++ ) {
        item = components.item(i);
        pi.log("adding component: " + item.className, item);
        π.pcl.components.add(components.item(i));
      }

      // we have pcl components, so it's an app
      pi.log("found " +  count + " pcl component" + (count == 1) ? "" : "s" + " on page");
      // load modules for a web app with session support
      π.pcl.components.__init();

      // return number of items found
      return count;
    },

    __init : function() {

      return true;
    },

    add : function (elem) {
      var 
        component = {},
        self = π.pcl.components;

      component.element = elem;
      component.index   = self.__items.length;

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
        pi.log("adding form: " + item.className, item);
        π.pcl.forms.add(item);
      }

      // we have pcl forms, so it's an app
      pi.log("found " +  count + " pcl form" + (count == 1) ? "" : "s" + " on page");

      // return number of items found
      return count;
    },

    __init : function() {

      return true;
    },

    add : function (elem) {
      var 
        form = {},
        self = π.pcl.forms;

      form.element = elem;
      form.index   = self.__items.length;

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





  π.pcl._loaded = true;

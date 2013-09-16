

  /** 
   *  π.app.bootstrap
   * 
   *  App bootstrapper
   * 
   */


   π.app = π.app || {};


  π.app.__init = function (DBG) {
    pi.log("ready to run: ", this);
    return true;
  };




  
  // whatever you need to do to start things up
  π.app.run = function (DBG) {
    var
      self = π.app;

    if (!self.__init(DBG)) {
      return false;
    }
    pi.log("ready to run: ", this);

  };





  // run app
  π.app.run();

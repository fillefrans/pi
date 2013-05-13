

  /**
   *  App bootstrapper
   * 
   */
  
  // load your dependencies
  π.require("app.session");

  // list other assets you may need later (false means we don't need it right away.)
  π.require("app.util", false);

  // whatever you need to do to start things up
  π.app.run = function (DBG) {
      console.log("ready to run: ",this);
    };

  // start your app
  π.app.run();

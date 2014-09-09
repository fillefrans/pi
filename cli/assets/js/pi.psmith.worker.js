  /**
   *
   * π.psmith.worker
   *
   * @description
   * 
   * Psmith is a background helper 
   * running as a Web Worker. 
   * communicates over the PostMessage API
   *
   * @author Johan Telstad, jt@viewshq.no
   * @copyright Views AS, 2014
   * 
   */


   /*

  Web Workers have access to the following 

    XMLHttpRequest
    Application Cache
    create other web workers
    navigator object
    location object
    setTimeout method
    clearTimeout method
    setInterval method
    clearInterval method
    importScripts method
    JSON
    Worker


  and NOT access to the following

    localStorage
    WebSocket
    DOM
    addEventListener


  */


  importScripts('pi.worker.js');





   var
    psmith = {

      // private

      __init : function (argument) {
        
      },

      // protected


      onmessage : function (msg, obj) {

      },


      // public

      reply : function (obj) {
        postMessage(address, obj);
      },


      run : function (argument) {
        
      }      
    };



// doop.no



onmessage = psmith.onmessage;



/* as pleases inquirer */
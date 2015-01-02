/**
 *  pi.worker.pi
 *
 *  Web worker example that demonstrates how even an infinite loop will not 
 *  affect the user interface.
 *  
 *  Calculate π to infinity
 *
 *  pi algo by Robert Gravelle, from his tutorial about Web Workers:
 *    <http://www.htmlgoodies.com/html5/tutorials/introducing-html-5-web-workers-bringing-multi-threading-to-javascript.html#fbid=wK7PwxEJF6A>
 *
 *  Check it out. It's excellent.
 * 
 */


  // math.pi.bbp contains a js implementation of the bbp formula for calculating π to a very high precision
  importScripts('pi.math.pi.bbp.js');

  // math.pi.longmath contains a js implementation of calculating pi using long math
  // importScripts('pi.math.pi.longmath.js');

  // a console emulation that passes log messages to the owner thread.
  importScripts("pi.worker.console.js");


  /* Event handlers */

  self.onmessage = function(msg) {
    var
      i = 0;

    if (!msg.data.message.command) {
      self.__error( "No command given: " + msg.data.message.command);
      return;
    }

    switch (msg.data.message.command) {
      case "invoke": {
        var 
          func = self[msg.data.message.func];

        if (!!!func) {
          self.postMessage({event: "error", message: "Function not found: " + msg.data.message.func, debug: {data : msg.data}});
          return false;
        }

        func(msg.data.message.parameters[0], msg.data.message.parameters[1] || null, msg.data.message.parameters[2] || null);
        break;
      }
      default: {
        self.__error("Unknown command: " + msg.data.message.command);
      }
    }
  }


  /* private functions */


  function __error (msg) {
    self.postMessage({event: "error", message: msg});
  }


  function __update (value, iteration, iterations) {
    if (value == "") {
      self.stop();
    }
    self.postMessage({event: "data", value: value, debug: {info: {iteration: iteration || 0, iterations: iterations || -1}}});
  }

  function __progress (value, total, time) {
    self.postMessage({type: "msg.progress", value: value || 0,  total: total || -1, time: time || 0});
  }



  function π_longmath (precision) {
    var
      start = new Date(),
      π = "",
      end = null;

    π = calcPI(precision);
    
    self.__update(π);

    end = new Date();
    var totaltime = end.getTime()-start.getTime();

    self.postMessage( { type: "msg.complete", message: "<br>Completed calculating " + precision + " digits of pi in " + totaltime + " ms."} );
  }


  function π_bbp(iterations, updateinterval) {

    var 
      π = "", n = 1, i = 0, 
      π_chunk  = "",
      start    = new Date(),
      end      = null, 
      index    = 0,
      interval = updateinterval || 10000;

    while( ++i < iterations || iterations === -1 ) {

      π_chunk = bbp(index+=10);
      if (parseInt(π_chunk, 16)==0) {
        //console.log("π_chunk is: " + π_chunk);
      }
      π += π_chunk + "  ";
      if( i % interval === 0) {
        self.__update(π_chunk, i+1, iterations);
      }
    }

    end = new Date();
    var totaltime = end.getTime()-start.getTime();
    var mean = parseInt(totaltime/i);
    console.log ("Completed " +i+ " iterations in " + totaltime + " ms. Mean time per iteration: " + mean + " ms");
    self.postMessage( { type: "msg.complete", message: "<br>Completed " +i+ " iterations in " + totaltime + " ms. Mean time per iteration: " + mean + " ms"} );
  }



  function π(iterations, updateinterval) {

    var 
      π = 0, n = 1, i = 0, 
      start    = new Date(),
      end      = null, 
      interval = updateinterval || 100000;

    while( iterations === -1 || i++ < iterations ) {

      π += (4/n) - (4/(n+2)); 
      n += 4;
      if( i % interval === 0) {
        self.__update(π, i, iterations);
      }
    }

  end = new Date();
  self.postMessage( { type: "msg.complete", message: "Completed in " + (end.getTime()-start.getTime()) + " millisec"} );
  }

//  π();
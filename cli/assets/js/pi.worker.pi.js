/**
 *  pi.worker.pi
 *
 *  Web worker example that demonstrates how even an infinite loop will not 
 *  affect the user interface.
 *  
 *  Calculate π to infinity
 *
 *  Algo by Robert Gravelle, from his tutorial about Web Workers:
 *    <http://www.htmlgoodies.com/html5/tutorials/introducing-html-5-web-workers-bringing-multi-threading-to-javascript.html#fbid=wK7PwxEJF6A>
 *
 *  Check it out. It's excellent.
 * 
 */



  function π() {

    var 
      π = 0, n = 1;

    // don't try this at home.
    while(1) {
      π += (4/n) - (4/(n+2)); 
      n += 4;
      self.postMessage({'π': π});
    }
  }

  π();
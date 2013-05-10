/**
 *  π.worker.π 
 * 
 *  Calculate π to infinity
 *
 * 
 *  Algo by Robert Gravelle, from his tutorial about Web Workers:
 *    <http://www.htmlgoodies.com/html5/tutorials/introducing-html-5-web-workers-bringing-multi-threading-to-javascript.html#fbid=wK7PwxEJF6A>
 *
 *  Check it out. It's excellent.
 * 
 */


  self.onmessage = function(e) {π()};


  function π() {

    var 
      π = 0, n = 1;

    while(1) {
      π += (4/n) - (4/(n+2)); 
      n += 4;
      self.postMessage({'π': π});
    }
  }
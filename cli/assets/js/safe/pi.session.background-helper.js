/**
 *
 * KROMA π - Background Worker
 *
 * π.core.background-worker
 *
 * 
 * A web worker used to offload as much processing as possible from the main thread
 *   
 * @author Johan Telstad, jt@kroma.no, 2013
 *
 */


/**
 *
 *  navigator object
 *  
    appName
    appVersion
    platform
    userAgent

 * 
 */

//  importScripts('foo.js', 'bar.js');      /* import two scripts and execute in order */
//  you should only do a quick setup in imported scripts, since they are blocking





/**   GLOBALS
 *
 *    We define all globals as object literals: it allows us to build namespaces
 */


//  thread global constants

var
    INITIALIZED   = false,
    SYSTEM        = {

      os    : navigator.platform,
      info  : 'System and device info and utilities.'
    };


//  thread global variables

var

    //  simple variables on top
    events   = [];



//  thread global namespaces
//  Remember: these are objects, not variables - use ":" instead of "="
var 

    SYSTEM = {
      browser   :  navigator.appName + ' ' + navigator.appVersion,
      os        :  navigator.platform,


    __info: 'System and device info and utilities.'
  };


//  thread global functions

var 

  binarySearch = function (list, val) {

    var min = 0, 
        max = list.length-1,
        mid = (min + max) >> 1,
        dat = list[mid];

    for(;;) {

      if (min + 11 > max) {
        for(var i=min ; i<=max; i++) {
          if(val === list[i]) {
            return i;
          }
        }
        return -1;
      }

      if ( val === dat ) {
        return mid;
      } 

      if( val > dat ) {
        min = mid + 1;
      } 
      else {
        max = mid - 1;
      }
    } // for(;;)

  },
  // :end binarySearch

  /**  ------  ADD comma-separated FUNCTIONS BELOW  ------      */






  /**  ^^^^^^  ADD comma-separated FUNCTIONS ABOVE  ^^^^^^      */

  _: null
};

  /** :end globals  */





// import and run scripts as you please
importScripts();


// start up our event listener, connect to the outside world

self.addEventListener('message', function(e) {
  self.postMessage(e.data);
}, false);






/**
 * Why π, you may ask. Because.
 *
 * Because why, you might insist. Because BECAUSE.
 * 
 */


  var
    worker  = new WebWorker('../../assets/js/pi.worker.pi.js');
    path    = "../../assets/js/",
    cursor  = document.getElementsByTagName ("head")[0] || document.documentElement,
    script  = null,

    include = {

      //includes for lazy-loading
      js: {

        // an empty object means we want the core module without submodules
        core    : {
          worker : true
        },
        app     : {},
        dom     : {},
//        session : true,
        events  : {},
        util    : {},
        plugins : {}
      },
  
      css  : {},
  
      img  : {},
  
      data : {}
    };


    // load pi.js
  var 

    head    = document.getElementsByTagName ("head")[0] || document.documentElement,
    script  = document.createElement('script'),
    path    = '../../assets/js/pi.'; 
    script.async  = false;
    script.defer  = true;
    script.src    = path + 'js';
    script.self   = script;
    script.onload = function (event) {
      console.log('script.onload: ', this.src);
      console.log('this.self: ', this.self);
    };

    script.onerror = function (event) {
      console.log('script.onerror: ', this.src);
    };



    cursor.insertBefore( script, head.firstChild ); 


    // load modules synchronously, but deferred
    for (var module in include.js) {

      script = document.createElement('script');
      script.async  = false;
      script.defer  = true;
      script.src    = path + module + '.js';
      cursor.insertBefore( script, head.firstChild ); 

      // then load submodules asynchronously and deferred
      for (var submodule in include.js[module]) {
        script = document.createElement('script');
        script.async  = true;
        script.defer  = true;
        script.src    = path + module + '.js';
        cursor.insertBefore(script, head.firstChild); 
      }
    }





    function getSource (module) {

      var 
        uri = '../../../srv/php/pi.util.file.highlight.php?file=',
        req = new XMLHttpRequest();
      
      req.onload = function() { 

        var 
          html = "",
          obj  = JSON.parse(this.responseText),
          div  = document.getElementById('article');

        if(!!obj.data) {
          obj.data.forEach( function(val, idx) {
            html += val;
          });

        var 
          fragment  = document.createDocumentFragment(),
          container = document.createElement("div");

        container.innerHTML = html;
        fragment.appendChild(container);
        div.appendChild(fragment);
        }
      };

      req.onerror = function(error) { 
        console.log(error);
      };

      req.open("get", uri + module, true);
      req.send();
    }


  // test it

    getSource('pi.session');

  </script>

##π


####Que?

pi is a distributed application platform for HTML5

- fast
- efficient
- extensible
- flexible
- scalable
- provides a communication network between all parts of the namespace
- only available in HTML5-compatible browsers
- real-time
- namespaced


The central concepts in pi are : channel, address, object and type

ADDRESS
- an address is a path in the pi namespace: 
    "pi.user.8739"
    "pi.service.time.tick"
    "pi.app"

CHANNEL
- a channel is a filter that allows separation of traffic to the same address

- a channel and an address are given together in the following style: 
    "db|pi.user.8739"
    "zmq:9001|pi.service.myzmqservice"

- there are 15 pre-defined channels in Pi

    - 0 : BASE
    - 1 : AUTH
    - 2 : CHAT
    - 3 : DEBUG
    - 4 : WARNING
    - 5 : ERROR
    - 6 : LOG
    - 7 : TYPE
    - 8 : DB
    - 9 : PING
    - 10 : CTRL
    - 11 : ADMIN
    - 12 : SYS

    - 14 : PUSH
    - 15 : ZMQ


- external addresses look like: 
    "db|pi.user.8739@pi.viewshq.no:8080/api/pi.io.db.php"
    "zmq:9001|pi.service.myzmqservice@zmq.myservice.com:7800/services/zmq/myzmq/"



Pi is inspired by flow-based programming and the old-school linux toolchain, where specialized, highly optimized agents can be configured into flexible chains of processing.



about pi

    a minuscule platform with majuscule impact

    scales and connects biologically (self-replication, self-organization, self-containment, data chaining, waste data, timing across & within processes and time scales)

    every tick is like a heartbeat

    

    allows us to work in parallell and sequentially at the same time, and do it real-time for all of
    the connected parts, and send only the changes made elsewhere to each individual part, while
    receiving only changes made in return

    separating above/below in the namespace and equating to before/after, allows us to
    collapse the changes into their sum at each tick (on any level), and efficiently update the state 
    of the dataset that represents the (local or global) network at that point

    that means we can reliably combine stored data with live data as they come in, since
    we know how many ticks there were between the state of the dataset received from db, and
    the tick at which we started receiving data

    then we can discard the duplicates from the overlap and start receiving only changes 
    to the dataset, which is more efficient



####Kudos
Pi could not exist without Redis, an in-memory database with persistence to disk. 



###Inspirations
* [Salvatore Sanfilippo](http://antirez.com/), creator of [Redis](http://redis.io)
* J. Paul Morrison's [Flow Based Programming](http://www.jpaulmorrison.com/fbp/) - [Wikipædia](http://en.wikipedia.org/wiki/Flow-based_programming)
* the unix toolchain



####General
* We are requiring HTML5, and using native functions wherever possible
* WebWorkers allow for background processing.
* GreenSock [GSAP](https://www.greensock.com/tag/tutorial/).
* Leaflet [Leaflet](http://leafletjs.com/) as a replacement for Google Maps.

* [CSS injection of SVG](http://www.somerandomdude.com/2012/08/12/svg-css-injection/) might be something to look at


####Client-side
* Components should be self-contained.
* Use documentFragment when adding more than one node to the DOM at a time



####Server-side optimizations
* Replace the PHP serializer with [igbinary](https://github.com/igbinary/igbinary).
* Compile Redis with  [ --enable-redis-igbinary ], to enable binary communication with Redis.
* Compile Redis as 32-bit, even on 64-bit systems. This is more memory-efficient, as Redis is very pointer-intensive
* Possible to use Redis for PHP session storage and application shared memory.


##Documentation
You're reading it, as things stand

---


##Philosophy
* Optimize for devices
* Optimize server/cache setup
* Background processing where sensible and possible
* Modular loading of resources
* Cross-browser, within reason
* Cross-device support, again within reason


##Target browsers
* WebKit + Firefox, all platforms
* iOs >= 5.1
* Android >= 4.0 (Except native browser)
* IE 10 on Windows 7/Windows Phone 8
* In effect, any modern browser with WebSocket support



###Not supported
* Opera Mini
* Android native browser has no WebSocket, and no Web Worker.
* IE < 9


####Client Libraries
* [Crossfilter.js](http://square.github.io/crossfilter/)
* [Raphaël - SVG library](http://raphaeljs.com/)


###Server libraries
* [ApnsPHP: Apple Push Notification & Feedback Provider](https://github.com/duccio/ApnsPHP)


###Miscellaneous
* [why-arent-you-using-git-flow](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/)
* [git-flow - A collection of Git extensions to provide high-level repository operations for Vincent Driessen's branching model](https://github.com/nvie/gitflow)
* [git-flow cheat sheet](http://danielkummer.github.io/git-flow-cheatsheet/)
* [Google PageSpeed](https://developers.google.com/speed/pagespeed/)
* [Charles Web Debugging Proxy - Windows/Mac/Linux](http://www.charlesproxy.com/)
* [caniuse.com - HTML5 browser support by feature](http://caniuse.com)
* [jsPerf — JavaScript performance playground](http://jsperf.com/)


### Other
* [Push Notification Plugin for iOS and Android](https://github.com/phonegap-build/PushPlugin)



###Speed and optimization guidelines:

* Load CSS in head section, to improve rendering consistency
* Files larger than 32KB (uncompressed) are never cached on many mobile devices
* Avoid DOM manipulation wherever possible
* use requestAnimationFrame
* use documentFragment
* use getClientBoundingRect
* use WebWorkers
* localStorage can probably be used even for css and scripts



###HTML5:
* [Transferable Objects](http://html5-demos.appspot.com/static/workers/transferables/index.html)
* [The TIME tag](http://www.brucelawson.co.uk/2012/best-of-time/)



### Further reading
* [Android Push Notifications with PhoneGap](http://www.adobe.com/devnet/phonegap/articles/android-push-notifications-with-phonegap.html)
* [More Bandwidth Doesn’t Matter (much)](http://www.belshe.com/2010/05/24/more-bandwidth-doesnt-matter-much/)
* [Make your mobile pages render in under one second](http://calendar.perfplanet.com/2012/make-your-mobile-pages-render-in-under-one-second/)
* [GoogleTechTalks - Speed Up Your JavaScript](http://www.youtube.com/watch?v=mHtdZgou0qU&feature=channel_page)
* [Mastering HTML5 Prefetching](http://www.catswhocode.com/blog/mastering-html5-prefetching)



###Debug
* [Airline on-time performance dataset](http://stat-computing.org/dataexpo/2009/)
* [Using the Android Emulator](http://developer.android.com/tools/devices/emulator.html)
* [Viewport resizer - Responsive design bookmarklet](http://lab.maltewassermann.com/viewport-resizer/)


###Tricks
* [Running multiple instances of Redis](http://chrislaskey.com/blog/342/running-multiple-redis-instances-on-the-same-server/)


###Snippets

####Detect mobile user agent with regex:
    var isMobile = /ip(hone|od|ad)|android|blackberry.*applewebkit|bb1\d.*mobile/i.test(navigator.userAgent);


####Speed up DOM manipulation with DocumentFragment:
    var div = document.getElementsByTagName("div");

    var fragment = document.createDocumentFragment();
    for ( var e = 0; e < elems.length; e++ ) {
        fragment.appendChild( elems[e] );
    }

    div[i].appendChild(fragment);

  - [DOM speed tips, documentFragment](http://stackoverflow.com/a/14049291)


###Browser support
  - Any browser with WebSockets: [caniuse - WebSockets](http://caniuse.com/#feat=websocket)




##Technologies

* HTML5:  WebSockets, WebWorkers, native array functions (each, some, filter, typedarray, etc)
* [Varnish](http://varnish-cache.org)
* [Redis](http://redis.io)
* PHP
    - [phpredis](https://github.com/nicolasff/phpredis‎) 
    - [igbinary](https://github.com/igbinary/igbinary) 
    - [phpws](https://github.com/Devristo/phpws/)




####[snippets]


    /* CSS inheritance from class to id */

    div.parentDiv,
    #childDiv
    {
        margin-top:3px;
        margin-left:auto;
        margin-right:auto;
        margin-bottom:0px;
    }

    /*  This will tell your #childDiv to apply the same styles as div.parentDiv has. 
        Then you extend this set of styles with more  specific to your #childDiv:
    */
    #childDiv
    {
        background-image:url("customBackground.gif");
        background-repeat:no-repeat;
        width:950px;
        height:572px;
    }


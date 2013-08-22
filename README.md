##π


####What is π?

First and foremost, it is a tool for building single-page web and mobile apps/platforms.

The short version is:

- π is pretty fast
- extensible
- and very flexible
- is heavily optimized for mobile devices
- uses mostly messaging instead of callbacks
- uses websockets instead of AJAX
- uses namespaces 
- uses background processing to avoid blocking the user thread
- does not rely on other libraries
- is designed to work optimally with Varnish and Redis
- provides a communication network between all parts of the namespace
- requires an HTML5-compatible browser. 


The central concept in π is the shared namespace across the client and server. 

There are in fact two namespaces: one namespace organizes objects and components in the web app.
The other namespace is an addressing namespace that allows message passing between arbitrary parts of the entire system of users, apps and services. 

Over this messaging system, an app can also communicate with another app or even a specific user session in another app.


Any part of the namespace may interact with any other part: server, app, user, module, plugin, component, &c

As an example: a server script may be invoked by a user session in an app. The app 
can then subscribe to a dedicated channel where the server script publishes progress events. 

Another example: two apps may define a common namespace for exchanging messages and events. A server currency exchange rate updating script might post messages with updates to a portfolio server application, or to users who have subscribed to the service via its address in the global namespace.


Any server software can access the PHP session information for any client, since it is stored in Redis.

Components can be loaded into apps on the fly, or queued for preloading. Scripts and CSS can be injected into the DOM in three ways:
   1. By adding a script-src tag to the DOM (good for using browser cache)
   2. Loaded over AJAX into the browser 
   3. Loaded over WebSocket, and injected into the DOM

For 2. and 3., it is possible to store documents and snippets in HTML5's localStorage


π is a bare-bones but extensible set of libraries that serves as platform for developing real-time client-server web apps/components/sites, with a modular architecture and a built-in dependency system using synchronous or asynchronous on-demand loading and preloading. 


π is inspired by flow-based programming and the old-school linux toolchain, where specialized, highly optimized agents can be configured into flexible chains of processing.


####Kudos
Pi could not exist without Redis, an in-memory database with persistence to disk. 



###Inspirations
* [Salvatore Sanfilippo](http://antirez.com/), creator of [Redis](http://redis.io)
* The [GreenSock Animation Library](http://greensock.com)
* Higgins' PubSub
* J. Paul Morrison's [Flow Based Programming](http://www.jpaulmorrison.com/fbp/) - [Wikipædia](http://en.wikipedia.org/wiki/Flow-based_programming)
* the unix toolchain



####General
* We are requiring HTML5, and using native functions wherever possible
* Check out [Leaflet](http://leafletjs.com/) as a replacement for Google Maps.
* Vector graphics is great for Retina displays.
* GreenSock is an almost hilariously good animation library. And it's small. [GSAP](https://www.greensock.com/tag/tutorial/).
* WebWorkers are awesome. 
* For size and position, using perfect cubes (or multiples): 8, 64, 216, 512, makes it easier to stack components.

* [CSS injection of SVG](http://www.somerandomdude.com/2012/08/12/svg-css-injection/) might be something to look at


####Client-side
* Components should be self-contained.
* The π.app and π.plugins namespaces are open to anyone.
* Feel free to write a better xhr module :) 
* Use CSS inheritance 
* Use documentFragment when adding more than one node to the DOM, it's faster



####Server-side optimizations
* Replace the PHP serializer with [igbinary](https://github.com/igbinary/igbinary).
* Compile Redis with  [ --enable-redis-igbinary ], to enable binary communication with Redis.
* Compile Redis as 32-bit, even on 64-bit systems. This is more memory-efficient.
* Use Redis for PHP session storage and application shared memory.


##Documentation
When we're at version 0.6 or thereabouts.

---


##Aims
* Optimized for Mobile
* Optimized server/cache setup
* Background processing where sensible
* Modular loading of resources
* Cross-browser, within reason
* Cross-device support, again within reason
* If possible, generic access to a subset of native device resources from JavaScript, such as accelerometer, camera, geolocation
* A plugin system
* An extensible visual component library called pcl


##Target Clients
* WebKit + Firefox, all platforms
* iOs >= 5.1
* Android >= 4.0 (Except native browser)
* IE 10 on Windows 7/Windows Phone 8
* In effect, any modern browser with WebSocket support



###Not supported
* Opera Mini
* Android native browser has no WebSocket, and no Web Worker.


####Client Libraries
* [GreenSock Animation Platform](http://greensock.com/)
* [Raphaël - SVG library](http://raphaeljs.com/)


###Server libraries
* [ApnsPHP: Apple Push Notification & Feedback Provider](https://github.com/duccio/ApnsPHP)


###Tools
* [git-flow - A collection of Git extensions to provide high-level repository operations for Vincent Driessen's branching model](https://github.com/nvie/gitflow)
* [git-flow cheat sheet](http://danielkummer.github.io/git-flow-cheatsheet/)
* [USB Remote debugging with Chrome Developer Tools](https://developers.google.com/chrome-developer-tools/docs/remote-debugging#remote-debugging)
* [LiveReload - Chrome extension](https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei?hl=en)
* [Google PageSpeed](https://developers.google.com/speed/pagespeed/)
* [mod_pagespeed - Apache module for automatic mobile optimization](https://developers.google.com/speed/pagespeed/mod)
* [mod_spdy - Apache SPDY module](http://code.google.com/p/mod-spdy/) -> [browser support](http://caniuse.com/spdy/)
* [Charles Web Debugging Proxy - Windows/Mac/Linux](http://www.charlesproxy.com/)
* [Google Developers - Web Performance Best Practices](https://developers.google.com/speed/docs/best-practices/)
* [CanIuse.com - HTML5 browser support by feature](http://caniuse.com)
* [jsPerf — JavaScript performance playground](http://jsperf.com/)


### Extras
* [Push Notification Plugin for iOS and Android](https://github.com/phonegap-build/PushPlugin)
* [howler.js - Modern Web Audio Javascript Library](http://goldfirestudios.com/blog/104/howler.js-Modern-Web-Audio-Javascript-Library)


###Speed and optimization:

* Load CSS in <head>
* Files larger than 32KB (uncompressed) are never cached on many mobile devices
* Avoid DOM manipulation
* use documentFragment
* use getClientBoundingRect
* use WebWorkers
* localStorage can be used even for css and scripts



### Further reading
* [Android Push Notifications with PhoneGap](http://www.adobe.com/devnet/phonegap/articles/android-push-notifications-with-phonegap.html)
* [More Bandwidth Doesn’t Matter (much)](http://www.belshe.com/2010/05/24/more-bandwidth-doesnt-matter-much/)
* [Make your mobile pages render in under one second](http://calendar.perfplanet.com/2012/make-your-mobile-pages-render-in-under-one-second/)
* [GoogleTechTalks - Speed Up Your JavaScript](http://www.youtube.com/watch?v=mHtdZgou0qU&feature=channel_page)
* [Mastering HTML5 Prefetching](http://www.catswhocode.com/blog/mastering-html5-prefetching)



###Testing & debugging
* [Using the Android Emulator](http://developer.android.com/tools/devices/emulator.html)
* [Viewport resizer - Responsive design bookmarklet](http://lab.maltewassermann.com/viewport-resizer/)


###Tricks & fixes
* [Running multiple instances of Redis](http://chrislaskey.com/blog/342/running-multiple-redis-instances-on-the-same-server/)
* [How to use git-flow](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/)
* [A fix for the iPhone ViewPort scale bug](http://www.blog.highub.com/mobile-2/a-fix-for-iphone-viewport-scale-bug/)


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


###Browser support
  - Any browser with WebSockets: [caniuse - WebSockets](http://caniuse.com/#feat=websocket)




##Technologies

* HTML5 native functions
* WebSockets
* WebWorkers
* [Varnish](http://varnish-cache.org)
* Apache
* PHP
    - [phpredis](https://github.com/nicolasff/phpredis‎) 
    - [igbinary](https://github.com/igbinary/igbinary) 
    - [phpws](http://code.google.com/p/phpws/)
* [Redis](http://redis.io)
* Free Pascal
    - [Bauglir2 WebSocket library](http://code.google.com/p/bauglir-websocket/)
    - [synapse](http://synapse.ararat.cz/doku.php/start)


####Useful snippets




    /* CSS inheritance from class to id */

    div.someBaseDiv,
    #someInhertedDiv
    {
        margin-top:3px;
        margin-left:auto;
        margin-right:auto;
        margin-bottom:0px;
    }

    /*  This will tell your #someInhertedDiv to apply the same styles as div.someBaseDiv has. 
        Then you extend this set of styles with more  specific to your #someInhertedDiv:
    */
    #someInhertedDiv
    {
        background-image:url("customBackground.gif");
        background-repeat:no-repeat;
        width:950px;
        height:572px;
    }


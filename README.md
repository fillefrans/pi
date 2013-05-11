#π


###What is π?

There is a tendency today of throwing hardware and cloud servers at inefficient applications. 

π is the polar opposite of this approach. A super-fast, bare-bones set of libraries that serves as platform for developing Client-Server web apps/sites, with a modular architecture and a built-in dependency system using asynchronous on-demand loading. 

Built to satisfy the need for speed, π aims to be the race car to $'s bus. 

Call it LAAP (Libraries as a platform).

π is Client-Server oriented. It creates a common namespace across the client and server, with seamless bi-directional access to data, events and pubsub.


###Who is it for?
Web nerds who feel the need for speed. 


###Who decides what goes in it?
[jsPerf](http://jsperf.com)



###Inspirations
* [Salvatore Sanfilippo](http://antirez.com/latest/0), creator of [Redis](http://redis.io)
* The [GreenSock Animation Library](http://greensock.com)
* Higgins' PubSub
* J. Paul Morrison's [Flow Based Programming](http://www.jpaulmorrison.com/fbp/) - [Wikipædia](http://en.wikipedia.org/wiki/Flow-based_programming)



##Technologies

* HTML5 native functions
* WebSockets
* Varnish
* Apache
* PHP
    - [phpredis](https://github.com/igbinary/igbinary) 
* Redis
* PhoneGap for app installation and HW access



###Rules for front-end developers:

####General
* No modernizr, we are requiring HTML5 already
* No jQuery or plugins, pretty please
* Write your own plugins
* If you really must have the $, use jqMobi
* No Google Maps. Use [Leaflet](http://leafletjs.com/) instead.
* Vector graphics are preferred for almost everything.
* Use GreenSock for animations. If you don't know it already, you should [learn it](https://www.greensock.com/tag/tutorial/).
* Use inline base64-encoding for small images and SVGs ()
* Don't load a whole font for the title or name of the website. Use SVG, or a Google Fonts subset
* Use SVG for logos. Think Retina.
* [CSS injection of SVG](http://www.somerandomdude.com/2012/08/12/svg-css-injection/) is best practice for now
* Type your CSS, and use inheritance. Don't copy-paste
* Grow a serious distaste for the flash of unstyled content
* Don't use !important
* Don't attach event listeners outside of your container. Send a message.
* Don't overuse the pubsub, especially not the pub
* Use web workers wherever you can. You should have at least one worker per app, so you can offload as much processing as possible to the background thread. 
* For size and position, prefer perfect cubes (or multiples): 8, 64, 216, 512. This makes it easier to stack components.





###Rules for developers:

####General
* No bloat. 
* Use HTML5 best practice


####Client-side
* No jQuery. Ever.
* Don't use Sizzle, either. Native functions are faster.
* Don't include any frameworks.
* Use messaging between components, and don't attach events to DOM elements. Only components should touch the DOM.
* Only the most minimal efforts to correct browser idiosyncrasies. If one particular browser is very bad at something, then we encourage you to expose that suckiness by not pandering to it. When encounter turd, don't sprinkle with sugar.
* Components have to be self-contained.
* It is allowed to create new subnodes under the π.app and π.plugins namespaces.
* Absolutely no XHR in the core library, even as a fallback. If you need compatibility, use Zepto or similar. 
* Write tests. Or don't. We don't care.
* Resist any inclination towards MVC and two-way data binding.
* No coffeescript, less, sass, or other languages that has to be compiled server-side
* Use CSS inheritance over explicit setting of every property 
* Use documentFragment when adding more than one node to the DOM



####Server-side
* Replace the PHP serializer with [igbinary](https://github.com/igbinary/igbinary).
* Compile Redis with  [ --enable-redis-igbinary ], to enable binary communication with Redis.
* Compile Redis as 32-bit, even on 64-bit systems. This is more memory-efficient.
* Use Redis for session storage and application shared memory.
* Use Redis pubsub for sending messages to app clients.
* Don't use Node.js.
* Don't use MongoDB.
* No REST servers, please. (Unless you create a REST interface accessible over the session WebSocket)
* No server application that runs in a VM, including Java, Node.js, Erlang, &c
* There will not be a Windows version of the server


#Documentation
When we're at version 0.6 or thereabouts.


##Mission

To create a HTML5/CSS3/JS template/scaffolding that can serve as a starting point for apps and web Pages. Incorporate mobile optimized client-side services accessible to creatives.

Collect examples and demos in a git repository. Use shared assets where possible.


---

##Requirements
* Optimized for Mobile
* Optimized server/cache setup
* Cross-browser, within reason
* Cross-device support (See Target Platforms, below)
* If possible, generic access to a subset of native device resources from JavaScript, such as accelerometer, camera, geolocation
* A plugin system
* Easy to copy a template and start adapting it



##Target Platforms
* WebKit + Firefox, all platforms
* iOs >= 5.1
* Android >= 4.0 (Except native browser)
* IE 10 on Windows 7/Windows Phone 8



###Not supported
* Opera Mini
* Android native browser has no session support, and no server bridge.


###Frameworks

We don't believe in frameworks.


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


##Implementation

A global JavaScript object π will have a basic bootstrapper, and preload scripts in the background. 


###For speed and optimization, keep in mind:

* Load CSS in <head>
* Files larger than 32KB are not cached on many iPhones
* Avoid DOM manipulation as far as possible



##Resources
----------------------------
* [Game Content Resources](http://content.gpwiki.org/index.php/Game_Content_Resources)
* [Open Game Art](http://opengameart.org/art-search?keys=icon&page=1)
* [Volumetric Sprites](http://gushh.net/blog/gamedev-resources/volumetric-sprites/)



### Further reading
* [Android Push Notifications with PhoneGap](http://www.adobe.com/devnet/phonegap/articles/android-push-notifications-with-phonegap.html)
* [Accelerometer & Gyro Tutorial](http://www.instructables.com/id/Accelerometer-Gyro-Tutorial/)
* [iOS/Android Device orientation (pitch, yaw, roll). Is it better with accelerometer or gyroscope?](http://stackoverflow.com/questions/9304160/ios-android-device-orientation-pitch-yaw-roll-is-it-better-with-acceleromet?rq=1)
* [MDN - Orientation and motion data explained](https://developer.mozilla.org/en-US/docs/DOM/Orientation_and_motion_data_explained)



###Reports
* [Apache config for HTML5 Mobile Boilerplate ](https://github.com/h5bp/server-configs/tree/master/apache)
* [jQuery vs Zepto vs jQMobi - which one is the fastest?](http://www.codefessions.com/2012/08/performance-of-jquery-compatible-mobile.html)
* [More Bandwidth Doesn’t Matter (much)](http://www.belshe.com/2010/05/24/more-bandwidth-doesnt-matter-much/)
* [Make your mobile pages render in under one second](http://calendar.perfplanet.com/2012/make-your-mobile-pages-render-in-under-one-second/)
* [GoogleTechTalks - Speed Up Your JavaScript](http://www.youtube.com/watch?v=mHtdZgou0qU&feature=channel_page)
* [Mastering HTML5 Prefetching](http://www.catswhocode.com/blog/mastering-html5-prefetching)



###Testing & debugging
* [Using the Android Emulator](http://developer.android.com/tools/devices/emulator.html)
* [Viewport resizer - Responsive design bookmarklet](http://lab.maltewassermann.com/viewport-resizer/)


###Docs
* [iOS Dev Center](https://developer.apple.com/devcenter/ios/index.action)
* [PhoneGap API documentation](http://docs.phonegap.com/en/2.5.0/index.html)
* [DeviceMotion W3 Specification](http://dev.w3.org/geo/api/spec-source-orientation.html#devicemotion)
* [Differences between Native Apps and Mobile Web Apps](http://en.wikipedia.org/wiki/HTML5_in_mobile_devices#Differences_from_Native_Apps_and_Mobile_Web_Apps)


###Examples
* [Camera and Video Control with HTML5](http://davidwalsh.name/browser-camera)
* [seismograph.js - WebKit DeviceMotion / MozDeviceOrientation example](http://isthisanearthquake.com/seismograph.html)
* [Accessing Accelerometer on Flash/Android 2.2 - example](http://www.mobilexweb.com/blog/android-froyo-html5-accelerometer-flash-player)
* [PhoneGap accelerometer example](http://www.mobilexweb.com/samples/ball.html)

###Videos
* [Video: Google I/O 2012 - High Performance HTML5](http://www.youtube.com/watch?v=6EJ801el-I8)
* [Video: Google I/O 2012 - Making Good Apps Great: More Advanced](http://www.youtube.com/watch?v=PwC1OlJo5VM)


###HTML5 Demos
* [Sencha Touch demos](http://www.sencha.com/products/touch/demos/)
* [Apple HTML5 demos](http://www.apple.com/html5/)
* [Chrome Experiments](http://www.chromeexperiments.com/)


###Tricks & fixes
* [Detect rotation of Android phone in the browser with javascript](http://stackoverflow.com/questions/1649086/detect-rotation-of-android-phone-in-the-browser-with-javascript)
* [How to access accelerometer/gyroscope data from Javascript?](http://stackoverflow.com/questions/4378435/how-to-access-accelerometer-gyroscope-data-from-javascript/4378439)
* [How to use git-flow](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/)
* [A fix for the iPhone ViewPort scale bug](http://www.blog.highub.com/mobile-2/a-fix-for-iphone-viewport-scale-bug/)


###Snippets
####Detect mobile user agent with JS regex:
    var isMobile = /ip(hone|od|ad)|android|blackberry.*applewebkit|bb1\d.*mobile/i.test(navigator.userAgent);


####Speed up DOM manipulation with DocumentFragment:
    var div = document.getElementsByTagName("div");

    var fragment = document.createDocumentFragment();
    for ( var e = 0; e < elems.length; e++ ) {
        fragment.appendChild( elems[e] );
    }

    div[i].appendChild(fragment);


###Browser/device feature support
* [caniuse - DeviceOrientation API](http://caniuse.com/#feat=deviceorientation)
* [caniuse - WebSockets](http://caniuse.com/#feat=websocket)
* [caniuse - WebWorkers](http://caniuse.com/#feat=webworker)
* [caniuse - GeoLocation API](http://caniuse.com/#feat=geolocation)
* [caniuse - CORS (Cross-Origin Resource Sharing)](http://caniuse.com/#feat=cors)
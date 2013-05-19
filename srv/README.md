##π server


###Server setup

####Varnish
default.vcl
    #some settings

####Redis
redis.conf
    #some settings

####PHP
You need php >5.3 to run pi server

/etc/php5/apache2/php.ini
    ; settings for php 

/etc/php5/cli/php.ini
    ; settings for php running from command line



####Server-side optimizations
* Replace the PHP serializer with [igbinary](https://github.com/igbinary/igbinary).
* Compile Redis with  [ --enable-redis-igbinary ], to enable binary communication with Redis.
* Compile Redis as 32-bit, even on 64-bit systems. This is more memory-efficient.
* Use Redis for PHP session storage
* 


###Libraries
* [ApnsPHP: Apple Push Notification & Feedback Provider](https://github.com/duccio/ApnsPHP)


###Tools
* [git-flow - A collection of Git extensions to provide high-level repository operations for Vincent Driessen's branching model](https://github.com/nvie/gitflow)
* [Why aren't you using git-flow?](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/)
* [git-flow cheat sheet](http://danielkummer.github.io/git-flow-cheatsheet/)
* [mod_pagespeed - Apache module for automatic mobile optimization](https://developers.google.com/speed/pagespeed/mod)
* [mod_spdy - Apache SPDY module](http://code.google.com/p/mod-spdy/) -> [browser support](http://caniuse.com/spdy/)


### Extras
* [PhoneGap Push Notification Plugin for iOS and Android](https://github.com/phonegap-build/PushPlugin)







* [Varnish](http://varnish-cache.org)
* Apache
* PHP
    - [phpredis](https://github.com/nicolasff/phpredis) 
    - [igbinary](https://github.com/igbinary/igbinary) 
    - [phpws](http://code.google.com/p/phpws/)
* [Redis](http://redis.io)
* Free Pascal
    - [Bauglir2 WebSocket library](http://code.google.com/p/bauglir-websocket/)
    - [Object Pascal Redis client](https://github.com/ik5/redis_client.fpc)
    - [synapse](http://synapse.ararat.cz/doku.php/start)



####Kudos
Pi could not exist without Redis, an in-memory database with persistence to disk. 



###Inspirations
* [Salvatore Sanfilippo](http://antirez.com/), creator of [Redis](http://redis.io)
* The [GreenSock Animation Library](http://greensock.com)
* Higgins' PubSub
* J. Paul Morrison's [Flow Based Programming](http://www.jpaulmorrison.com/fbp/) - [Wikipædia](http://en.wikipedia.org/wiki/Flow-based_programming)


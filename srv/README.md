##π server


###Server setup

####Varnish
default.vcl

    backend default {
        .host = "127.0.0.1";
        .port = "8080";
    }

     sub vcl_recv {

         if(req.url ~ "^/[ve]/"){  //matches '/v/' or '/e/' at start of url

           // return http code 204 - "no content", best practice for gif beacon
          error 204;
         }
     }


    sub vcl_error{
         return (deliver);
     }

####Redis
redis.conf:

    # By default Redis does not run as a daemon. Use 'yes' if you need it.
    # Note that Redis will write a pid file in /var/run/redis.pid when daemonized.
    daemonize yes

    # When running daemonized, Redis writes a pid file in /var/run/redis.pid by
    # default. You can specify a custom pid file location here.
    pidfile /var/run/redis.pid
    
    ...
    
    # Specify the path for the unix socket that will be used to listen for
    # incoming connections. There is no default, so Redis will not listen
    # on a unix socket when not specified.
    #
    unixsocket /var/run/redis/redis.sock
    unixsocketperm 755




####PHP

You need php >5.3 to run pi server

/etc/php5/apache2/php.ini
    ; settings for php 
    extension=/usr/lib/php5/20100525/igbinary.so
    extension=/usr/lib/php5/20100525/redis.so

---

    [Session]

    ; Handler used to store/retrieve data.
    ; http://php.net/session.save-handler
    session.save_handler = redis

    ; http://php.net/session.save-path

    ; this sets up phpredis to connect over a tcp socket
    ; session.save_path = "tcp://localhost:6379?database=2"

    ; this uses unix sockets rather than tcp, which is 30% - 90% faster
    session.save_path = "unix:///var/run/redis/redis.sock?persistent=1&weight=1&database=2"

---



/etc/php5/cli/php.ini
    ; settings for php running from command line



####Server-side optimizations
* Replace the PHP serializer with [igbinary](https://github.com/igbinary/igbinary).
* Compile Redis with  [ --enable-redis-igbinary ], to enable binary communication with Redis.
* Compile Redis as 32-bit, even on 64-bit systems. This is more memory-efficient.
* Use Redis for PHP session storage




### Technologies

* [Varnish](http://varnish-cache.org)
* Apache
* PHP
    - [phpredis](https://github.com/nicolasff/phpredis) 
    - [igbinary](https://github.com/igbinary/igbinary) 
    - [phpws](http://code.google.com/p/phpws/)
* [MySQL](http://mysql.com)
* [Redis](http://redis.io)
* Free Pascal
    - [Bauglir2 WebSocket library](http://code.google.com/p/bauglir-websocket/)
    - [Object Pascal Redis client](https://github.com/ik5/redis_client.fpc)
    - [synapse](http://synapse.ararat.cz/doku.php/start)


###Tools
* [git-flow - A collection of Git extensions to provide high-level repository operations for Vincent Driessen's branching model](https://github.com/nvie/gitflow)
* [Why aren't you using git-flow?](http://jeffkreeftmeijer.com/2010/why-arent-you-using-git-flow/)
* [git-flow cheat sheet](http://danielkummer.github.io/git-flow-cheatsheet/)
* [mod_pagespeed - Apache module for automatic mobile optimization](https://developers.google.com/speed/pagespeed/mod)
* [mod_spdy - Apache SPDY module](http://code.google.com/p/mod-spdy/) -> [browser support](http://caniuse.com/spdy/)




####Kudos
Pi could not exist without Redis, an in-memory database with persistence to disk. 



###Inspirations
* [Salvatore Sanfilippo](http://antirez.com/), creator of [Redis](http://redis.io)
* The [GreenSock Animation Library](http://greensock.com)
* Higgins' PubSub
* J. Paul Morrison's [Flow Based Programming](http://www.jpaulmorrison.com/fbp/) - [Wikipædia](http://en.wikipedia.org/wiki/Flow-based_programming)


##π server


###Setup

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

/etc/php5/apache2/php.ini:

    ; settings for php 
    extension=redis.so
    extension=igbinary.so

    ...

    [Session]

    ; Handler used to store/retrieve data.
    ; http://php.net/session.save-handler
    session.save_handler = redis

    ; http://php.net/session.save-path

    ; this sets up phpredis to connect over a tcp socket
    ; session.save_path = "tcp://localhost:6379?database=2"

    ; this uses unix sockets rather than tcp, which is 30% - 90% faster
    session.save_path = "unix:///var/run/redis/redis.sock?persistent=1&weight=1&database=2"

    ...


    # Use igbinary as session serializer
    session.serialize_handler=igbinary

    # Enable or disable compacting of duplicate strings
    # The default is On.
    igbinary.compact_strings=On

    # Use igbinary as serializer in APC cache (3.1.7 or later)
    ;apc.serializer=igbinary


.. and in your php code replace serialize and unserialize function calls
with ``igbinary_serialize`` and ``igbinary_unserialize``.




/etc/php5/cli/php.ini

    ; settings for php running from command line
    ; same as for mod_php, mostly



Pi Channel


  channel:[var]| PI_ADDRESS


Built-in channels:

auth
sys

ctrl
admin
ping

type
db
zmq

log
error
debug
warning

push
chat


    [BASE]   = 0

    AUTH     = 1
    CHAT     = 2
    DEBUG    = 3
    WARNING  = 4
    ERROR    = 5
    LOG      = 6
    TYPE     = 7
    DB       = 8
    PING     = 9
    CTRL     = 10
    ADMIN    = 11
    SYS      = 12

    PUSH     = 14
    ZMQ      = 15





Access control:

Communication between channels can be blocked, either in one direction, or
in both directions

Channel permissions can be combined with other permissions, such as file permissions, user
permissions, app permissions, etc

A Channel mask can be stored as 4 bytes:  2 x uint16, with one bit per channel (for each direction)



examples 


zmq:8008|pi.app.test@129.45.12.101:8080

zmq:8008|pi.app.test@update.viewshq.no/


channel:chat|pi.user.5273

chat|pi.user.5273


db|pi.user  // read: list users from db, write: set params given for ALL users in db





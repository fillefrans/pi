#Web Service flow description

###This document outlines the flow of requests coming in via the Web Service.


*Web Service 
    Web Service will receive and return JSON data.
    An API key will be required to access Web Service

1. A request is received by the Web Service. 
  - check validity of input 
  - add number and parameters to report lines 
  - check whether number is already in cache 
    - yes: add to Redis queue for insertion into MySQL
    - no: add to Redis queue for retrieval of information
  - write request to webservice log
  - reply with status

  future features:


* Redis queue processor (PHP daemon)
  1. Connect to Redis and check queues every second.
  2. If queue not empty:
    - pop oldest element from queue and process
    - repeat until queue empty
    - disconnect from Redis
    - goto 1.



User workflow for using WebService:

1. Create a new report in Web App
2. Select the "Create JSON Web Service" option
3. Note the service id and service key
4. Read documentation for how to access the Web Service.
5. Optionally, register one or more hosts to accept requests from
  - if blank, service requires only valid service id and key for the service



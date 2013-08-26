



internal data types



pi routeable JSON packets



pi command packet

{
  command : [ session publish listen read write service run sysinfo fileinfo *unset ]
  address: "",
  replyto: "",
  scope: 2,
  data : {},

  [timestamp]
  [serialno]

}


pi data packet

{
  address: "",
  scope: 2,
  data : {}
}





pi REDIS data structures


queue


circular buffer


sorted set


bitmap




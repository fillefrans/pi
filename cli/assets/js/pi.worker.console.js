
var console = {
  log: function (msg, obj) {
    self.postMessage({event: "debug.log", debug: { log: {msg: msg, obj: obj || null }}});
  }
}

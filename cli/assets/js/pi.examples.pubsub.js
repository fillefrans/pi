  // ------
  // Use it
  // ------

  // Handle an event
  pi.events.subscribe("e", function(){
  	console.log("e!");
  });

  pi.events.publish("e"); // "e!"

  // Initialized with `true`?  You've got the path, as parsed, as the last parameter!
  pi.events.subscribe("nested.event", function(){
  	var path = arguments[arguments.length-1];
  	console.log("Path: " + path.join("."));
  });

  pi.events.publish("nested.event"); // "Path: nested.event"

  // Bubbles up the hierarchy, too!
  pi.events.publish("nested.event.even.deeper.yet!"); // "Path: nested.event.even.deeper.yet!"

  // Stop the bubbling at any time!
  pi.events.subscribe("nested.event.even", function() {
  	return false;
  });

  pi.events.publish("nested.event.even.deeper.yet!"); // nothing happens!

  // Global event listeners!
  pi.events.subscribe("", function(){
  	console.log("Event fired: " + arguments[arguments.length-1].join("."));
  });

  pi.events.publish("an.event"); // "Event fired: an.event"


  // Unsubscribe by handler!
  var handler = function(){
  	console.error("failed to remove");
  };

  pi.events.subscribe("remove.me", handler);
  pi.events.unsubscribe("remove.me", handler);
  pi.events.publish("remove.me") // nothing happens!

  // Unsubscribe by path!
  pi.events.subscribe("remove.me", handler);
  pi.events.unsubscribe("remove.me");
  pi.events.publish("remove.me") // nothing happens!

  // Unsubscribe recursively!
  pi.events.subscribe("remove.me", handler);
  pi.events.unsubscribe("remove", handler, true);
  pi.events.publish("remove.me") // nothing happens!

  // Unsubscribe everything!
  pi.events.subscribe("remove", handler).subscribe("another.hierarchy", handler);
  pi.events.unsubscribe("", true);
  pi.events.publish("remove").publish("another.hierarchy"); // nothing happens!




  // PVector implementation from ProcessingJS

  var PVector = function() {
    function PVector(x, y, z) {
      this.x = x || 0;
      this.y = y || 0;
      this.z = z || 0
    }
    PVector.dist = function(v1, v2) {
      return v1.dist(v2)
    };
    PVector.dot = function(v1, v2) {
      return v1.dot(v2)
    };
    PVector.cross = function(v1, v2) {
      return v1.cross(v2)
    };
    PVector.angleBetween = function(v1, v2) {
      return Math.acos(v1.dot(v2) / (v1.mag() * v2.mag()))
    };
    PVector.prototype = {
      set: function(v, y, z) {
        if (arguments.length === 1) this.set(v.x || v[0] || 0, v.y || v[1] || 0, v.z || v[2] || 0);
        else {
          this.x = v;
          this.y = y;
          this.z = z
        }
      },
      get: function() {
        return new PVector(this.x, this.y, this.z)
      },
      mag: function() {
        var x = this.x,
          y = this.y,
          z = this.z;
        return Math.sqrt(x * x + y * y + z * z)
      },
      add: function(v, y, z) {
        if (arguments.length === 1) {
          this.x += v.x;
          this.y += v.y;
          this.z += v.z
        } else {
          this.x += v;
          this.y += y;
          this.z += z
        }
      },
      sub: function(v, y, z) {
        if (arguments.length === 1) {
          this.x -= v.x;
          this.y -= v.y;
          this.z -= v.z
        } else {
          this.x -= v;
          this.y -= y;
          this.z -= z
        }
      },
      mult: function(v) {
        if (typeof v === "number") {
          this.x *= v;
          this.y *= v;
          this.z *= v
        } else {
          this.x *= v.x;
          this.y *= v.y;
          this.z *= v.z
        }
      },
      div: function(v) {
        if (typeof v === "number") {
          this.x /= v;
          this.y /= v;
          this.z /= v
        } else {
          this.x /= v.x;
          this.y /= v.y;
          this.z /= v.z
        }
      },
      dist: function(v) {
        var dx = this.x - v.x,
          dy = this.y - v.y,
          dz = this.z - v.z;
        return Math.sqrt(dx * dx + dy * dy + dz * dz)
      },
      dot: function(v, y, z) {
        if (arguments.length === 1) return this.x * v.x + this.y * v.y + this.z * v.z;
        return this.x * v + this.y * y + this.z * z
      },
      cross: function(v) {
        var x = this.x,
          y = this.y,
          z = this.z;
        return new PVector(y * v.z - v.y * z, z * v.x - v.z * x, x * v.y - v.x * y)
      },
      normalize: function() {
        var m = this.mag();
        if (m > 0) this.div(m)
      },
      limit: function(high) {
        if (this.mag() > high) {
          this.normalize();
          this.mult(high)
        }
      },
      heading2D: function() {
        return -Math.atan2(-this.y, this.x)
      },
      toString: function() {
        return "[" + this.x + ", " + this.y + ", " + this.z + "]"
      },
      array: function() {
        return [this.x, this.y, this.z]
      }
    };

    function createPVectorMethod(method) {
      return function(v1, v2) {
        var v = v1.get();
        v[method](v2);
        return v
      }
    }
    for (var method in PVector.prototype) if (PVector.prototype.hasOwnProperty(method) && !PVector.hasOwnProperty(method)) PVector[method] = createPVectorMethod(method);
    return PVector
  }();



// A simple swarming algorithm
// (c) Alasdair Turner 2008

var swarm = new Array(200);


function setup() {
  size(400,400);
  colorMode(HSB);

  for (var i = 0, count = swarm.length; i < count; i++) {
    swarm[i] = new Fly();
  }
}

function draw() {
  background(0);
  for (int i = 0; i < swarm.length; i++) {
    swarm[i].move(swarm);
    swarm[i].draw();
  }
}




Fly = {

  direction: new PVector(),
  position:  new PVector(),
  speed:     null,
  colour:    null,
  that:      this,

  init: function() {
    // random location
    this.position = new PVector(random(0,width),random(0,height));
    // random direction
    direction = new PVector(random(-1,1),random(-1,1));
    direction.normalize();
    // random speed
    speed = random(1,2);
    // random HSB color
    colour = color(random(0,64),64,64);
  },

  move: function(swarm) {

    // fly to the centre of the swarm...
    centre = new PVector(0,0);

    for (var i = 0, fly_count = swarm.length; i < fly_count; i++) {
      centre.add(swarm[i].position);
    }
    centre.mult(1.0/swarm.length);

    // ...but avoid getting too close to other flies
    var closest = -1;
    var closestdist = width + height;
    for (int i = 0; i < swarm.length; i++) {
      float d = PVector.dist(swarm[i].position,position);
      if (swarm[i] != this && d < closestdist) {
        closest = i;
        closestdist = d;
      }
    }
    // now implement: only fly to the centre if you are not too close
    if (closestdist > 10) {
      PVector centredir = PVector.sub(centre,position);
      centredir.normalize();
      // steer towards the centre, here using simple addition:
      direction.add(centredir);
    }
    else {
      // otherwise avoid other flies!
      PVector closestdir = PVector.sub(swarm[closest].position,position);
      closestdir.normalize();
      // steer away from the closest fly, here using simple subtraction:
      direction.sub(closestdir);
    }
    // normalise the combination of other operations
    direction.normalize();
    direction.mult(speed);
    position.add( direction );
    // constrain to screen:
    position.x = (position.x + width) % width;
    position.y = (position.y + height) % height;
  }
  void draw()
  {
    stroke(colour);
    pushMatrix();
    translate(position.x,position.y);
    line(0,0,direction.x*2,direction.y*2);
    popMatrix();
  }
}


$(document).ready(function() {
    var myAccelerometer  = [0, 0, 0];
    var kFilteringFactor = 0.6;
    var leftTriggered    = false;
    var rightTriggered   = false;

    var audio = [$("#audio1")[0], $("#audio2")[0], $("#audio3")[0], $("#audio4")[0]];

    for (var i = 0; i < audio.length; i++) {
      audio[i].load();
    }

    window.ondevicemotion = function(event) {
        var acceleration = new Array();

        acceleration['x'] = event.accelerationIncludingGravity.x;
        acceleration['y'] = event.accelerationIncludingGravity.y;
        acceleration['z'] = event.accelerationIncludingGravity.z;

        // Use a basic high-pass filter to remove the influence of the gravity
        myAccelerometer[0] = acceleration.x * kFilteringFactor + myAccelerometer[0] * (1.0 - kFilteringFactor);
        myAccelerometer[1] = acceleration.y * kFilteringFactor + myAccelerometer[1] * (1.0 - kFilteringFactor);
        myAccelerometer[2] = acceleration.z * kFilteringFactor + myAccelerometer[2] * (1.0 - kFilteringFactor);

        // Compute values for the three axes of the acceleromater
        var x = acceleration.x - myAccelerometer[0];
        var y = acceleration.y - myAccelerometer[0];
        var z = acceleration.z - myAccelerometer[0];

        // Compute the intensity of the current acceleration
        var length = Math.sqrt(x * x + y * y + z * z);

        if (length > kFilteringFactor) {
            $('#bjelle').css('-webkit-transform', 'rotate3d(0, 0, 1, ' + -acceleration.x *3.14+ 'deg)');
            $('#bjelle').css('-moz-transform', 'rotate3d(0, 0, 1, ' + -acceleration.x *3.14+ 'deg)');

            if (-acceleration.x > 0.5 && !leftTriggered) {
                leftTriggered  = true;
                rightTriggered = false;

                var rand = Math.floor(Math.random()*3);
                audio[rand].play();
            }
            else if (-acceleration.x < -0.5 && !rightTriggered) {
                rightTriggered = true;
                leftTriggered  = false;

                var rand = Math.floor(Math.random()*3);
                audio[rand].play();
            }
            else if (-acceleration.x > -0.5 && -acceleration.x < 0.5) {
                leftTriggered  = false;
                rightTriggered = false;
            }
        }
    }

    $('#bjelle').click(function() {
        var rand = Math.floor(Math.random()*3);
        audio[rand].play();
    });

});


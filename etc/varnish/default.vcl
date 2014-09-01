vcl 4.0;
backend default {
    .host = "127.0.0.1";
    .port = "8080";
}

sub vcl_recv {
     
     if (req.url ~ "^/__pi/") {  
      return(synth(204, "No content"));
     }
 }

 


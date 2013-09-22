<?php

  require_once( __DIR__ . "/../../srv/php/pi.php");



  class PiFileUpload extends Pi {

    protected $id       = null;
    protected $name     = 'pi.io.file.upload';
    protected $address  = 'file.pi.io.file.upload.';



    private $allowedExts  = array("gif", "jpeg", "jpg", "png");
    private $temp         = explode(".", $_FILES["file"]["name"]);
    private $extension    = end($temp);


    public function __construct($allowedExts=false) {
      if( is_array($allowedExts) && (count($allowedExts) > 0) ) {
        $this->allowedExts = $allowedExts;
      }
    }


    public function __init() {
      $this->id = uniqid();
      $this->address .= $this->id;
    }



   if ((($_FILES["file"]["type"] == "image/gif")
   || ($_FILES["file"]["type"] == "image/jpeg")
   || ($_FILES["file"]["type"] == "image/jpg")
  || ($_FILES["file"]["type"] == "image/pjpeg")
  || ($_FILES["file"]
      ["type"] == "image/x-png")
   || ($_FILES["file"]["type"] == "image/png"))
   && ($_FILES["file"]["size"] < 20000)
   && in_array($extension, $allowedExts))
     {
     if ($_FILES["file"]["error"] > 0)
       {
       echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
       }
     else
       {
       echo "Upload: " . $_FILES["file"]["name"] . "<br>";
       echo "Type: " . $_FILES["file"]["type"] . "<br>";
       echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
       echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

       if (file_exists("upload/" . $_FILES["file"]["name"]))
         {
         echo $_FILES["file"]["name"] . " already exists. ";
         }
       else
         {
         move_uploaded_file($_FILES["file"]["tmp_name"],
         "upload/" . $_FILES["file"]["name"]);
         echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
         }
       }
     }
   else
     {
     echo "Invalid file";
     }


     public function run() {
      if ($this->__init()) {

      }
     }


  }


  $receiver = new PiFileUpload();

  try {
    $receiver->run();
  }
  catch($e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }



 ?>
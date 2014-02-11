<?php


session_start();


  require_once( __DIR__ . "/../../srv/php/pi.php");



  class PiFileUpload extends Pi {

    protected $id       = null;
    protected $name     = 'pi.io.file.upload';
    protected $address  = 'file.pi.io.file.upload.';
    protected $event = 'event.pi.io.file.upload.';


    private $key = ini_get("session.upload_progress.prefix") . $_POST[ini_get("session.upload_progress.name")];


    private $files        = array();
    private $allowedExts  = array("gif", "jpeg", "jpg", "png");
    private $temp         = explode(".", $_FILES["file"]["name"]);
    private $extension    = end($temp);


    public function __construct($allowedExts=false) {
      if( is_array($allowedExts) && (count($allowedExts) > 0) ) {
        $this->allowedExts = $allowedExts;
      }
    }


    public function __init() {
      $this->id        = uniqid();
      $this->address  .= $this->id;
      $this->event    .= $this->id . ".";

      return true;
    }


    private function normalize() {
      $files = array();
      $count = count($_FILES[$this->name]);
      $keys  = array_keys($_FILES[$this->name]);

      for ($i=0; $i<$count; $i++) {
        foreach ($keys as $key) {
          $files[$i][$key] = $post[$key][$i];
        }
      }

      return $files;
    }


    protected function onTick() {
      $this->pubsub->publish( $this->event . "progress", json_encode($this->normalize()) );
    }


    private function receiveFiles () {

      ini_set("session.upload_progress.freq", $_FILES["file"]["size"])

      if ($_FILES["file"]["error"] > 0) {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
      }
      else {
        echo "Upload: " . $_FILES["file"]["name"] . "<br>";
        echo "Type: " . $_FILES["file"]["type"] . "<br>";
        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
        echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

        if (file_exists("upload/" . $_FILES["file"]["name"])) {
          echo $_FILES["file"]["name"] . " already exists. ";
        }
        else {
          move_uploaded_file($_FILES["file"]["tmp_name"],
          "upload/" . $_FILES["file"]["name"]);
          echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
        }
      }
    
      // 
      $this->redis->set($this->address, json_encode($_FILES));

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
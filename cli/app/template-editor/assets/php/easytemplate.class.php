<?php


  class EasyTemplate {

    

    private   $filename = "";
    private   $basename = "";
    private   $longname = "";

    private   $jsonfile = "";

    private   $rendered = "";
    private   $data     = array();

    private   $defaults = array();

    private   $width    = 0;
    private   $height   = 0;

    // the raw template string
    public    $raw      = "";

    public    $log      = [];

    public function __construct ($filename, $raw=null) {


      if($raw === null && !file_exists($filename)) {
        return;
      }


      $this->filename = $filename;
      $this->longname = str_replace(".html", "", self::folderToKey($filename));
      $this->jsonfile = str_replace(".html", ".json", $filename);
      $this->basename = basename($filename, ".html");


      if (file_exists($this->jsonfile) && filesize($this->jsonfile) ) {
        $this->defaults = json_decode(file_get_contents($this->jsonfile), true);
        $this->defaults[$this->longname] = json_decode(file_get_contents($this->jsonfile), true);
      }
      else {
        $this->jsonfile = "../../" . str_replace(basename($this->filename), "defaults.json", $this->filename);
  
        if (file_exists($this->jsonfile) && filesize($this->jsonfile) ) {
          $this->log[] = "OK: " . $this->jsonfile;
          $this->defaults = json_decode(file_get_contents($this->jsonfile), true);
          $this->defaults[$this->longname] = json_decode(file_get_contents($this->jsonfile), true);
          // $this->log[] = "loaded defaults : " . json_encode($this->defaults);
        }
        else {
          $this->log[] = "NOPE: " . $this->jsonfile;
        }
      }


      if ($raw) {
        $this->raw = $raw;
      }
      else {
        $this->raw = file_get_contents($filename);
      }

      $this->init();

    }


    public static function folderToKey ($folder) {
      $result = "";
      $charsToDelete = 0;

      if ( strpos($folder, ".") === 0 ) {
        $charsToDelete++;
      }
      if ( strpos($folder, "/") <= 1 ) {
        $charsToDelete++;
      }

      return str_replace("/", "-", substr($folder, $charsToDelete));

    }


    private function init () {
      $adSize = self::getSizeFromFilename($this->basename);

      $this->width  = $adSize['w'];
      $this->height = $adSize['h'];

      if(count($adSize) >= 2) {
        $this->data['adwidth']  = $adSize['w'];
        $this->data['adheight'] = $adSize['h'];
      } 

      if(!isset($this->data['imagesrc'])) {
        // default image
        $this->data['imagesrc'] = "assets/images/test.jpg";
      }
      if(!isset($this->data['custom4'])) {
        // default image
        $this->data['custom4'] = "assets/images/test.jpg";
      }

    }





    /**
     *    utility functions
     * 
     */

    private static function getSizeFromFilename ($filename) {
      if( strpos($filename, 'x') === false ) {
        return null;
      }

      $size = array();

      $nameParts = explode("x", $filename);

      $size['w'] = intval($nameParts[0], 10);
      $size['h'] = intval($nameParts[1], 10);

      return $size;
    }


    private static function toRegex ($keyArray) {

      if(!is_array($keyArray)) {
        return null;
      }

      $i = count($keyArray)-1;

      while($i >= 0) {
        // regex to match {key*}
        $keyArray[$i] = "/\{(" . $keyArray[$i] . ")([^}]*)\}/";
        $i--;
      }
      return $keyArray;
    }



    public function render ($contents = null, $showsource = false, $toString = false) {

      $this->rendered = "";

      if($contents && is_array($contents)) {

        foreach ($this->defaults as $key => $value) {
          if(!isset($this->data[$key]) && (!is_numeric($key))) {
            $this->data[$key] = $value;
          }
        }
        // $contents = array_merge($this->defaults, $contents);

        foreach ($contents as $key => $value) {
          $this->data[$key] = $value;
        }
      }


      $keys   = self::toRegex(array_keys($this->data));
      $values = array_values($this->data);

      $this->rendered = @preg_replace($keys, $values, $this->raw);


      if($toString === true) {
        return $this->rendered;
      }
      else {
        print("<p /><a name='{$this->longname}' href='#{$this->longname}'>{$this->basename}</a>\n");
        print($this->rendered);
      }

      if($showsource) {
        $this->showSource();
      }

    }


    public function showSource ($toString = false) {

      if($toString === true) {
        return $this->raw;
      }
      else {
        print(
              "<div style='width:{$this->width}px;text-align:center;'><a name='#{$this->longname}-source' href='#{$this->longname}-source'>source</a>" . 
              "</div>" . '<textarea id="'. $this->longname .'-source" class="sourcecode">' . $this->raw . '</textarea>' .
              "<div id='". $this->longname ."-editor'></div>"
              );
      }
    }

  }


?>
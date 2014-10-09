<?php

  require_once('template.class.php');


  /**
   *  TemplateList
   *
   * list templates in folder + subfolder
   *
   * render as json
   *
   * 
   */



  class TemplateList {

    private   $rootfolder = "";
    private   $subfolders = array();

    private   $start  = 0;
    private   $timers = array();
    private   $log    = array();



    // array containing all the listed templates
    public  $items = array();

    public  $defaults = array();

    public function __construct ($rootfolder, $recursive = false) {

      $this->start = microtime(true);
      if ( !file_exists($rootfolder) || !is_dir($rootfolder) ) {
        return;
      }

      $this->rootfolder = $rootfolder;

      $this->init();

      $this->timers['ready'] = microtime(true);
    }


    public function showTimers () {
      $now = microtime(true);
      print($this->start . "\n");
      foreach ($this->timers as $key => $value) {
        print("\t$key : " . (1000*($value - $this->start) . "\n"));
      }
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


    private function debug ($str) {
      $this->log[] =  $str;
    }

    private function init () {


      $this->timers['init'] = microtime(true);


      $this->subfolders = glob($this->rootfolder . "/*", GLOB_ONLYDIR);

      if ( count($this->subfolders) === 0 ) {
        $this->log[] =  "No folders found under root : " . $this->rootfolder;
        return false;
      }

      // print ("<pre>\n");

      foreach ( $this->subfolders as $folder ) {
        if ($folder === "./assets") {
          $this->log[] =  "Skipping : $folder";
          continue;
        }

        if (file_exists($folder."/defaults.json") && filesize($folder."/defaults.json")) {
          $this->log[] =  "Adding to defaults['" . TemplateList::folderToKey($folder) . "'] : $folder/defaults.json";
          $this->defaults[TemplateList::folderToKey($folder)] = json_decode(file_get_contents($folder."/defaults.json"), true);
        }

        // $key = TemplateList::folderToKey($folder);
        $this->log[] =  "Entering folder : $folder";
        $this->items[TemplateList::folderToKey($folder)] = TemplateList::readFolder($folder);
      }


      // print ("</pre>\n");

    }


    private function readFolder ($folder) {

      $this->log[] =  "Now in readfolder() : $folder";

      $templates = array();

      // create a list of all "[nn]x[nn].html" files in current directory
      if ($handle = opendir($folder)) {
        while (false !== ($entry = readdir($handle))) {

          if ( $entry === "." || $entry === ".." ) {
            continue;
          }



          $entrypath = "$folder/$entry";


          if(is_dir($entrypath)) {

            $this->log[] =  "found new subfolder : $entrypath";

            if (file_exists($entrypath."/defaults.json") ) {
              $this->log[] =  "Found defaults.json in $entrypath/defaults.json";
              $this->log[] =  "Adding to defaults['" . TemplateList::folderToKey($entrypath) . "'] : $folder/defaults.json";
              $this->defaults[TemplateList::folderToKey($entrypath)] = json_decode(file_get_contents($entrypath."/defaults.json"), true);
            }

            $this->log[] =  "Entering subfolder : $entrypath";
            $templates[TemplateList::folderToKey($entrypath)] = $this->readFolder($entrypath);
            // foreach ($templategroup as $template) {
            //   print ("\ttemplate : " . $template . "\n");
            // }
            continue;
          }



          if( (strpos($entry, '.html') !== false) && filesize($entrypath) ) {
            $name = preg_replace("/.html/", "", $entry);

            $entryname = "$folder/$name";

            $this->log[] =  "Found new template : $entryname";
            $templates[$name] = new Template($entrypath);

            // print ( "\t" . TemplateList::folderToKey($entryname) . "\t => " . $entrypath . "\n");

          }
          else {
            if( (strpos($entry, '.json') !== false) && filesize($entrypath) ) {
              $name = preg_replace("/.json/", "", $entry);
              if($name == "defaults") {

                continue;
              }
              $longname = TemplateList::folderToKey(preg_replace("/.json/", "", $entrypath));
              $this->defaults[$longname] = json_decode(file_get_contents($entrypath), true);
              // print ( "\t" . TemplateList::folderToKey($entryname) . "\t => " . $entrypath . "\n");
            }

          }
        }
        closedir($handle);
      }
      else {
        $this->log[] =  "Error - Unable to open dir : $folder";
      }

      $this->log[] =  "Sorting " . count($templates) . " entries";
      ksort($templates);

      if (count ($templates) <= 2 )
        $this->log[] =  htmlspecialchars(print_r(array_keys($templates), true));

      return $templates;
    }



    public function toJSON () {

      $content  = array();
      $items    = array();

      $this->log[] =  "Starting render loop";

      foreach ($this->items as $site => $customers) {

        $this->log[] =  "Processing site $site : " . count($customers) . " customers";
        $items[$site] = array();

        foreach ($customers as $customer => $formats) {
          
          if(is_array($formats)) {
            $this->log[] =  "Processing customer $customer : " . count($formats) . " formats";

            $items[$site][$customer] = array();

            foreach ($formats as $format => $template) {
              if($template instanceof Template) {
                // $template->render($defaults, $showsource);
                $items[$site][$customer][$format] =  array( 'filename' => $template->getFilename(), 'template' => ($template->raw));
                // $items[$site][$customer][$format] =  base64_encode($template->raw);
              }
              else {
                $this->log[] =  "Error - NOT A TEMPLATE : $format";
              }
            }
          }
          else {
            if($formats instanceof Template) {
              $this->log[] =  "Processing template from site dir (?) : ";

              $items[$site][$customer] = array( 'filename' => $template->filename, 'template' => ($template->raw));
              // $formats->render($defaults, $showsource);
              // $template->showSource();
            }
            else {
              $this->log[] =  "Error (in site dir ?) - NOT A TEMPLATE : $formats";
            }
          }
        }
      }


      $content['items']     = $items;

      $content['defaults']  = $this->defaults;
      $content['log']       = $this->log;
      $content['timers']    = $this->timers;

      // $content['vardump'] = htmlspecialchars(print_r($content, true));

      return json_encode($content, JSON_PRETTY_PRINT);


    }




    public function render ($defaults = null, $showsource = false) {

      $this->log[] =  "Starting render loop";

      foreach ($this->items as $site => $customers) {

        $this->log[] =  "Processing site $site : " . count($customers) . " customers";
        print ("<p onclick='toggleVisible(\"$site\")'>$site</p><div id='$site' class='site'>\n");

        foreach ($customers as $customer => $formats) {
          print ("<p onclick='toggleVisible(\"$customer\")'>$customer</p><div id='$customer' class='customer'>\n");

          if(is_array($formats)) {
            $this->log[] =  "Processing customer $customer : " . count($formats) . " formats";

            foreach ($formats as $format => $template) {
              if($template instanceof Template) {
                $template->render($defaults, $showsource);
              }
              else {
                $this->log[] =  "Error - NOT A TEMPLATE : $format";
              }
            }
          }
          else {
            if($formats instanceof Template) {
              $this->log[] =  "Processing template from site dir (?) : ";


              print("<p /><a name='$customer' href='#$customer'>$customer</a>\n");
              $formats->render($defaults, $showsource);
              // $template->showSource();
            }
            else {
              $this->log[] =  "Error (in site dir ?) - NOT A TEMPLATE : $formats";
            }
          }
          print ("</div>\n"); // closes the customer tag
        }
        print ("</div>\n"); // closes the site tag
      }

      print("<pre></pre>\n");

      print("<pre style='background : #272822'>\n\n\n    DEFAULTS\n\n");
      print(json_encode($this->defaults, JSON_PRETTY_PRINT));
      print("\n\n</pre>\n");

      print("<pre>\n");
      foreach ($this->log as $lineno => $line) {
        print ("$line\n");
      }

      print("\nTimers:\n");

      $this->showTimers();
      print("</pre>\n");


    }

  }


?>
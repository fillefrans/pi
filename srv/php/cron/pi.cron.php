<?php

    /**
     * π.cron
     * 
     * Base class for Cron scripts
     *
     * This script is part of the pi server
     *
     * @author Johan Telstad, jt@viewshq.no, 2011-2014
     *
     */

    require_once( __DIR__ . "/../pi.php");

    class PiCronException extends PiException {};

    class PiCron extends Pi {

        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }


        private function quit($msg="Goodbye. No message.") {

          die($msg);
        }


        /**
         * Include all .php script files found in $path
         * 
         * @param  [string]   $path The path to search
         * 
         * @return [int|bool]       Number of files included, or boolean False on error.
         * 
         * @throws PiCronException  If path does not exist, and could not be created.
         */
        protected function includeScripts($path) {
          if (!file_exists($path)) {
            print("mkdir: '$path'\n");
            return mkdir($path);
          }
          if (!is_dir($path)) {
            throw new PiCronException("$path don't exist, or already exists and is not a directory!", 1);
          }

          $scripts = glob("$path/*.php");
          
          foreach ($scripts as $script) {
            print("including cron script : " . $script ."\n");
            include($script);
          }
          return count($scripts);


        }



        public function run($dbg=false){

          // $this->__init();
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");

        }

    }


?>
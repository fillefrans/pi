<?php

namespace Psmith;


define('DEBUG', true);


$request = file_get_contents("php://input");


/**
 * The AclController class will implement Access Control Lists.
 *
 * The constructor of the AclController class takes a User as
 * parameter, and provides methods for checking access privileges
 * 
 * 
 *
 * @category  Pi
 * @package   ACL
 * @example   ../index.php
 * @example 
 *  $user = new User();
 *  $user->login();
 *  $aclController = new AclController($user);
 *  if($aclController->hasAccess(__NAMESPACE__)) {
 *    // etc
 *  }
 *  
 *  $aAllEmails = $oUser->getEmails();
 *  $oUser->addEmail('test@test.com');
 * @version   0.01
 * @since     2014-02-25
 * @author    Johan Telstad <jt@enfield.no>
 * @copyright 2011-2014 Views AS
 * 
 */

abstract class AclController {


  /**
   * The user's access level. Defaults to 0.
   * @var int
   */
  
  private $accesslevel;

  /**
   * The user's access level. Defaults to 0.
   * @var string
   */
  private $group;

  abstract public function __construct($user);



}



/**
 * @interface Rest
 * 
 */

interface Rest {

  public $response;

  private $request;
  private $aclController;



  /**
   * Creates a new object in the database. 
   * 
   *
   * @param   object|array $object The object or objects to create
   *
   * @return  int|boolean   Number of objects created, or boolean false on error
   * @throws  InvalidArgumentException
   * @todo    Check access rights with AclController
   *
   * @author  Johan Telstad <jt@enfield.no>
   *
   */
  public function create($object);



  /**
   * Reads one or more objects from the database. 
   * 
   *
   * @param   int|array $ids The id or ids to read from database
   *
   * @return  array|null|boolean  Array containing result if found, null if not found. Returns boolean false on error;
   * @throws  InvalidArgumentException
   * @todo    Check access rights with AclController
   *
   * @author  Johan Telstad <jt@enfield.no>
   *
   */
  public function read  ($ids);


  /**
   * Updates one or more objects in the database. 
   * 
   *
   * @param   object|array $objects The object or objects to update
   *
   * @return  int|array|boolean Boolean false on error.
   * @throws  InvalidArgumentException
   * @todo    Check access rights with AclController
   *
   * @author  Johan Telstad <jt@enfield.no>
   *
   */
  public function update($objects);


  /**
   * Deletes one or more objects from the database. 
   * 
   *
   * @param   object|array $ids The object or objects to delete
   *
   * @return  int
   * @throws  InvalidArgumentException
   * @todo    Check access rights with AclController
   *
   * @author  Johan Telstad <jt@enfield.no>
   *
   */
  public function delete($ids);

  private function verifyRequest();

}




/**
 * The API class is the class that provides API services for Pi
 *
 * 
 *
 * @category  Pi
 * @package   API
 * @example   ../index.php
 * @example 
 *  $user = new User();
 *  $user->login();
 *  $aclController = new AclController($user);
 *  if($aclController->hasAccess(__NAMESPACE__)) {
 *    // etc
 *  }
 *  
 *  $aAllEmails = $oUser->getEmails();
 *  $oUser->addEmail('test@test.com');
 * @version   0.01
 * @since     2014-02-25
 * @author    Johan Telstad <jt@enfield.no>
 * @copyright 2011-2014 Views AS
 * 
 */


class Api {


  private $request = array();
  private $response = array();


  public function __construct() {

  }



  protected function readRequest() {
    return file_get_contents('php://input');
  }

  protected function processRequest($request) {

    // $requestProcessor = $this->registry[];

  }


  private function getRequestProcessor($name) {
    if(isset(($this->registry[$name]))) {
      return $this->registry[$name]->create();
    }
  }



  private function init() {
    $this->registry['overview'] = new stdClass();
    $this->registry['overview']->create = function() {
        return new OverviewRequestProcessor();
      };
      
  }

  public function run() {
    processRequest($this->readRequest());
  }

}




$query = "
  SELECT *, SUM(sublime.seconds) as totaltid 
  FROM `tracs-sublime` sublime
  JOIN `tracs-files` file ON file.id = sublime.file_id
  WHERE 1 
  GROUP BY project_id, file_id 
  ORDER BY project_id, totaltid DESC;";


?>
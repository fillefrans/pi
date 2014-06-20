<?

  /**
   *  Pi ACL class
   *
   *  Implements basic ACL for the Pi namespace.
   *
   *
   * @author 2011-2014 Johan Telstad <jt@enfield.no>
   * 
   */



  require_once('pi.db.php');



  class PiACL {

    private   $name       = 'acl';
    private   $userEmpty  = false;


    private   $permissionCache = array('user'=> array(), 'group' => array());


    public function __construct() {
      $this->db = new PiDB();
    }


    public function check ($permission, $userid, $group_id) {

      // check user permissions first
      if (!$this->user_permissions($permission, $userid)) {
        $this->permissionCache['user'][$userid] = array($permission => false);
        return false;
      }

      if (!$this->group_permissions($permission, $group_id) & $this->isUserEmpty()) {
        $this->permissionCache['group'][$group_id] = array($permission => false);
        return false;
      }

      $this->permissionCache['user'][$userid] = array($permission => true);
      $this->permissionCache['group'][$group_id] = array($permission => true);
      return true;
    }



    private function user_permissions ($permission, $userid) {
      
      $this->db->query("SELECT COUNT(*) AS count FROM user_permissions WHERE permission_name='$permission' AND user_id='$userid' ");

      $row = $this->db->fetch();

      if ($row['count'] > 0) {
        $this->db->query("SELECT * FROM user_permissions WHERE permission_name='$permission' AND user_id='$userid' ");

        $row = $this->db->fetch();

        if ($row['permission_type'] == 0) {
          return false;
        }
      
      return true;
     }

     $this->setUserEmpty(true);
     return true;
     
    }



    private function group_permissions($permission, $group_id) {
      
      $this->db->query("SELECT COUNT(*) AS count FROM group_permissions WHERE permission_name='$permission' AND group_id='$group_id' ");

      $row = $this->db->fetch();

      if ($row['count'] > 0) {
        $this->db->query("SELECT * FROM group_permissions WHERE permission_name='$permission' AND group_id='$group_id' ");

        $row = $this->db->fetch();

        if ($row['permission_type'] == 0) {
          return false;
        }
        return true;
      }
      return true;
    }



    private function setUserEmpty($val) {
      $this->userEmpty = $val;
    }



    private function isUserEmpty() {
      return $this->userEmpty;
    }

  }


?>
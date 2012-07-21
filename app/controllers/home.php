<?php
/**
 *  @author Adrian Soluch adrian@soluch.at
 */

include('app/classes/controller.php');

class Home extends Controller {

/**
 *  Method 'allowed' stores all the Methods of 
 *  this Class which are allowed to be accessed through REST
 */
    //public function allowed() {

        //$this->allowed = array('test','index','login','logout','register');
    //}

/**
 *  Method 'authExceptions' stores the Methods where the user doesn't need to 
 *  be logged in to access through REST
 *  (Automaticly redirection to login if not in list)
 */
    public function authExceptions()
    {
        $this->authExceptions = array('index');
    }

    /**
     *  default method
     */
    public function index()
    {
        if( isset($_SESSION['__sessiondata']['loggedin']) ) {

            $this->initDB();
            try {
                $stmt = $this->DB->prepare("SELECT id, service, username, image, active, created FROM socialaccounts WHERE user_id = :userid AND service = 'twitter'");
                $stmt->execute(
                    array(
                        ':userid' => $_SESSION['__sessiondata']['user_id']
                    )
                );
                $this->services = $stmt->fetchAll();
            }
            catch (Exception $e) {

                dumper($e->getMessage());
                return FALSE;
            }
        }
        //dumper($_SERVER);
    }
}

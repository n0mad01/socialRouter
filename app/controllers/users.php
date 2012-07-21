<?php
/**
 *  @author Adrian Soluch adrian@soluch.at
 */

include('app/classes/controller.php');

class Users extends Controller {

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
    public function authExceptions() {

        $this->authExceptions = array('index','login','logout','register');
    }

    /**
     *  default method
     */
    public function index() {

        if($this->isLoggedIn()) {
            $this->accounts = $this->getUsersSocialAccounts( $_SESSION['__sessiondata']['user_id'] );
            //dumper($ret);
        } else {

        }
    }
    
    /**
     *  User login  / Authentication
     */
    public function login()
    {
        if(isset($this->postdata)) {

            $email = $this->postdata['email'];
            $fromDB = $this->getUserByEmail($email);

            if(empty($fromDB)) {

                $ret['invalid']['notfound'] = _('The user/password combination is wrong!');
                return $ret;
            } else {

                $pass = $this->hashPassword($this->postdata['password']);

                // login correct
                if($pass === $fromDB['password']) {

                    $token = $this->generateToken($email);
                    //dumper($fromDB);die();

                    $this->session->email = $email;
                    $this->session->user_id = $fromDB['id'];
                    $this->session->loggedin = TRUE;
                    $this->session->logintoken = $token;

                    // Save token in Cookie // 1500 days
                    setcookie('logintoken', $token, time()+(3600*24*1500), '/', $_SERVER['HTTP_HOST']);

                    $this->initDB();
                    try {
                        $sql = "INSERT INTO logintokens (user_id, token, created, refreshed) VALUES (:userid, :token, now(), now())";
                        $pdoparams = array(
                            ':userid' => $fromDB['id'],
                            ':token' => $token
                        );
                        $stmt = $this->DB->prepare($sql);
                        $stmt->execute($pdoparams);

                    }
                    catch (Exception $e) {

                        dumper($e->getMessage());
                        return FALSE;
                    }

                    // redirect
                    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/');
                
                }
            }
        }
    }

/**
 *  User logout 
 */
    public function logout() {

        // destroy $_SESSION 
        $this->session->destroy();

        // unset cookies
        setcookie("logintoken", '', time()+3600*24*1500, '/', $_SERVER['HTTP_HOST']);
        unset($_COOKIE['logintoken']);

        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/');
    }

    /**
     *  New User registration
     *  @return     Boolean
     */
    public function register() {

        $valid = TRUE;
        $ret = array(); // returning errormessage
        if(isset($this->postdata)) {

            if(isset($this->postdata['businessTerms']) && $this->postdata['businessTerms'] === 'on') {
                //$valid = TRUE;
            }
            else {
                $ret['invalid']['businessTerms'] = _('You must agree to the terms of use in order to proceed!');
                $valid = FALSE;
            }

            if($this->validEmail($this->postdata['email'])) {
                //$valid = TRUE;
            }
            else {
                $ret['invalid']['email'] = _('Email address you\'ve entered is invalid, please try again!');
                $valid = FALSE;
            }
            if($this->getUserByEmail($this->postdata['email'])) {
                $ret['invalid']['email'] = _('This Email address is already used!');
                $valid = FALSE;
            }
            else {
                //$valid = TRUE;
            }

            if($this->validPasswords($this->postdata['password'], $this->postdata['password2']))
            {
                //echo 'PASSWORDS OK!';
            } else {
                $ret['invalid']['passwords'] = _('The Passwords must be at least 6 characters long and both the same!');
                $valid = FALSE;
            }

            if($valid) {
                $ret = $this->saveUser($this->postdata);
            }
        }
        return $ret;
    }

    /**
     *  save the freshly registered user
     *
     *  @param  Array()
     */
    private function saveUser($user)
    {
        $fromDB = $this->getUserByEmail($user['email']);
        if(empty($fromDB)) {

            // set vars to save
            unset($user['password2']);
            $user['password'] = $this->hashPassword($user['password']);
            //$user['lastlogin'] = date('Y-m-d');
            //$user['modified'] = date('Y-m-d');

            // save new user
            try {
                $this->initDB();

                $pdoparams = array(
                    ':email' => $user['email'],
                    ':password' => $user['password']
                );
                $sql = "INSERT INTO users (email, password, created, modified) VALUES (:email, :password, now(), now())";
                $stmt = $this->DB->prepare($sql);
                $stmt->execute($pdoparams);
                $userID = $this->DB->lastInsertId();

            } catch (Exception $e) {

                dumper($e->getMessage());
                return FALSE;
            }
            // redirect
            //header('Location: http://' . $_SERVER['HTTP_HOST'] . '/users/login/');
            //$this->postdata['email'] = $user['email'];
            //$this->postdata['password'] = $user['password'];

            $this->login();
            
            
            //return TRUE;
        } else {

            $ret['invalid']['email'] = _('This Email address is already used!');
            return $ret;
        }
    }

/**
 *  lookup if email address already exists in db
 */
    private function getUserByEmail($email) {

        $this->initDB();
        try {

            $result = '';
            $stmt = $this->DB->prepare("SELECT * FROM users where email = ?");
            if ($stmt->execute(array($email))) {

                $result = $stmt->fetch();
            }

        } catch (Exception $e) {
            dumper($e->getMessage());
            return FALSE;
        }
        return $result;
    }

    private function getUsersSocialAccounts($id)
    {
        $this->initDB();

        try {
            $stmt = $this->DB->prepare("SELECT id, service, username, image, active, created FROM socialaccounts WHERE user_id = :userid AND active = TRUE");
            $stmt->execute(
                array(
                    ':userid' => $id
                )
            );
            return $stmt->fetchAll();
        }
        catch (Exception $e) {

            dumper($e->getMessage());
            return FALSE;
        }
    }
}

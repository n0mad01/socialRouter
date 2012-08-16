<?php
/**
 *  @author Adrian Soluch adrian@soluch.at
 */

include('app/classes/controller.php');
require_once 'HTTP/OAuth/Consumer.php';

class Shorteners extends Controller {

/**
 *  Method 'allowed' stores all the Methods of 
 *  this Class which are allowed to be accessed through REST
 */
    public function allowed() {

        $this->allowed = array('index', 'bitly', 'all', 'shorten', 'remove', 'add');
    }

/**
 *  Method 'authExceptions' stores the Methods where the user doesn't need to 
 *  be logged in to access through REST
 *  (Automaticly redirection to login if not in list)
 */
    //public function authExceptions()
    //{
        //$this->authExceptions = array('index', 'test', 'getTwitterAccounts');
    //}

    public function index()
    {
        if($this->isLoggedIn()) {

            $this->initDB();
            try {
                $result = '';
                $stmt = $this->DB->prepare("SELECT service, username, apikey FROM users_shorteners WHERE user_id = ?");
                if ($stmt->execute(array($_SESSION['__sessiondata']['user_id']))) {
                    $result = $stmt->fetchAll();
                }

            } catch (Exception $e) {
                dumper($e->getMessage());
                return FALSE;
            }

            // set view data
            $this->data = $result;
        }
        else {

            header('Location: http://' . $_SERVER['HTTP_HOST'] . '/users/login/');
        }
    }

    public function remove($data)
    {
        $this->initDB();
        try {
            $result = '';
            $stmt = $this->DB->prepare("DELETE FROM users_shorteners WHERE user_id = ? AND service = ? AND username = ?");
            if ($stmt->execute(array($_SESSION['__sessiondata']['user_id'], $data[0], $data[1]))) {
            }

        } catch (Exception $e) {
            dumper($e->getMessage());
            return FALSE;
        }
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/shorteners/index/');
    }

    public function add($get)
    {
        if(isset($get[0])) {
            $service = strtolower($get[0]);
            switch($service) {
                case 'google' :
                    $this->addGoogle();
                    break;
                case 'bitly' :
                    //$this->render = TRUE;
                    $this->render(array('controller'=>'shorteners', 'view'=>'bitly'));
                    return $this->bitly();
            }
        }
    }

    public function bitly()
    {
        if($this->isLoggedIn()) {

            if(isset($this->postdata)) {
                $username = $this->postdata['bitly_username'];
                $apikey = $this->postdata['bitly_api_key'];

                if(empty($username) || empty($apikey)) {
                    $ret['invalid']['error'] = _('Please Provide a username & API key!');
                    return $ret;
                }
                else {
                    if($this->checkBitlyAccount($username)) {
                        // already exists
                        $ret['invalid']['error'] = _('You have already entered this Account!');
                        return $ret;
                    }
                    else {
                        $this->saveBitlyAccount($username, $apikey);
                        // redirect
                        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/shorteners/index/');
                    }
                }
            }
        }
    }

    private function saveBitlyAccount($username, $apikey)
    {
        $this->initDB();
        try {
            $stmt = $this->DB->prepare("INSERT INTO users_shorteners (user_id, service, username, apikey, created, modified) VALUES (?, 'bitly', ?, ?, now(), now())");
            $result = $stmt->execute(array($_SESSION['__sessiondata']['user_id'], $username, $apikey));
        }
        catch (Exception $e) {
        
            dumper($e->getMessage());
            return FALSE;
        }

        if($result) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    /**
     *  Check whether a bitly username already exists
     */
    private function checkBitlyAccount($username)
    {
        $this->initDB();
        try {
            $result = '';
            $stmt = $this->DB->prepare("SELECT id FROM users_shorteners WHERE user_id = ? AND service = 'bitly' AND username = ?");
            if ($stmt->execute(array($_SESSION['__sessiondata']['user_id'], $username))) {
                $result = $stmt->fetch();
            }

        } catch (Exception $e) {
            dumper($e->getMessage());
            return FALSE;
        }

        if($result) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }

    public static function shorten($longurl)
    {
        $BITLY_API     = "http://api.bit.ly/v3/shorten?format=txt";
        $BITLY_DOMAIN  = "&domain=";
        $BITLY_LOGIN   = "&login=";
        $BITLY_APIKEY  = "&apiKey=";
        $BITLY_LONGURL = "&longUrl=";
        $USER_AGENT = "Bit.ly-Shortener 0.0a";
        $DOMAIN_BITLY = "bit.ly";

        $login = 'n0mad01';
        $apiKey = 'R_a9e235e2f3871f4fecb109a692de5100';
        //$longUrl = 'http://sr.soluch.at/';

        $apiUrl = $BITLY_API .
            //$BITLY_DOMAIN . $domain .
            $BITLY_LOGIN . $login .
            $BITLY_APIKEY . $apiKey .
            $BITLY_LONGURL . urlencode($longurl);

        //http://api.bit.ly/v3/shorten?format=txt&login=$username$&apiKey=$key$&longUrl=$longurl$

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT      => $USER_AGENT,
            CURLOPT_VERBOSE        => 1
        );

        $curl = curl_init($apiUrl);
        curl_setopt_array($curl, $options);
        $url = trim(curl_exec($curl));
        $header = curl_getinfo($curl);
        curl_close($curl);

        $result = null;
        if ($header["http_code"] == 200) {
            $result = $url;
        }
        //dumper( $result );
        return $result;
    }

    private function genShortcode() {
        $charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($charset), 0, 6);
    }

}

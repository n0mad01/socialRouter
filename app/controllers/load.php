<?php
/**
 *  @author Adrian Soluch adrian@soluch.at
 */

//include('app/classes/controller.php');
require_once('app/controllers/shorteners.php');
require_once 'Services/Twitter.php';
require_once 'HTTP/OAuth/Consumer.php';

class Load extends Controller {

/**
 *  Method 'allowed' stores all the Methods of 
 *  this Class which are allowed to be accessed through REST
 */
    public function allowed() {

        $this->allowed = array('index', 'delegateMessage', 'delegateMessageJSONP', 'js');
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

    /**
     *  default method
     */
    public function index()
    {
        $GLOBALS['renderingTime'] = FALSE;
        $GLOBALS['renderPiwik'] = FALSE;
        $this->renderDefault = FALSE;

        if($this->isLoggedIn()) {

            $this->twitterAccounts = $this->getTwitterAccounts();

            $this->shortenerAccounts = $this->getShortenerAccounts();

            if(isset($_SERVER['HTTP_REFERER'])) {
                $this->referer = Shorteners::shorten($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function js()
    {
        $GLOBALS['renderingTime'] = FALSE;
        $GLOBALS['renderPiwik'] = FALSE;
        $this->renderDefault = FALSE;
    }

    public function getShortenerAccounts()
    {
        $this->initDB();
        try {
            $result = '';
            $stmt = $this->DB->prepare("SELECT service, username, apikey FROM users_shorteners WHERE user_id = ? AND active = true");
            if ($stmt->execute(array($_SESSION['__sessiondata']['user_id']))) {
                $result = $stmt->fetchAll();
            }

        } catch (Exception $e) {
            dumper($e->getMessage());
            return FALSE;
        }
        return $result;
    }

    protected function getTwitterAccounts()
    {
        $this->initDB();
        try {
            $stmt = $this->DB->prepare("SELECT * FROM socialaccounts WHERE user_id = :userid AND service = 'twitter'");
            $stmt->execute(
                array(
                    ':userid' => $_SESSION['__sessiondata']['user_id'],
                )
            );
            $row = $stmt->fetchAll();
            if($row) {
                return $row;
            }
            else {
                return FALSE;
            }
        }
        catch (Exception $e) {

            dumper($e->getMessage());
            return FALSE;
        }
    }

    public function delegateMessageJSONP()
    {
        $GLOBALS['renderingTime'] = FALSE;
        $GLOBALS['renderPiwik'] = FALSE;
        $this->renderDefault = FALSE;

        if(isset($_SESSION['__sessiondata']['loggedin']) && $_SESSION['__sessiondata']['loggedin']) {
            dumper($_GET);
        }
    }

function parse_utf8_url($url)
{
    static $keys = array('scheme'=>0,'user'=>0,'pass'=>0,'host'=>0,'port'=>0,'path'=>0,'query'=>0,'fragment'=>0);
    if (is_string($url) && preg_match(
            '~^((?P<scheme>[^:/?#]+):(//))?((\\3|//)?(?:(?P<user>[^:]+):(?P<pass>[^@]+)@)?(?P<host>[^/?:#]*))(:(?P<port>\\d+))?' .
            '(?P<path>[^?#]*)(\\?(?P<query>[^#]*))?(#(?P<fragment>.*))?~u', $url, $matches))
    {
        foreach ($matches as $key => $value)
            if (!isset($keys[$key]) || empty($value))
                unset($matches[$key]);
        return $matches;
    }
    return false;
}

    public function delegateMessage()
    {
        $GLOBALS['renderingTime'] = FALSE;
        $GLOBALS['renderPiwik'] = FALSE;
        $this->renderDefault = FALSE;

        if(isset($_SESSION['__sessiondata']['loggedin']) && $_SESSION['__sessiondata']['loggedin']) {
        //dumper($_SESSION);

            if(isset($this->postdata)) {
                dumper($this->postdata);

                if(isset($this->postdata['twitterUser'])) {

                    $inQuery = implode(',', array_fill(0, count($this->postdata['twitterUser']), '?'));

                    $this->initDB();
                    try {
                        $stmt = $this->DB->prepare("SELECT token, token_secret FROM socialaccounts WHERE user_id = ? AND service = 'twitter' AND username IN($inQuery)");

                        $stmt->bindValue(1, $_SESSION['__sessiondata']['user_id']);
                        foreach ($this->postdata['twitterUser'] as $k => $id) {
                            $stmt->bindValue(($k+2), $id);
                        }

                        $stmt->execute();

                        $row = $stmt->fetchAll();

                    }
                    catch (Exception $e) {

                        dumper($e->getMessage());
                        return FALSE;
                    }

                    try {
                        $this->twitter = new Services_Twitter();

                        foreach($row as $r) {

                            $oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $r['token'], $r['token_secret']);
                            $this->twitter->setOAuth($oauth);

                            $msg = $this->twitter->statuses->update($this->postdata['message']);

                            dumper($msg);
                        }


                    }
                    catch (Services_Twitter_Exception $e) {

                        dumper($e->getMessage());

                    }
                }
            }
        }
    }
}

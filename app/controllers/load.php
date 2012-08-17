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

            //dumper($_GET);
            $twitterUsers = explode('|', $_GET['twitterUsers']);
            if( ! empty( $twitterUsers[0] ) ) {

                $reply = array();

                $this->initDB();
                try {
                    $inQuery = implode( ',', array_fill(0, count($twitterUsers), '?') );

                    $stmt = $this->DB->prepare("SELECT username, token, token_secret FROM socialaccounts WHERE user_id = ? AND service = 'twitter' AND username IN($inQuery)");
                    $stmt->bindValue( 1, $_SESSION['__sessiondata']['user_id'] );
                    foreach ($twitterUsers as $k => $id) {
                        $stmt->bindValue(($k+2), $id);
                    }

                    $stmt->execute();
                    $row = $stmt->fetchAll();

                    //dumper( $row );
                }
                catch (Exception $e) {

                    dumper($e->getMessage());
                    return FALSE;
                }
            }

            try {
                $reply['html'] = '';
                $this->twitter = new Services_Twitter();

                foreach($row as $r) {

                    $oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $r['token'], $r['token_secret']);
                    $this->twitter->setOAuth($oauth);

//dumper($r['username']);
                    //dumper( urldecode( $_GET['textarea'] ) );
                    //$msg = $this->twitter->statuses->update( urldecode( $_GET['textarea'] ) );
                    //if( $msg ) {
                        //dumper($msg);
                        //$reply['html'] = '<div>OK</div>';
                        $reply['html'] .= '<span>' . $r['username'] . ' - tweeted <img src="data:image/gif;base64, iVBORw0KGgoAAAANSUhEUgAAAAwAAAALCAYAAABLcGxfAAABXklEQVQoz2NgQAN6vX5wdvb2NGYY231ehEPI8rhzKIqN+gPg7LxdGXDF/oujPWLWJh4JXha7Hq7AYkoQhKEuxFC4JxOu2GdhlGfC+qSnMWsSjtlMC1FhsJ0ewmw0wZ8RLOugxFCEpNh7YVRAyqaUd4kbku47zgw1QHEO0ErW1C0prEiKIzO2pn1L25L6Beh+J7hC3V4/IXSP+y6KSgR6+FfW9rT/AUui42DiJ370MjA4zgrt8VoYGeW7OFqEwVCaw29xdFbuzvRf+bsy/ocsj21ANki51Qto2uKoruwd6a+SN6WcCVsZNxcYOh+K9mb9j12buBioBu5Ei8mBEIbb3Ail1M0pVwt2Z/4v25/9v2Rf9v+UzSknJOpc5WCKnWeHMaK42WNeRFTh7syPVYdy/wNte2w3I9QNJpeyOZmRIccKzZf2ChyJG5K3AZ3zP3pNwgSY8Kv/cxn4yhxRlAIANjqCIshinqwAAAAASUVORK5CYII=" alt="base64 check"></span><br />';
                        //$reply['user'][$r['username']] = 'ok';
                    //}
                }
            }
            catch (Services_Twitter_Exception $e) { dumper($e->getMessage()); }

            echo $_GET['callback'] . '('.json_encode($reply).')';
            //echo $_GET['callback'] . '()';
        }
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

<?php
/**
 *  @author Adrian Soluch adrian@soluch.at
 */

//include('app/classes/controller.php');
include('app/controllers/shorteners.php');
require_once 'Services/Twitter.php';
require_once 'HTTP/OAuth/Consumer.php';

class Load extends Controller {

/**
 *	Method 'allowed' stores all the Methods of 
 *	this Class which are allowed to be accessed through REST
 */
    public function allowed() {

        $this->allowed = array('index', 'delegateMessage', 'js');
    }

/**
 *	Method 'authExceptions' stores the Methods where the user doesn't need to 
 *	be logged in to access through REST
 *	(Automaticly redirection to login if not in list)
 */
	//public function authExceptions()
    //{
		//$this->authExceptions = array('index', 'test', 'getTwitterAccounts');
	//}

	/**
	 *	default method
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
//dumper($this->shortenerAccounts);die();
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
            //dumper($result['user_id']);die();

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
        //dumper($row);
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

	public function delegateMessage()
    {
        if(isset($_SESSION['__sessiondata']['loggedin']) && $_SESSION['__sessiondata']['loggedin']) {

		    if(isset($this->postdata)) {
		        dumper($this->postdata);

                if(isset($this->postdata['twitterUser'])) {

                    $inQuery = implode(',', array_fill(0, count($this->postdata['twitterUser']), '?'));
//dumper($inQuery);
                    $this->initDB();
		            try {
                        $stmt = $this->DB->prepare("SELECT token, token_secret FROM socialaccounts WHERE user_id = ? AND service = 'twitter' AND username IN($inQuery)");

                        $stmt->bindValue(1, $_SESSION['__sessiondata']['user_id']);
                        foreach ($this->postdata['twitterUser'] as $k => $id) {
                            $stmt->bindValue(($k+2), $id);
                        }

                        $stmt->execute();

                        $row = $stmt->fetchAll();
                        //dumper($row);

		            }
                    catch (Exception $e) {

		            	dumper($e->getMessage());
		            	return FALSE;
		            }

                    //dumper($row);
                    try {
                        $this->twitter = new Services_Twitter();

                        foreach($row as $r) {

                            //dumper($r);
                            //$oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $row['token'], $row['token_secret']);
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

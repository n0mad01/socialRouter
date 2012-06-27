<?php
/**
 *  @author Adrian Soluch adrian@soluch.at
 */

include('app/classes/controller.php');
require_once 'Services/Twitter.php';
require_once 'HTTP/OAuth/Consumer.php';

class Twitter extends Controller {

	public function __construct() {}

	public function __get($var = NULL) {}

	public function authExceptions() {

		$this->authExceptions = array('test');
	}

	/**
	 *	the logged-in users site map for all twitter accounts 
	 *	(choose a twitter account)
	 */
	public function index()
    {
        $this->setFlashMsg('FLASH');
	}

	public function testTweet()
    {
        if(isset($_SESSION['__sessiondata']['loggedin']) && $_SESSION['__sessiondata']['loggedin']) {

        //dumper($_SESSION);
        //dumper($username);
/*        $username = 'php_live';
        $this->initDB();
		try {
            $stmt = $this->DB->prepare("SELECT token, token_secret FROM socialaccounts WHERE user_id = :userid AND service = 'twitter' AND username = :username");
            $stmt->execute(
                array(
                    ':userid' => $_SESSION['__sessiondata']['user_id'],
                    ':username' => $username,
                )
            );
            $row = $stmt->fetch();
		}
        catch (Exception $e) {

			dumper($e->getMessage());
			return FALSE;
		}

        //dumper($row);
        try {
            $this->twitter = new Services_Twitter();

            $oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $row['token'], $row['token_secret']);
            $this->twitter->setOAuth($oauth);

            $msg = $this->twitter->statuses->update("TestTweet!");

            dumper($msg);

        }
        catch (Services_Twitter_Exception $e) {

            dumper($e->getMessage());

        }*/
        }
	}

	/**
	 *	an overview-site for a twitter account
	 *
	 *	@param		String		$user		
	 *	@return		Void
	 */
	public function account($user = NULL) {

		if(!empty($user)) {

			$user = $user[0];

			if(isset($this->session->twitterAccounts->$user) || $this->getTwitterAccount($user)) {
				// set data for view
				$usr = $this->session->twitterAccounts->$user;
				$this->postdata['screen_name'] = $usr['screen_name'];
				$this->postdata['profile_image_url'] = $usr['profile_image_url'];
				$this->postdata['allAccounts'] = $this->session->twitterAccounts;
			} else {
				// TODO no such account
				return FALSE;
			}
		}
	}

	/**
	 *	remove a users twitter account 
	 */
	public function remove() {

		$twUsers = Array();
		$this->collection = $this->DB->selectCollection($this->config['database'], 'twitterAccounts');

		if($this->postdata) {
			try {
				// Remove the given user
				$this->collection->update(
					array(
						'accounts' => array(
							'$elemMatch' => array(
								'screen_name' => $this->postdata['user']
							)
						)
					),
					array(
						'$pull' => array(
							'accounts' => array(
								'screen_name' => $this->postdata['user']
							)
						)
					)
				);
				$twUsers['msg'] = _('Twitter account '. $this->postdata['user'] .' has been successfully removed!');

			} catch (Exception $e) {
				dumper($e->getMessage());
				return FALSE;
			}
		}

		try {

			$query = array('userId' => $this->session->userID);
			$found = $this->collection->findOne($query); 

			if(!empty($found)) {

				// forwardind the twitter-user screen_name
				// the view
				foreach($found['accounts'] as $f) {
					$twUsers['twitterusers'][] = $f['screen_name'];
				}

				$this->postdata = $twUsers;
			}
			return TRUE;

		} catch (Exception $e) {
			dumper($e->getMessage());
			return FALSE;
		}
	}

	/**
	 *	the method for adding twitter users
	 */
	public function add($credentials = NULL) {

		try {
			$oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET);

			$oauth->getRequestToken('http://twitter.com/oauth/request_token', 'http://' . $_SERVER['HTTP_HOST'] . '/twitter/accessToken');

			$this->session->token = $oauth->getToken();
			$this->session->token_secret = $oauth->getTokenSecret();

		    $url = $oauth->getAuthorizeUrl('http://twitter.com/oauth/authorize');

		    header('Location: '.$url);

		} catch (Services_Twitter_Exception $e) {
			dumper($e->getMessage());
		}
	}

	/**
	 *	method for receiving the accessToken from Twitter,
	 *	so the return-point from twitter should look like:
	 *	http://domain.com/twitter/accessToken
	 */
	public function accessToken($credentials = NULL) {

		try {

			$oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $this->session->token, $this->session->token_secret);
			$oauth->getAccessToken('http://twitter.com/oauth/access_token', $_GET['oauth_verifier']);

			// the 'real'/ long-time AccessToken
			$token = Array();
			$token['token'] = $oauth->getToken();
			$token['token_secret'] = $oauth->getTokenSecret();

			// save the retrieved token
			$this->saveTokens($token);

		} catch (Services_Twitter_Exception $e) {
			dumper($e->getMessage());		
		}
		
	}

	/**
	 *	Saving the retrieved token (accessToken())
	 *
	 *	@params		Array	$token	
	 */
	private function saveTokens($token = NULL)
    {
        if(isset($_SESSION['__sessiondata']['loggedin']) && $_SESSION['__sessiondata']['loggedin']) {
		    if($token !== NULL) {

		    	try {

		    		$this->twitter = new Services_Twitter();

		    		$oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $token['token'], $token['token_secret']);
		    		$this->twitter->setOAuth($oauth);

		    		// get twitter-username et al.
		    		$credentials = $this->twitter->account->verify_credentials();

		    	} catch (Services_Twitter_Exception $e) {
		    		echo $e->getMessage();
		    	}

//dumper($credentials);die();

                $this->initDB();

				try {
                    $stmt = $this->DB->prepare("SELECT id FROM socialaccounts WHERE user_id = :userid AND service = 'twitter' AND username = :username AND user_service_id = :user_service_id");
                    $stmt->execute(
                        array(
                            ':userid' => $_SESSION['__sessiondata']['user_id'],
                            ':username' => $credentials->screen_name,
                            ':user_service_id' => $credentials->id,
                        )
                    );
                    $row = $stmt->fetch();
		    	}
                catch (Exception $e) {

		    		dumper($e->getMessage());
		    		return FALSE;
		    	}

                if($row) {
                    // Twitter account already linked
                    $this->setErrorMsg(_('This Twitter User is already linked to your account!'));
                }
                else {
		    	    try {
                        $pdoparams = array(
                            ':userid' => $_SESSION['__sessiondata']['user_id'],
                            ':service' => 'twitter',
                            ':username' => $credentials->screen_name,
                            ':user_service_id' => $credentials->id,
                            ':image' => $credentials->profile_image_url,
                            ':token' => $token['token'],
		    	    		':token_secret' => $token['token_secret']
                        );
                        $sql = "INSERT INTO socialaccounts (user_id, service, username, user_service_id, image, token, token_secret, created, modified) VALUES (:userid, :service, :username, :user_service_id, :image, :token, :token_secret, now(), now())";
                        $stmt = $this->DB->prepare($sql);
                        $stmt->execute($pdoparams);
		    	    }
                    catch (Exception $e) {

		    	    	dumper($e->getMessage());
		    	    	return FALSE;
		    	    }
                }

		    	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/twitter/account/' . $credentials->screen_name);
		    	return TRUE;
		    }
		}
		return FALSE;
	}

	/**
	 *	Returns (when exists) data for a users Twitter account
	 *	Also sets all found accounts into Memcache
	 *
	 *	@params		String		twitterAccount
	 *	@Return		Boolean/Array
	 */
	private function getTwitterAccount($user) {

		/*$this->collection = $this->DB->selectCollection($this->config['database'], 'twitterAccounts');

		// retrieve all users twitter-accounts
		$query = array('userId' => $this->session->userID, 'accounts.screen_name' => $user);
		$found = $this->collection->findOne($query);

		if(!empty($found)) {

			// set memcache
			$userObj = new stdClass;

			$true = FALSE;

			// filter the 'right' account
			foreach($found['accounts'] as $acc) {
				// save all screen_names for view (changeing accounts)
				$allAccounts[] = $acc['screen_name'];

				// for memcached
				$userObj->$acc['screen_name'] = array(
					'screen_name' => $acc['screen_name'],
					'profile_image_url' => $acc['profile_image_url'],
					'id' => $acc['id'],
					'token' => $acc['token'],
					'token_secret' => $acc['token_secret']
				);
				if($acc['screen_name'] === $user) {
					$account = $acc;
					$true = TRUE;
				}
			}

			if($true) {
				$this->session->twitterAccounts = $userObj;
				return TRUE;
			} else {
				return FALSE;
			}
		} else { 
			return FALSE;
		}*/
	}

}

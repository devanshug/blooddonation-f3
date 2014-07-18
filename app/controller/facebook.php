<?php

class Facebook
{
	protected $f3;
	protected $db;
	
	public function __construct($f3, $db)
	{
		$this->f3 = $f3;
		$this->db = $db;
	}
	
	public function check_login()
	{
		/* If the user wants to login using the Facebook Plugin for login. */

		/* If there is no accessToken sent through the request. Then it is
		/* Invalid Login Request. */
		if(!$this->f3->exists('GET.accessToken'))
		{
			$this->f3->reroute('/?message=Access%20Token%20Missing.');
		}

		/* Fields sent by Facebook to authorise the user.
		/* Access Token is the limited period token which expires after some time. 
		/* Signed Request is another parameter sent by Facebook, and can used in
		/* creating Object in Facebook PHP SDK */
		$accessToken = $this->f3->get('GET.accessToken');
		if($this->f3->exists('GET.signedRequest'))
			$signedRequest = $this->f3->get('GET.signedRequest');

		/* AppID and AppSecret are parameter related the Actual App hosted on Facebook */
		$appId = $this->f3->get('FB_APP_ID');
		$appSecret = $this->f3->get('FB_APP_SECRET');

		/* Checking if the Access Token is correct or not. */
		if(!$this->is_valid_fb_token($accessToken))
		{
			$this->f3->error(401);
			return;
		}

		/* This allows and sets some important parameters for the Facebook PHP SDK. */
		Facebook\FacebookSession::setDefaultApplication($appId, $appSecret);
		/* Creating Session object allows to get the Graph Object from facebook
		/* to retrieve information */
		$session = new Facebook\FacebookSession($accessToken);

		if ($session) {
			/* Create a Request Object, so that it can be sent to facebook
			/* for getting Required Information*/
			$request = new Facebook\FacebookRequest($session, 'GET', '/me');
			/* Recieved Request is in the form of Response Object */
			$response = $request->execute();
			/* From this Response Object we can create a Graph Object. */ 
			$user_profile = $response->getGraphObject();

			/* This Graph Object contains information sent by Graph API of facebook */
			$email = $user_profile->getProperty('email');
			$firstname = $user_profile->getProperty('first_name');
			$lastname = $user_profile->getProperty('last_name');
			$gender = $user_profile->getProperty('gender');
			$name = $user_profile->getProperty('name');
			$pic = "http://graph.facebook.com/".$user_profile->getProperty('id')."/picture?height=100&type=normal&width=100";

			/* Everythings is now fine, lets create Object, Sessions and Save Important Information */
			$fbuser_obj = new FBUser($this->db);
			$fbuser = array('username'=>$email, 'firstname'=>$firstname, 'lastname'=>$lastname, 'gender'=>$gender, 'name'=>$name, 'pic'=>$pic);
			$this->f3->set('SESSION.fbuser', $fbuser+array('accessToken'=>$accessToken));
			$this->f3->set('fbuser', $fbuser);
			if(!$fbuser_obj->exists($email))
			{
				$fbuser_obj->add();
				$this->f3->reroute('/wall?message=You%20have%20been%20successfully%20registered%20and%20Logged%20In');
			}
			else
			{
				$fbuser_obj->edit($email);
			}
			$this->f3->reroute('/wall?message=You%20have%20successfully%20been%20Authorised%20and%20Logged%20In');
		}
	}
	
	public function check_loggedin()
	{
		if($this->f3->exists("SESSION.fbuser"))
		{
			try
			{
				$accessToken = $this->f3->get("SESSION.fbuser['accessToken']");
				if(!$this->is_valid_fb_token($accessToken))
				{
					$this->check_logout();
					$this->f3->reroute('/?message=You%20have%20successfully%20been%20Logged%20Out');
				}
			}
			catch(Exception $e)
			{
				$this->check_logout();
				$this->f3->reroute('/?message=You%20have%20successfully%20been%20Logged%20Out');
			}
			return true;
		}
		else
		{
			$this->f3->clear("SESSION.fbuser");
		}
		return false;
	}
	
	public function check_logout()
	{
		$this->f3->clear('SESSION.fbuser');
	}
	
	public function is_valid_fb_token($accessToken)
	{
		$appId = $this->f3->get('FB_APP_ID');
		$token_url = "https://graph.facebook.com/oauth/access_token_info?client_id=". $appId . "&access_token=". $accessToken;
		$response = @file_get_contents($token_url);
		$params = json_decode($response);
		if($params==NULL || $params->{"access_token"}=="")
		{
			return false;
		}
		return true;
	}
}
<?php

class UserController extends Controller
{

	protected function _fileexist($file)
	{
		//Check for all the files in the UI folder
		foreach ($this->f3->split($this->f3->get('UI').';./') as $dir)
		{
			if (is_file($this->view=$this->f3->fixslashes($dir.$file))) {
				return true;
			}
		}
		return false;
	}

	public function index()
	{	
		/* method variable is gets current HTTP REQUEST method type -> GET|POST */
		$method = $this->f3->get('VERB');
		
		/* Variable mostly common for all the Pages */
		
		/* 'pagename' = It is name of the page which is asked to be loaded. */
		$pagename = $this->f3->get('PARAMS.pagename');
		
		/* Check if the user is logged in or not. */
		$logged_in = false;
		if($this->auth->check_loggedin())
		{
			$logged_in = true;
			$this->f3->set('logged_in', $logged_in);
			$this->f3->set('type', 'regular');
			$this->f3->set('username', $this->f3->get("SESSION.user['username']"));
		}
		else if($this->check_loggedin_FB())
		{
			$logged_in = true;
			$this->f3->set('logged_in', $logged_in);
			$this->f3->set('type', 'facebook');
			$this->f3->set('username', $this->f3->get("SESSION.fbuser['username']"));
		}
		else
		{
			$this->f3->set('logged_in', $logged_in);
		}
		
		/* Passing Invalid HTTP Request */
		if($logged_in && ($pagename=='login' || $pagename=='' || $pagename=='register'))
		{
			$this->f3->reroute('/wall?message=Page%20Not%20Available%20for%20LoggedIn%20User.');
		}
		else if(!$logged_in && ($pagename=='wall' || $pagename=='urgent' || $pagename=='logout'))
		{
			$this->f3->reroute('?message=Page%20Not%20Available%20for%20Unauthorised%20User.');
		}
		
		/* Logging the user in if login is successful. */
		if($pagename=='login' && $method=='POST')
		{
			$auth = new Authenticate($this->f3, $this->db);
			if($auth->check_login($this->f3->get('POST.username'), $this->f3->get('POST.password')))
			{
				$this->f3->reroute('/wall?message=You%20have%20successfully%20been%20Authorised%20and%20Logged%20In.');
			}
			else
			{
				$this->f3->set('error','Incorrect Login Information Provided');
			}
		}
		/* Logging the user out if logout is successful. */
		if($pagename=='logout')
		{
			if($this->f3->get('type')=='regular')
			{
				$this->auth->check_logout();
				$this->f3->reroute('?message=You%20have%20successfully%20been%20Logged%20Out');
			}
			else if($this->f3->get('type')=='facebook')
			{
				$this->check_logout_FB();
				$this->f3->reroute('?message=You%20have%20successfully%20been%20Logged%20Out');
			}
			else
			{
				$this->f3->error(404);
			}
		}
		
		/* 'donorlist' = Loads Registered Donors List ->
		/*				 Bloodgroups and number of Bloodgroup owner registered. */
		$custom = new Custom($this->db);
		$this->f3->set('donorlist', $custom -> getDonorList());
		
		/* Variable which are page specific, it includes, databases
		/* results and other specific variables. Some property may
		/* be eligible for bunch of pages, while other for a single
		/* page. */
		
		/* 'banner' = It remains false for all pages except main index page.
		/* This variable is responsible for loading the banner in the main page. */
		$banner = false;
		if($pagename=='')
		{
			$banner = true;
		}
		$this->f3->set('banner', $banner);
		
		/* 'urgent' = Loads Urgent Blood Requirement Requests List. 
		/* The list is only shown in Index and Wall page.   */
		if($pagename=='' || $pagename=='wall')
		{
			if($method=='GET' && $this->f3->exists('GET.message'))
			{
				$this->f3->set('response_message', $this->f3->get('GET.message'));
			}
			$this->paginateRequests(0);
			$pagename = 'requests';
		}
		
		/* If the required page is for registration or urgent */
		if($pagename=='register' || $pagename=='urgent')
		{
			$groups = new Bloodgroup($this->db);
			$this->f3->set('groups', $groups->all());
			$states = new State($this->db);
			$this->f3->set('states', $states->all());
			$captchas = new CaptchasDotNet('devanshug', '6HdMmG1EgKnJEOqHfQ5r9ywhkUbcM42UaeA0ffK1',
										   $this->f3->get('CAP_API_PATH').'captchas',
										   '3600','abcdefghkmnopqrstuvwxyz0123456789','6','240',
										   '80','000088');
			$this->f3->set('random',$captchas->random());
			$this->f3->set('image_url',$captchas->image_url());
			
			$reset = true;
			if($method=='POST')
			{
				if($pagename=='register')
					$reset = $this->register();
				else if($pagename=='urgent')
					$reset = $this->urgent();
			}
			$this->f3->set('textcomponent', 'textcomponent-inactive');
			if($reset)
			{
				if(!$this->f3->exists('error'))
					$this->f3->set('error', '');
				$this->f3->set('username', 'Enter a Username');
				$this->f3->set('firstname', 'Enter your First Name');
				$this->f3->set('lastname', 'Enter your Last Name');
				$this->f3->set('email', 'Enter your mail address');
				$this->f3->set('gender', '');
				$this->f3->set('bloodgroup', '');
				$this->f3->set('city', '');
				$this->f3->set('cities', array());
				$this->f3->set('state', '');
				$this->f3->set('day', '');
				$this->f3->set('month', '');
				$this->f3->set('year', '');
				$this->f3->set('mobile', 'Mobile Number');
				$this->f3->set('name', 'Enter the Recipient\'s Name');
				$this->f3->set('location', 'Name of hospital etc.');
				$this->f3->set('message', 'Enter your message');
				$this->f3->set('textcomponent', 'textcomponent-active');
			}
		}
		$filename = 'content/'.$pagename.'.htm';
		if($this->_fileexist($filename))
		{
			$this->f3->set('pagename', $filename);
			echo Template::instance()->render('godfather.htm');
		}
		else
		{
			$this->f3->error(404);
		}
	}
	
	public function check_logout_FB()
	{
		$this->f3->clear('SESSION.fbuser');
	}
	
	public function is_valid_fb_token($accessToken)
	{
		$appId = $this->f3->get('FB_APP_ID');
		$token_url="https://graph.facebook.com/oauth/access_token_info?client_id=". $appId . "&access_token=". $accessToken;
		$response = @file_get_contents($token_url);
		if($response===false)
		{
			$params = null;
			parse_str($response, $params);
			$this->check_logout_FB();
			return false;
		}
		return true;
	}
	
	public function check_loggedin_FB()
	{
		if($this->f3->exists("SESSION.fbuser"))
		{
			try
			{
				$accessToken = $this->f3->get("SESSION.fbuser['accessToken']");
				return $this->is_valid_fb_token($accessToken);
			}
			catch(Exception $e)
			{
				$this->check_logout_FB();
				return false;
			}
			return true;
		}
		$this->f3->clear("SESSION.fbuser");
		return false;
	}
	
	public function paginateRequests($value)
	{
		$urgent = new Urgent($this->db);
		$urgent_count = $urgent->countall();
		$paginate_size = intval($this->f3->get('PAGINATE_SIZE'));
		$pagenumber = intval($value);
		$from = $pagenumber * $paginate_size;
		if($from + $paginate_size < $urgent_count)
		{
			$this->f3->set('next_requests', $pagenumber+1);
		}
		if($pagenumber > 0)
		{
			$this->f3->set('prev_requests', $pagenumber-1);
		}
		$requests = $urgent->paginateUrgent($pagenumber, $paginate_size);
		$this->f3->set('urgent', $requests);
	}
	
	/* This function is executed when there is a request from /@pagename/@value */
	public function infoPages()
	{
		/* PARAMS.pagename is @pagename and PARAMS.value is @value */
		$pagename = $this->f3->get('PARAMS.pagename');
		$value = $this->f3->get('PARAMS.value');
		
		if($pagename == 'login')
		{
			/* Whenever any user wants to Login to the website other than
			/* regular way, like using social resources.*/
			
			/* But what if he/she is already Logged In, such requests are Invalid. */
			if($this->auth->check_loggedin() || $this->check_loggedin_FB())
			{
				$this->f3->reroute('/wall?message=Page%20Not%20Available%20for%20LoggedIn%20User.');
			}
			if($value=='fb')
			{
				/* If the user wants to login using the Facebook Plugin for login. */
				
				/* If there is no accessToken sent through the request. Then it is
				/* Invalid Login Request. */
				if(!$this->f3->exists('GET.accessToken'))
				{
					$this->f3->reroute('?message=Access%20Token%20Missing.');
				}
				
				/* Fields sent by Facebook to authorise the user.
				/* Access Token is the limited period token which expires after some time. 
				/* Signed Request is another parameter sent by Facebook, and can used in
					creating Object in Facebook PHP SDK*/
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

					/* Everythings is now fine, lets create Object, Sessions and Save Important Information */
					$fbuser_obj = new FBUser($this->db);
					$fbuser = array('username'=>$email, 'firstname'=>$firstname, 'lastname'=>$lastname, 'gender'=>$gender, 'name'=>$name);
					$this->f3->set('SESSION.fbuser', $fbuser+array('accessToken'=>$accessToken));
					$this->f3->set('fbuser', $fbuser);
					if(!$fbuser_obj->exists($email))
					{
						$fbuser_obj->add();
						$this->f3->reroute('/wall?message=You%20have%20been%20successfully%20registered%20and%20Logged%20In');
					}
					$this->f3->reroute('/wall?message=You%20have%20successfully%20been%20Authorised%20and%20Logged%20In');
				}
			}
			else
			{
				/* No other page exists otherwise. */
				$this->f3->error(404);
			}
		}
		else if($pagename=='requests')
		{
			/* This pages resends all the blood requests information back to
			/* the browser, ORDERED and Paginated. */
			$this->paginateRequests($value);
			echo Template::instance()->render('content/requests.htm');
		}
		else if($pagename == 'data')
		{
			/* Whenever any state is selected the request for the required is
			/* executed in this block, via url /data/STATE_NAME */
			$cities = new City($this->db);
			$this->f3->set('cities', $cities->getByStateName($value));
			echo Template::instance()->render('info/data.htm');
		}
		else if($pagename == 'exists')
		{
			/* When a user want to register than this page can be used by
			/* Client-side script to check if the user already exists or not. */
			$user = new User($this->db);
			if($user->exists($value))
				echo "Username Exists";
		}
		else if($pagename == 'verify')
		{
			/* Following code runs when the request is from /verify/captcha_api?password=XXX&random=YYY */
			if($value=='captcha_api')
			{
				/* GET variables send via the request */
				$password = $this->f3->get('GET.password');
				$random   = $this->f3->get('GET.random');
				
				$valid = new ValidationController;
				$valid->captcha_validation($password, $random);
			}
			else
			{
				/* No other page exists otherwise. */
				$this->f3->error(404);
			}
		}
		else
		{
			/* No other page exists otherwise. */
			$this->f3->error(404);
		}
	}
	
	public function urgent()
	{
		/* Controller for validating the fields. */
		$valid = new ValidationController;
		
		/* POST array sent through request. */
		$post = $this->f3->get('POST');
		
		/* To check if all the required fields exists. */
		if(!$this->f3->exists('POST.bloodgroup') || !$this->f3->exists('POST.name') || !$this->f3->exists('POST.mobile') || !$this->f3->exists('POST.location') || !$this->f3->exists('POST.state') || !$this->f3->exists('POST.city') || !$this->f3->exists('POST.message') || !$this->f3->exists('POST.captchacode') || !$this->f3->exists('POST.random'))
		{
			$this->f3->set('error', 'Incorrect Information provided');
			return true;
		}
		
		/* User currently logged in is the user making request. */
		$username = $this->f3->get('username');
		
		/* Fields entered during Urgent Blood Form filling. */
		$bloodgroup = $post['bloodgroup'];
		$name = $post['name'];
		$mobile = $post['mobile'];
		$location = $post['location'];
		$state = $post['state'];
		$city = $post['city'];
		$message = $post['message'];
		$captchacode = $post['captchacode'];
		$random = $post['random'];
		
		/* To check if all the required fields are valid or not. */
		if($valid->captcha_validation($captchacode,$random,false) || $valid->bloodgroup($bloodgroup) || $valid->fullname($name) || $valid->mobile($mobile) || $valid->location($location) || $valid->place($city,$state) || $valid->message($message))
		{
			$error = $valid->getError();
			$cities = new City($this->db);
			$this->f3->set('error', $error);
			$this->f3->set('bloodgroup', $bloodgroup);
			$this->f3->set('name', $name);
			$this->f3->set('mobile', $mobile);
			$this->f3->set('location', $location);
			$this->f3->set('state', $state);
			$this->f3->set('city', $city);
			$this->f3->set('cities', $cities->getByStateName($state));
			$this->f3->set('message', $message);
			return false;
		}
		else
		{
			/* Register and save the confirmed User Blood Request. */
			$this->f3->set('urgent', array('username'=>$username, 'bloodgroup'=>$bloodgroup, 'name'=>$name, 'mobile'=>$mobile,
										'location'=>$location, 'state'=>$state, 'city'=>$city, 'message'=>$message));
			$urgent = new Urgent($this->db);
			$urgent->add();
			$this->f3->reroute('/wall?message=Your%20Response%20has%20been%20saved.');
		}
	}
	
	public function register()
	{
		/* Controller for validating the fields. */
		$valid = new ValidationController;
		
		/* POST and FILES array sent through request. */
		$post = $this->f3->get('POST');
		$file = $this->f3->get('FILES');
		
		/* To check if all the required fields exists. */
		if(!$this->f3->exists('POST.username') || !$this->f3->exists('POST.firstname') || !$this->f3->exists('POST.lastname') || !$this->f3->exists('POST.password1') || !$this->f3->exists('POST.password2') || !$this->f3->exists('POST.email') || !$this->f3->exists('POST.gender') || !$this->f3->exists('POST.bloodgroup') || !$this->f3->exists('POST.city') || !$this->f3->exists('POST.state') || !$this->f3->exists('POST.day') || !$this->f3->exists('POST.month') || !$this->f3->exists('POST.year') || !$this->f3->exists('POST.mobile') || !$this->f3->exists('FILES.pic') || !$this->f3->exists('POST.captchacode') || !$this->f3->exists('POST.random'))
		{
			$this->f3->set('error', 'Incorrect Information provided');
			return true;
		}
		
		/* Fields entered during registration */
		$username = $post['username'];
		$firstname = $post['firstname'];
		$lastname = $post['lastname'];
		$password1 = $post['password1'];
		$password2 = $post['password2'];
		$email = $post['email'];
		$gender = $post['gender'];
		$bloodgroup = $post['bloodgroup'];
		$city = $post['city'];
		$state = $post['state'];
		$day = $post['day'];
		$month = $post['month'];
		$year = $post['year'];
		$mobile = $post['mobile'];
		$pic = $file['pic'];
		$captchacode = $post['captchacode'];
		$random = $post['random'];
		
		/* To check if all the required fields are valid or not. */
		if($valid->captcha_validation($captchacode,$random,false) || $valid->userName($username) || $valid->name($firstname) || $valid->name($lastname) || $valid->passwordMatch($password1,$password2) || $valid->email($email) || $valid->gender($gender) || $valid->bloodgroup($bloodgroup) || $valid->place($city,$state) || $valid->date($day, $month, $year) || $valid->mobile($mobile) || ($pic['name']==''?false:$valid->image($pic)))
		{
			$error = $valid->getError();
			$cities = new City($this->db);
			$this->f3->set('error', $error);
			$this->f3->set('username', $username);
			$this->f3->set('firstname', $firstname);
			$this->f3->set('lastname', $lastname);
			$this->f3->set('email', $email);
			$this->f3->set('gender', $gender);
			$this->f3->set('bloodgroup', $bloodgroup);
			$this->f3->set('city', $city);
			$this->f3->set('cities', $cities->getByStateName($state));
			$this->f3->set('state', $state);
			$this->f3->set('day', $day);
			$this->f3->set('month', $month);
			$this->f3->set('year', $year);
			$this->f3->set('mobile', $mobile);
			return false;
		}
		else
		{
			/* To check if the file is uploaded or not. */
			if($pic['name']=='')
			{
				$newname = '';
			}
			else
			{
				/* Save the file in the correct location. */
				$tempfilepath = $pic['tmp_name'];
				$filename = $pic['name'];
				$extension = substr($filename, strrpos($filename,'.'));
				$newname = $username.$extension;
				$filepath = $f3->get('UPLOAD_IMG_PATH').$newname;
				move_uploaded_file($tempfilepath, $filepath);
			}
			/* Register and save the confirmed User information. */
			$this->f3->set('details', array('username'=>$username, 'email'=>$email, 'firstname'=>$firstname, 'lastname'=>$lastname,
								 'bloodgroup'=>$bloodgroup, 'gender'=>$gender, 'day'=>$day, 'month'=>$month, 'year'=>$year,
								 'state'=>$state, 'city'=>$city, 'mobile'=>$mobile, 'pic'=>$newname));
			$this->f3->set('user' , array('username'=>$username, 'password'=>$password1));
			$detail = new Details($this->db);
			$detail->add();
			$user = new User($this->db);
			$user->add();
			$auth = new Authenticate($this->f3, $this->db);
			$auth -> check_login($username, $password1);
			$this->f3->reroute('/wall?message=You%20have%20been%20successfully%20registered');
		}
	}
}
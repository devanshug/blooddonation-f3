<?php

class FunctionController extends Controller
{
	protected function _fileexist($file)
	{
		/* Check for all the files in the UI folder(s) if the page exists. */
		foreach ($this->f3->split($this->f3->get('UI').';./') as $dir)
		{
			if (is_file($this->view=$this->f3->fixslashes($dir.$file))) {
				return true;
			}
		}
		return false;
	}

	public function hasLoggedIn()
	{
		/* Check if the user is logged in or not. */
		$logged_in = false;
		if($this->auth->check_loggedin())
		{
			$logged_in = true;
			$this->f3->set('logged_in', $logged_in);
			$this->f3->set('type', 'regular');
			$this->f3->set('username', $this->f3->get("SESSION.user['username']"));
		}
		else if($this->fb->check_loggedin())
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
		return $logged_in;
	}

	public function isInvalidHTTPRequest($logged_in, $pagename, $method)
	{
		/* Passing Invalid HTTP Request */
		if($logged_in && ($pagename=='login' || $pagename=='' || $pagename=='register'))
		{
			$this->f3->reroute('/wall?message=Page%20Not%20Available%20for%20LoggedIn%20User.');
		}
		else if(!$logged_in && ($pagename=='wall' || $pagename=='urgent' || $pagename=='logout'))
		{
			$this->f3->reroute('/?message=Page%20Not%20Available%20for%20Unauthorised%20User.');
		}
		if($method=='POST' && !($pagename=='register' || $pagename=='urgent' || $pagename=='login'))
		{
			$this->f3->error(405);
		}
		if($pagename=='requests')
			$this->f3->error(404);
	}

	public function login()
	{
		/* Logging the user in if login is successful. */
		$regular = $this->f3->exists('POST.username') && $this->f3->exists('POST.password');
		$fb = $this->f3->exists('GET.accessToken');
		if(!$regular && !$fb)
		{
			return;
		}
		if(!($regular^$fb))
		{
			$this->f3->set('error','Unauthorised way of Login used.');
			return;
		}
		echo $this->f3->get('GET.accessToken');
		/* Regular Login */
		if($this->auth->check_login($this->f3->get('POST.username'), $this->f3->get('POST.password')))
		{
			$this->f3->reroute('/wall?message=You%20have%20successfully%20been%20Authorised%20and%20Logged%20In.');
		}
		/* Facebook Login */
		else if($this->fb->check_login())
		{
			$this->f3->reroute('/wall?message=You%20have%20successfully%20been%20Authorised%20and%20Logged%20In.');
		}
		else
		{
			$this->f3->set('error','Incorrect Login Information Provided');
		}
	}
	
	public function logout()
	{
		if($this->f3->get('type')=='regular')
		{
			$this->auth->check_logout();
			$this->f3->reroute('/?message=You%20have%20successfully%20been%20Logged%20Out');
		}
		else if($this->f3->get('type')=='facebook')
		{
			$this->fb->check_logout();
			$this->f3->reroute('/?message=You%20have%20successfully%20been%20Logged%20Out');
		}
		else
		{
			$this->f3->error(404);
		}
	}
	
	public function setFormBasicInfo()
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
		$this->f3->set('textcomponent', 'textcomponent-inactive');
	}
	
	public function setDefaultFormField()
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
		/* 'urgent' = Loads Urgent Blood Requirement Requests List. */
		$requests = $urgent->paginateUrgent($pagenumber, $paginate_size);
		$this->f3->set('urgent', $requests);
		$details = new Details($this->db);
		$fbuser = new FBUser($this->db);
		$pics = array();
		$valid = new ValidationController;
		foreach($requests as $request)
		{
			if(!$valid->email($request->username))
			{
				array_push($pics, $fbuser->selectByUserName($request->username)[0]->pic);
				continue;
			}
			$selected = $details->selectByUserName($request->username);
			if(sizeOf($selected)==0 || $selected[0]->pic=='')
			{
				array_push($pics, $this->f3->get('PUBLIC_IMG_PATH').'anonymous_icon.jpg');
			}
			else
			{
				array_push($pics, $this->f3->get('UPLOAD_IMG_PATH').$selected[0]->pic);
			}
		}
		$this->f3->set('pics', $pics);
	}
	
	public function iconisePic($filename, $image_type)
	{
		//Dimensions
		list($width, $height) = getimagesize($filename);
		$new_width = 60;
		$new_height = 50;

		//$image_type = image_type_to_mime_type ( exif_imagetype($filename) );								//DEBUG

		// Resample
		$image_p = imagecreatetruecolor($new_width, $new_height);
		if($image_type == 'image/png')
		{
			$image_png = imagecreatefrompng($filename);
			imagecopyresampled($image_p, $image_png, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagepng($image_p, $filename);
		}
		else if($image_type == 'image/jpeg')
		{
			$image_jpeg = imagecreatefromjpeg($filename);
			imagecopyresampled($image_p, $image_jpeg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagejpeg($image_p, $filename);
		}
		else if($image_type == 'image/gif')
		{
			$image_gif = imagecreatefromgif($filename);
			imagecopyresampled($image_p, $image_gif, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
			imagegif($image_p, $filename);
		}
	}
}
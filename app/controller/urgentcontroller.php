<?php

class UrgentController extends Controller
{
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
		if($valid->captcha($captchacode,$random,false) || $valid->bloodgroup($bloodgroup) || $valid->fullname($name) || $valid->mobile($mobile) || $valid->location($location) || $valid->place($city,$state) || $valid->message($message))
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
}

?>
<?php

class RegisterController extends Controller
{
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
		if($valid->captcha($captchacode,$random,false) || $valid->userName($username) || $valid->name($firstname) || $valid->name($lastname) || $valid->passwordMatch($password1,$password2) || $valid->email($email) || $valid->gender($gender) || $valid->bloodgroup($bloodgroup) || $valid->place($city,$state) || $valid->date($day, $month, $year) || $valid->mobile($mobile) || ($pic['name']==''?false:$valid->image($pic)))
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
				$filepath = $this->f3->get('UPLOAD_IMG_PATH').$newname;
				move_uploaded_file($tempfilepath, $filepath);
				$functions = new FunctionController;
				$functions->iconisePic($filepath, $pic['type']);
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
			$this->auth->check_login($username, $password1);
			$this->f3->reroute('/wall?message=You%20have%20been%20successfully%20registered');
		}
	}
}

?>
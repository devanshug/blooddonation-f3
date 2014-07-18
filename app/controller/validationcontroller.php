<?php

class ValidationController extends Controller
{
	protected $error;
	public function userName($username)
	{
		$error = NULL;
		if(!(strlen($username) >= 4 && strlen($username) <= 10))
		{
			$error = 'UserName Length must be 4<=length(username)<=10';
		}
		if(!preg_match('/^[a-zA-Z]+$/', $username))
		{
			$error = 'Invalid UserName must match ^[a-zA-Z]+$';
		}
		$user = new User($this->db);
		if($user->exists($username))
		{
			$error = 'Username Exists';
		}
		$this->error = $error;
		return $error;
	}
	public function name($name)
	{
		$error = NULL;
		if(!(strlen($name) >= 2 && strlen($name) <= 10))
		{
			$error = 'Name length must be 2<=length(name)<=10';
		}
		if(!preg_match('/^[a-zA-Z]+$/', $name))
		{
			$error = 'Invalid Name must match ^[a-zA-Z]+$';
		}
		$this->error = $error;
		return $error;
	}
	public function fullname($fullname)
	{
		$error = NULL;
		if(!(strlen($fullname) >= 4 && strlen($fullname) <= 20))
		{
			$error = 'Name length must be 4<=length(name)<=20';
		}
		if(!preg_match('/^[a-zA-Z ]+$/', $fullname))
		{
			$error = 'Invalid Name must match ^[a-zA-Z ]+$';
		}
		$this->error = $error;
		return $error;
	}
	public function message($message)
	{
		$error = NULL;
		if(!(strlen($message) >= 10 && strlen($message) <= 50))
		{
			$error = 'Message length must be 10<=length(message)<=50';
		}
		$this->error = $error;
		return $error;
	}
	public function location($location)
	{
		$error = NULL;
		if(!(strlen($location) >= 5 && strlen($location) <= 50))
		{
			$error = 'Location length must be 5<=length(location)<=50';
		}
		$this->error = $error;
		return $error;
	}
	public function passwordMatch($password1, $password2)
	{
		$error = NULL;
		if($password1 != $password2)
		{
			$error = 'Passwords Entered do not match, Please check again.';
		}
		if(!preg_match('/^[a-zA-Z0-9]+$/', $password1))
		{
			$error = 'Password must match ^[a-zA-Z0-9]+$';
		}
		if(!(strlen($password1) >= 5 && strlen($password1) <= 10))
		{
			$error = 'Password length must be 5<=length(pass)<=10';
		}
		$this->error = $error;
		return $error;
	}
	public function email($email)
	{
		$error = NULL;
		if(!(strlen($email) >= 5 && strlen($email) <= 50))
		{
			$error = 'Email length must be 5<=length(email)<=50';
		}
		if(!preg_match('/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/', $email))
		{
			$error = 'Invalid Email must match ^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$';
		}
		$this->error = $error;
		return $error;
	}
	public function gender($gender)
	{
		$error = NULL;
		if($gender!='Male' && $gender!='Female')
		{
			$error = 'Gender must be Male or Female';
		}
		$this->error = $error;
		return $error;
	}
	public function bloodgroup($bloodgroup)
	{
		$error = NULL;
		$group = new BloodGroup($this->db);
		if(!$group->exists($bloodgroup))
		{
			$error = 'BloodGroup entered not found.';
		}
		$this->error = $error;
		return $error;
	}
	public function place($city, $state)
	{
		$error = NULL;
		$place = new City($this->db);
		if(!$place->exists($city, $state))
		{
			$error = 'Entered city ('.$city.') not found in state ('.$state.')';
		}
		$this->error = $error;
		return $error;
	}
	public function date($day, $month, $year)
	{
		$error = NULL;
		$day = intval($day);
		$month = intval($month);
		$year = intval($year);
		if($month<1 || $day<1 || $year<1)
		{
			$error = 'Wrong date';
		}
		else if($month==2)
		{
			if(($year%100==0 && $year%400!=0) || $year%4!=0)
			{
				if($day>28)
				{
					$error = 'Wrong date';
				}
			}
			else
			{
				if($day>29)
				{
					$error = 'Wrong date';
				}
			}
		}
		else if($month<=7 && $month>=1)
		{
			if($day>=31 && $month%2==0)
			{
				$error = 'Wrong date';
			}
			else if($day>=32 && $month%2==1)
			{
				$error = 'Wrong date';
			}
		}
		else if($month<=12 && $month>=8)
		{
			if($day>=31 && $month%2==1)
			{
				$error = 'Wrong date';
			}
			else if($day>=32 && $month%2==0)
			{
				$error = 'Wrong date';
			}
		}
		else
		{
			$error = 'Wrong date';
		}
		if(!$error)
		{
			/* Validating Date if the person's age is between 18 and 100 or not. */
			$currentDate = getDate();
			$curr_day = $currentDate['mday'];
			$curr_month = $currentDate['mon'];
			$curr_year = $currentDate['year'];
			if($year>1970)
			{
				$presenttimestamp = mktime(0, 0, 0, $curr_day, $curr_month, $curr_year-18);
				$datetimestamp = mktime(0, 0, 0, $day, $month, $year);
				if($presenttimestamp<$datetimestamp)
				{
					$error = 'Minimum age for blooddonation must be 18';
				}
			}
			if($year<$curr_year-100)
			{
				$error = 'Its better not to donate';
			}
		}
		$this->error = $error;
		return $error;
	}
	public function mobile($mobile)
	{
		$error = NULL;
		if(strlen($mobile)!=10)
		{
			$error = 'Mobile Number Length should be of 10 digit';
		}
		if(!preg_match('/^[0-9]+$/', $mobile))
		{
			$error = 'Invalid Number must match ^[0-9]+$';
		}
		$this->error = $error;
		return $error;
	}
	public function landline($landline)
	{
		$error = NULL;
		if(!(strlen($landline) >= 5 && strlen($landline) <= 15))
		{
			$error = 'Landline Number Length must be 5<=length(landline)<=15';
		}
		if(!preg_match('/^[0-9]+$/', $landline))
		{
			$error = 'Invalid Landline Number must match ^[0-9]+$';
		}
		$this->error = $error;
		return $error;
	}
	public function image($pic)
	{
		$error = NULL;
		if(!is_array($pic) || !array_key_exists('type',$pic) || !array_key_exists('size',$pic) || !array_key_exists('error',$pic) || !array_key_exists('tmp_name',$pic) || !array_key_exists('name',$pic))
		{
			$error = 'Image Parameter\'s Incorrect.';
		}
		else
		{
			$type = $pic['type'];
			$size = $pic['size'];
			$error_code = $pic['error'];
			$tmp_name = $pic['tmp_name'];
			$types = array('image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png');
			if(!file_exists($tmp_name))
			{
				$error = 'File Upload Not Successful';
			}
			if(!in_array($type, $types))
			{
				$error = 'Image File type doesn\'t exist.';
			}
			if($size>1000000 && $size<0)
			{
				$error = 'Image File size must be <1000000';
			}
			if($error_code!=0)
			{
				$error = 'Error Occurred During File Transmission : Error Code-'.$error_code;
			}
		}
		$this->error = $error;
		return $error;
	}
	public function captcha($password, $random, $canEcho=true)
	{
		$error = NULL;
		
		/* Captcha Api Object -> uses api from captchas.net */
		$captchas = new CaptchasDotNet('devanshug', '6HdMmG1EgKnJEOqHfQ5r9ywhkUbcM42UaeA0ffK1', $this->f3->get('CAP_API_PATH').'captchas',
									   '3600', 'abcdefghkmnopqrstuvwxyz0123456789', '6', '240', '80', '000088');
		
		/* Check whether the request contains correct random or not. */
		if ($canEcho && !$captchas->validate ($random))
		{
			$error = 'Invalid Random!!!';
			$return = true;
		}
		
		/* Check, that the right CAPTCHA password has been entered and
		/* return an error message otherwise. */
		if (!$captchas->verify($password, $random))
		{
			$error = 'Incorrect Captcha!!!';
			$return  = true;
		}
		else
		{
			$error = 'Verified Human!!!';
			$return = false;
		}
		$this->error = $error;
		
		if($canEcho)
			echo $error;
		else
			return $return;
	}
	public function getError()
	{
		return $this->error;
	}
	public function errorReset()
	{
		$this->error = NULL;
	}
}

?>
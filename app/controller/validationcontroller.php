<?php

class ValidationController extends Controller
{
	protected $error;
	public function userName($username)
	{
		$error = NULL;
		if(strlen($username) < 2)
		{
			$error = 'Doesn\'t sound like a valid username, please check!!!';
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
		//echo 'ERROR (In UserName) ->'.$error.'<br/>';									//DEBUG
		$this->error = $error;
		return $error;
	}
	public function name($name)
	{
		$error = NULL;
		if(strlen($name) < 2)
		{
			$error = 'Doesn\'t sound like a valid name, please check!!!';
		}
		if(!preg_match('/^[a-zA-Z]+$/', $name))
		{
			$error = 'Invalid Name must match ^[a-zA-Z]+$';
		}
		//echo 'ERROR (In Name) ->'.$error.'<br/>';										//DEBUG
		$this->error = $error;
		return $error;
	}
	public function fullname($fullname)
	{
		$error = NULL;
		if(strlen($fullname) < 2)
		{
			$error = 'Doesn\'t sound like a valid name, please check!!!';
		}
		if(!preg_match('/^[a-zA-Z ]+$/', $fullname))
		{
			$error = 'Invalid Name must match ^[a-zA-Z ]+$';
		}
		//echo 'ERROR (In FullName) ->'.$error.'<br/>';									//DEBUG
		$this->error = $error;
		return $error;
	}
	public function message($message)
	{
		$error = NULL;
		if(strlen($message) < 10)
		{
			$error = 'Message Length must be >=10';
		}
		//echo 'ERROR (In Message) ->'.$error.'<br/>';									//DEBUG
		$this->error = $error;
		return $error;
	}
	public function location($location)
	{
		$error = NULL;
		if(strlen($location) < 10)
		{
			$error = 'Location Message Length must be >=10';
		}
		//echo 'ERROR (In Message) ->'.$error.'<br/>';									//DEBUG
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
		if(strlen($password1)<5)
		{
			$error = 'Password size must be >= 5';
		}
		//echo 'ERROR (In Password) ->'.$error.'<br/>';									//DEBUG
		$this->error = $error;
		return $error;
	}
	public function email($email)
	{
		$error = NULL;
		if(!preg_match('/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/', $email))
		{
			$error = 'Invalid Email must match ^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$';
		}
		//echo 'ERROR (In Email) ->'.$error.'<br/>';									//DEBUG
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
		//echo 'ERROR (In Gender) ->'.$error.'<br/>';									//DEBUG
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
		//echo 'ERROR (In BloodGroup) ->'.$error.'<br/>';								//DEBUG
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
		//echo 'ERROR (In Place) ->'.$error.'<br/>';									//DEBUG
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
		}
		else if($month<=12 && $month>=8)
		{
			if($day>=31 && $month%2==1)
			{
				$error = 'Wrong date';
			}
		}
		else
		{
			$error = 'Wrong date';
		}
		//echo 'ERROR (In Date) ->'.$error.'<br/>';										//DEBUG
		$this->error = $error;
		return $error;
	}
	public function mobile($mobile)
	{
		$error = NULL;
		if(strlen($mobile)!=10)
		{
			$error = 'Number should be of 10 digit';
		}
		if(!preg_match('/^[0-9]+$/', $mobile))
		{
			$error = 'Invalid Number must match ^[0-9]+$';
		}
		//echo 'ERROR (In Mobile) ->'.$error.'<br/>';									//DEBUG
		$this->error = $error;
		return $error;
	}
	public function landline($landline)
	{
		$error = NULL;
		if(!preg_match('/^[0-9]+$/', $landline))
		{
			$error = 'Invalid Landline Number must match ^[0-9]+$';
		}
		echo 'ERROR (In Landline) ->'.$error.'<br/>';
		$this->error = $error;
		return $error;
	}
	public function image($pic)
	{
		$error = NULL;
		$type = $pic['type'];
		$size = $pic['size'];
		$error_code = $pic['error'];
		$types = array('image/gif', 'image/jpeg', 'image/jpg', 'image/pjpeg', 'image/x-png', 'image/png');
		if(!in_array($type, $types))
		{
			$error = 'Image File Type doesn\'t exist.';
		}
		if($size>1000000 && $size<0)
		{
			$error = 'Image File Size must be <1000000';
		}
		if($error_code!=0)
		{
			$error = 'Error Occurred During File Transmission : Error Code-'.$error_code;
		}
		echo 'ERROR (In Image) ->'.$error.'<br/>';
		$this->error = $error;
		return $error;
	}
	public function captcha_validation($password, $random, $canEcho=true)
	{
		$error = NULL;
		/* Captcha Api Object -> uses api from captchas.net */
		$captchas = new CaptchasDotNet('devanshug', '6HdMmG1EgKnJEOqHfQ5r9ywhkUbcM42UaeA0ffK1',
								   $this->f3->get('CAP_API_PATH').'captchas',
								   '3600','abcdefghkmnopqrstuvwxyz0123456789','6','240',
								   '80','000088');
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
}

?>
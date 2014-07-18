<?php

class InfoRouteController extends FunctionController
{
	/* Method is responsible for routing pages with route /@pagename/@value */
	public function router()
	{
		/* PARAMS.pagename is @pagename and PARAMS.value is @value */
		/* 'pagename' = It is name of the page which is asked to be loaded. */
		/* 'value' = It is the parameter related to which information is required. */
		$pagename = $this->f3->get('PARAMS.pagename');
		$value = $this->f3->get('PARAMS.value');
		
		if($pagename=='requests')
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
				$valid->captcha($password, $random);
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
}
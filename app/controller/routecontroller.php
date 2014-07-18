<?php

class RouteController extends FunctionController
{
	/* Method is responsible for routing pages with routes / and /@pagename */
	public function router()
	{
		/* Variable mostly common for all the Pages */

		/* VERB is System Variable provided by Fat Free Framework for getting type of method invoked. */
		/* 'method' = It is gets current HTTP REQUEST method type -> GET|POST */
		/* 'pagename' = It is name of the page which is to be loaded. */
		$method = $this->f3->get('VERB');
		$pagename = $this->f3->get('PARAMS.pagename');

		/* Login and Logout */

		/* Check if the user is logged in or not. */
		$logged_in = $this->hasLoggedIn();

		/* Passing Invalid HTTP Request */
		$this->isInvalidHTTPRequest($logged_in, $pagename, $method);

		
		/* Logging the user in if login is successful. */
		if($pagename=='login')
			$this->login();

		/* Logging the user out if logout is successful. */
		if($pagename=='logout')
			$this->logout();

		/* 'donorlist' = It is the Registered Donors List. */
		$custom = new Custom($this->db);
		$this->f3->set('donorlist', $custom -> getDonorList());

		/* 'banner' = It remains false for all pages except main index page.
		/* This variable is responsible for loading the banner in the main page. */
		$banner = false;
		if($pagename=='')
			$banner = true;
		$this->f3->set('banner', $banner);

		/* Index(Home for Unauthorised User) and Wall(Home for Authorised User) page.   */
		if($pagename=='' || $pagename=='wall')
		{
			if($method=='GET' && $this->f3->exists('GET.message'))
			{
				$this->f3->set('response_message', $this->f3->get('GET.message'));
			}
			$this->paginateRequests(0);
			$pagename = 'requests';
		}

		/* Register Page registers a user and Urgent files a blood request */
		if($pagename=='register' || $pagename=='urgent')
		{
			$this->setFormBasicInfo();
			$reset = true;
			if($method=='POST')
			{
				if($pagename=='register')
				{
					$register = new RegisterController;
					$reset = $register->register();
				}
				else if($pagename=='urgent')
				{
					$urgent = new UrgentController;
					$reset = $urgent->urgent();
				}
			}
			if($reset)
				$this->setDefaultFormField();
		}

		/* Create Final View and Adding content area for the page */
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
}
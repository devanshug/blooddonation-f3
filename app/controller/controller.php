<?php

class Controller {
	protected $f3;
	protected $db;
	protected $auth;
	protected $fb;
	function beforeRoute() {
	}
	function afterRoute() {
	}
	function __construct() {
		$f3=Base::instance();
		$db=new DB\SQL( $f3->get('db_dns') . $f3->get('db_name'),
						$f3->get('db_user'),
						$f3->get('db_pass')
					  );
		$auth = new Authenticate($f3, $db);
		$fb = new Facebook($f3, $db);
		$this->f3 = $f3;
		$this->db = $db;
		$this->auth = $auth;
		$this->fb = $fb;
	}
}

?>

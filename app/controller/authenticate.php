<?php

class Authenticate extends Auth {
	protected $f3;
	protected $db;
	
	public function __construct($f3, DB\SQL $db) {
		$mapper = new DB\SQL\Mapper($db,'auth_user');
		parent::__construct($mapper, array('id'=>'username', 'pw'=>'password'));
		$this->f3 = $f3;
		$this->db = $db;
	}
	
	public function check_login($id, $pass) {
		if($this->login($id,$pass))
		{
			$this->f3->set('SESSION.user', array('username'=>$id));
			return true;
		}
		return false;
	}
	
	public function check_loggedin() {
		if($this->f3->exists("SESSION.user"))
		{
			return true;
		}
		$this->f3->clear("SESSION.user");
		return false;
	}
	
	public function check_logout() {
		$this->f3->clear('SESSION.user');
	}

}

?>
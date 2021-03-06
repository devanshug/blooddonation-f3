<?php

class FBUser extends DB\SQL\Mapper {
 
    public function __construct(DB\SQL $db) {
        parent::__construct($db,'fbuser');
    }

    public function all() {
        $this->load();
        return $this->query;
    }
 
    public function add() {
        $this->copyFrom('fbuser');
        $this->save();
    }
	
	public function exists($username) {
		return $this->count(array('username=?', $username))>0;
	}
	
	public function getByEmail($username) {
		$this->load(array('username=?',$username));
		return $this->query;
    }
	
	public function selectByUsername($username) {
		return $this->select('pic', array('username=?',$username));
	}
 
    public function edit($username) {
        $this->load(array('username=?',$username));
        $this->copyFrom('fbuser');
        $this->update();
    }
 
    public function delete($username) {
        $this->load(array('username=?',$username));
        $this->erase();
    }
}
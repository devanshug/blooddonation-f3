<?php

class Details extends DB\SQL\Mapper {
 
    public function __construct(DB\SQL $db) {
        parent::__construct($db,'details');
    }

    public function all() {
        $this->load();
        return $this->query;
    }
 
    public function add() {
        $this->copyFrom('details');
        $this->save();
    }
	
	public function getByBloodgroup($bloodgroup) {
		return $this->count(array('bloodgroup=?',$bloodgroup));
    }
	
	public function getByUsername($username) {
		$this->load(array('username=?',$username));
		return $this->query;
    }
	
	public function selectByUsername($username) {
		return $this->select('pic', array('username=?',$username));
	}
	
    public function edit($id) {
        $this->load(array('id=?',$id));
        $this->copyFrom('POST');
        $this->update();
    }
 
    public function delete($id) {
        $this->load(array('id=?',$id));
        $this->erase();
    }
}
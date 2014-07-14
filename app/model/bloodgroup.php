<?php

class BloodGroup extends DB\SQL\Mapper {
 
    public function __construct(DB\SQL $db) {
        parent::__construct($db,'bloodgroup');
    }

    public function all() {
        $this->load();
		return $this->query;
    }
	
	public function exists($bloodgroup) {
		return $this->count(array('bloodgroup=?',$bloodgroup))>0;
	}
}

?>
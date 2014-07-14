<?php

class Urgent extends DB\SQL\Mapper {
 
    public function __construct(DB\SQL $db) {
        parent::__construct($db,'urgentblood');
    }

    public function all() {
        $this->load();
        return $this->query;
    }
	
	public function countall() {
		return $this->count();
	}
	
    public function add() {
        $this->copyFrom('urgent');
        $this->save();
    }
	
	public function paginateUrgent($pos, $size) {
		return $this->paginate($pos, $size, NULL, array('order' => 'id DESC'))['subset'];
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
<?php

class State extends DB\SQL\Mapper {
 
    public function __construct(DB\SQL $db) {
        parent::__construct($db,'state');
    }
	
	public function all() {
		$this->load();
		return $this->query;
    }
}
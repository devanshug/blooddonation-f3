<?php

class City extends DB\SQL\Mapper {
 
    public function __construct(DB\SQL $db) {
        parent::__construct($db,'city');
    }
	
	public function getByStateName($state_name) {
		$this->load(array('state_name=?',$state_name));
		return $this->query;
    }
	
	public function exists($city_name,$state_name=NULL) {
		if(!$state_name)
		{
			return $this->count(array('city_name=?',$city_name))>0;
		}
		echo $this->count(array('city_name=? and state_name=?',$city_name,$state_name));
		return $this->count(array('city_name=? and state_name=?',$city_name,$state_name))>0;
	}
}
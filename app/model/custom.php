<?php

class Custom{

	public function __construct(DB\SQL $db) {
		$this->db = $db;
	}

	public function getDonorList() {
		$bloodgroup = new Bloodgroup($this->db);
		$count = new Details($this->db);
		$donorlist = array();
		$groups = $bloodgroup -> all();
		foreach($groups as $group)
		{
			$donor = array('group'=>$group->bloodgroup, 'count'=>$count->getByBloodgroup($group->bloodgroup));
			array_push($donorlist, $donor);
		}
		return $donorlist;
	}
}

?>
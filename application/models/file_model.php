<?php
class File_model extends CI_Model {
	public function add_file($file_name)
	{
		$file=array(
			'file_path'=>'./uploads/'.$file_name);
		$this->db->insert('file',$file);
	}

	public function get_files()
	{
		$this->db->select('*');
		$this->db->from('file');
		$query=$this->db->get();
		return $query->result();
	}

	/*
	public function store()
	{
		for ($i=1; $i <424 ; $i++) { 
			$file=array(
			'file_path'=>'./uploads/'.$i.'.txt',
			'active'=>1
			);
			$this->db->insert('file',$file);
		}
		
	}
	*/
}
<?php

class File_model extends CI_Model {
	public function add_file($file_path)
	{
		$file=array(
			'file_path'=>$file_path);
		$this->db->insert('file',$file);
	}

	public function get_files()
	{
		$this->db->select('*');
		$this->db->from('file');
		$query=$this->db->get();
		return $query->result_array();
	}
}
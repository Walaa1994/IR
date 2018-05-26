<?php
class Doc_model extends CI_Model {
	public function add_doc($doc_name,$size)
	{
		$doc=array(
			'docPath'=>'./uploads/'.$doc_name,
			'size'=>$size,
			'indexing'=>0
			);
		$this->db->insert('doc',$doc);
	}

	public function get_docs()
	{
		$this->db->select('*');
		$this->db->from('doc');
		//لا تنسي تعدلي هون و تحولي لصفر
		$this->db->where('indexing',1);
		$query=$this->db->get();
		return $query->result();
	}

	public function get_unindex_doc_size()
	{
		$this->db->select('size');
		$this->db->from('doc');
		$this->db->where('indexing',0);
		$query=$this->db->get();
		$result=$query->result();
		$size=0;
		foreach ($result as $value) {
			$size+=$value->size;
		}
		return $size;
	}

	public function get_index_doc_size()
	{
		$this->db->select('size');
		$this->db->from('doc');
		$this->db->where('indexing',1);
		$query=$this->db->get();
		$result=$query->result();
		$size=0;
		foreach ($result as $value) {
			$size+=$value->size;
		}
		return $size;
	}

	public function get_doc_terms($docID)
	{
		$this->db->select('*');
		$this->db->from('tf');
		$this->db->where('docID',$docID);
		$query=$this->db->get();
		return $query->result();
	}

	public function get_term_id($term)
	{
		$this->db->from('dictionary');
		$this->db->where('term',$term);
		$query=$this->db->get();
		return $query->row();
	}

	public function convert_to_indexing($docs)
	{
		foreach ($docs as $value) {
			$data = array('indexing' => 1 );
			$this->db->where('docID',$value->docID);
			$this->db->update('document',$data);
		}
	}

	public function corpus_docs_count()
	{
		$this->db->from('doc');
		//here we count just indexed files
		$this->db->where('indexing',1);
		$query=$this->db->get();
		return $query->num_rows();
	}

	public function index_docs($index)
	{
		$dictionary=$index['dictionary'];
		foreach ($dictionary as $term => $DF) {
			$this->db->where('term',$term);
	    	$query = $this->db->get('dictionary');
	    	$result= $query->row();
	    	if ($query->num_rows() > 0){
	    		$term_id=$result->termID;
	    		
	    		$array=$DF['postings'];
				foreach ($array as $docID =>$tf) {
					$tf_record = array(
					'termID' => $term_id,
					'DocID'=>$docID,
					'tf'=>$tf['tf']
					 );
					$this->db->insert('tf',$tf_record);	
				}		
	    	}
			else  
			{
				$data = array('term' => $term );
				$this->db->insert('dictionary',$data);
				$term_id = $this->db->insert_id();
				$array=$DF['postings'];
				foreach ($array as $docID =>$tf) {
					$tf_record = array(
					'termID' => $term_id,
					'DocID'=>$docID,
					'tf'=>$tf['tf']
					 );
					$this->db->insert('tf',$tf_record);	
				}
			}
		}	
	}

	public function get_df($term_id)
	{
		$this->db->from('tf');
		$this->db->where('termID',$term_id);
		$query=$this->db->get();
		return $query->num_rows();
	}

	public function check_term($term)
	{
		$this->db->where('term',$term);
    	$query = $this->db->get('dictionary');
    	$result= $query->row();
	}

	public function check_lookup($term){
		$this->db->from('lookup');
		$this->db->where('value',$term);
		$query=$this->db->get();
	    $result=$query->num_rows();
	    if ($result>0) {
	    	$ref_id=$query->row()->refID;
	    	return $this->get_reference($ref_id)->value;
	    }
	    else
	    	return $term;
	}

	public function get_reference($ref_id){
		$this->db->select('value');
		$this->db->from('reference');
		$this->db->where('refID',$ref_id);
		$query=$this->db->get();
		return $query->row();
	}

	public function get_doc_path($docID)
	{
		$this->db->select('docPath');
		$this->db->from('document');
		$this->db->where('docID',$docID);
		$query=$this->db->get();
		return $query->row();
	}

	/*public function store()
	{
		for ($i=1; $i <424 ; $i++) { 
			$file_path='./uploads/'.$i.'.txt';
			$size=filesize($file_path);
			$file=array(
			'docPath'=>$file_path,
			'indexing'=>1,
			'size'=>$size
			);
			$this->db->insert('document',$file);
		}		
	}*/	
}
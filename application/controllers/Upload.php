<?php
ob_start();
include(APPPATH.'controllers/PDF2Text.php');
class Upload extends CI_Controller {
    function __construct()
    {
        parent::__construct();
     
        $this->load->database();
        $this->load->helper('url');
        /* ------------------ */ 
         
        $this->load->library('grocery_CRUD');
     
    }

    public function index()
    {
        
        $crud = new grocery_CRUD();
        $crud->set_table('document');
        $crud->fields('docPath');
        $crud->set_field_upload('docPath','uploads');
        $crud->unset_add();
        /*$crud->unset_edit();*/

        $output = $crud->render();

        $this->_example_output($output);
    }

    function _example_output($output = null)
     
    {
    //$this->load->view('template.php',$output);
        $data['subview'] = 'template.php';
        $data['output']=$output;    
        $this->load->view('admin.php',$data);    
    }

    function add_file(){
        $data['subview'] = 'upload.php';
        $data['output']='';    
        $this->load->view('admin.php',$data);
    }

    //upload pdf file and extract its contents and put them in the text file in the corpus
    public function do_upload()
    {
        if(isset($_FILES['userFile']))
        {
            $a = new PDF2Text();
            $a->setFilename($_FILES['userFile']['tmp_name']);
            $a->decodePDF();
            $data= $a->output();
            $filename = $_FILES['userFile']['name'];
            $file = basename($filename, '.pdf').'.txt';
            fopen('./uploads/'.$file, 'w');
            file_put_contents('./uploads/'.$file, $data);
            $size=filesize('./uploads/'.$file);
            $this->load->model('doc_model');
            $this->doc_model->add_doc($file,$size);

            //////1% Percentage handling
            $this->load->model('doc_model');
            $unindex_size=$this->doc_model->get_unindex_doc_size();
            $index_size=$this->doc_model->get_index_doc_size();
            $percent=( $unindex_size/$index_size)*100;
            if ($percent == 1 ) {
               require('token.php');
               $token = new Token();
               $token->getIndex();
            }
            //call grid page
            $this->index();
        }      
    }

    /*public function database()
    {
        $this->load->model('doc_model');
        $this->doc_model->store();
    }*/
}
?>
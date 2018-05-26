<?php
ob_start();
include(APPPATH.'controllers/PDF2Text.php');
class Upload extends CI_Controller {

    public function __construct()
    {
            parent::__construct();
    }

    public function index()
    {
            $this->load->view('upload', array('error' => ' ' ));
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
        }      
    }

    /*public function database()
    {
        $this->load->model('doc_model');
        $this->doc_model->store();
    }*/
}
?>
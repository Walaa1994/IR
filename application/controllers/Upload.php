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
                    $this->load->model('file_model');
                    $this->file_model->add_file($file);
                }
              
        }

        public function database()
        {
                $this->load->model('file_model');
                $this->file_model->store();
        }
}
?>
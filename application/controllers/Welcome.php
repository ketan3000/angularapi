<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends CI_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
        $this->load->helper('url');
        $this->load->library('aws3');
    }

    function index() {
        $headers = $this->input->request_headers();
        print_r($headers);
        //exit;
        
         $_POST = json_decode(file_get_contents('php://input'));
        print_r($_POST);
    }

    public function test_addbucket($name) {
        $return = $this->aws3->addBucket($name);
        var_dump($return);
    }

    public function upload() {
        //$config['upload_path'] = './uploads';
        //print_r($_FILES);exit;
        $image_data['file_name'] = $this->aws3->sendFile('ketan852147', $_FILES['userfile']);
        $data = array('upload_data' => $image_data['file_name']);
        $this->load->view('aws', $data);
    }
    
    public function clients() {
       
        $this->load->model("Model_event");
        $result = $this->Model_event->get_shoping_datatables();
        $output = array(
            "totalItems" => $this->Model_event->count_shoping_filtered(),
            "users" => $result,
        );
        echo json_encode($output);
        exit;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
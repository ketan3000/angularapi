<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pdfread extends CI_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
    }

    function index() {
        require_once APPPATH . 'third_party/vendor/autoload.php';
        $filename = base_url() . 'public/samples/1.pdf';
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filename);
        // Retrieve all pages from the pdf file.
        $pages = $pdf->getPages();
        foreach ($pages as $page) {
            echo $page->getText() . "</br>";
        }
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
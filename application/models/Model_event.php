<?php
class Model_event extends CI_Model {
    private $tablename, $userId, $roleId, $user_type, $company_id = "";
    var $cData; /* created user details (Array type) */
    var $uData;  /* updated user details (Array type) */
    var $order = array('ev.event_id' => 'desc');
    var $column_order = array('event_invoice', 'incident_id', 'part.client_title', 'clnt.client_title', 'gettype.title', 'devi.device_name', 'serv.service_description', 'ev.event_text', 'severity.severity', 'Duration', 'ev.event_start_time');
    var $column_order_history = array('event_invoice', 'part.client_title', 'clnt.client_title', 'gettype.title', 'devi.device_name', 'serv.service_description', 'ev.event_text', 'severity.severity', 'Duration', 'ev.event_start_time', 'ev.event_end_time');
    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    }
  
    
    /////////////////////////////////////////////////////////////////////////
    
      private function _get_shoping_datatables_query() {
//        $postdata = file_get_contents("php://input");
//        $_POST = json_decode($postdata, 'true');       
        $type = "";
        $permalink = array(2, 3);
        $this->db->select("*");
        $this->db->from('client');
        
    }
    public function get_shoping_datatables() {
        //echo "lskdf";exit;
        $this->_get_shoping_datatables_query();
        $limit = 10;        
        if ($_GET['page'] != -1){
            $total = $limit*$_GET['page'];
        }
        $this->db->limit($limit,$total);
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result();
    }
    public function count_shoping_filtered() {
        $this->_get_shoping_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    
 
}
?>
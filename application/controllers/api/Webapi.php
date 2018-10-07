<?php

defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

/**
 * DLF propkeep application
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Ketan kumar chauhan
 * @license         MIT
 * @link            http://52.39.23.134/dlfpropkeep8476/api/webapi/
 */
class Webapi extends REST_Controller {

    function __construct() {
// Construct the parent class
        parent::__construct();
        //$this->load->helper('email_moblie_helper');
        header('Access-Control-Allow-Origin: *');
        //file_get_contents('php://input');
    }

    /* start Custom function */

    public function authLogin_post() {
        //$_POST = json_decode(file_get_contents('php://input'));
        // print_r($this->post());exit;
        if ($this->post('password') != "" && $this->post('email') != "") {
            //echo "hello";exit;
            $email = $this->post('email');
            $password = md5($this->post('password'));
            $this->db->select('id,name, phone, token');
            $this->db->from('users');
            $this->db->where('password', md5($password));
            $this->db->where('email', $email);
            $this->db->where('active_status', 'Y');
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $rowResult = $query->row_array();
                $tokenData['id'] = $rowResult['token']; //TODO: Replace with data for token
                $tokenData['timestamp'] = time();
                $token = AUTHORIZATION::generateToken($tokenData);
                $this->set_response(['status' => TRUE, 'token' => $token], REST_Controller::HTTP_OK);
            } else {
                $this->set_response(['status' => FALSE, 'message' => 'User could not be found'], REST_Controller::HTTP_ACCEPTED); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response(['status' => FALSE, 'message' => 'Please enter required field'], REST_Controller::HTTP_ACCEPTED); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    public function editProfile_post() {
        //$_POST = json_decode(file_get_contents('php://input'));
        print_r($_FILES);
        print_r($this->post());
        exit;
    }

    public function checkAuthorization() {
        $headers = $this->input->request_headers();
        //print_r($headers);exit;
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {

            return AUTHORIZATION::validateTimestamp($headers['Authorization']);

            //print_r($data);
        } else {
            $ret['status'] = 'false';
            $ret['message'] = 'Token Not found';
            return $ret;
        }
    }

    public function users_get() {
        $autha = $this->checkAuthorization();
        if ($autha['status'] == "false") {
            $this->response(['status' => $autha['status'], 'message' => $autha['message']], REST_Controller::HTTP_UNAUTHORIZED); // BAD_REQUEST (400) being the HTTP response code
        } else {
            $userUUIDid = $autha['token']->id;
            if ($userUUIDid != "") {
                $this->db->select('*');
                $this->db->from('users');
                $this->db->where('token', $userUUIDid);
                $query = $this->db->get();
                if ($query->num_rows() > 0) {
                    $userRecord = $query->row();
                    $this->db->select('*');
                    $this->db->from('category');
                    $queryCatetogy = $this->db->get();
                    $catetogy = array();
                    if ($queryCatetogy->num_rows() > 0) {
                        $catetogy = $queryCatetogy->result();
                    }
                    $this->db->select('*');
                    $this->db->from('gender');
                    $queryGender = $this->db->get();
                    $gender = array();
                    if ($queryGender->num_rows() > 0) {
                        $gender = $queryGender->result();
                    }
                    $this->set_response(['status' => TRUE, 'gender' => $gender, 'category' => $catetogy, 'resultSet' => $userRecord], REST_Controller::HTTP_OK);
                } else {
                    $this->set_response(['status' => FALSE, 'message' => 'User could not be found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
                }
            } else {
                $this->response(['status' => FALSE, 'message' => 'UserId not found'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }
        }
    }

    public function category_get() {
        $this->db->select('*');
        $this->db->from('category');
        $query = $this->db->get();
        //echo $this->db->last_query();exit;
        if ($query->num_rows() > 0) {
            $resultSet = $query->result();
            // print_r($userRecord);
            $this->set_response(['status' => TRUE, 'resultSet' => $resultSet], REST_Controller::HTTP_OK);
        } else {
            $this->set_response(['status' => FALSE, 'message' => 'User could not be found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function subcategory_get() {
        $catslug = $this->get('category');
        if ($catslug != "") {
            $this->db->select('sc.id,sc.name,sc.url_slug');
            $this->db->from('sub_categroy sc');
            $this->db->join('category ct', 'ct.id = sc.cat_id');
            $this->db->where('ct.url_slug', $catslug);
            $query = $this->db->get();
            //echo $this->db->last_query();exit;
            if ($query->num_rows() > 0) {
                $resultSet = $query->result();
                // print_r($userRecord);
                $this->set_response(['status' => TRUE, 'resultSet' => $resultSet], REST_Controller::HTTP_OK);
            } else {
                $this->set_response(['status' => FALSE, 'message' => 'User could not be found'], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response(['status' => FALSE, 'message' => 'catagory name not found'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    private function totalProduct($catslug) {
        
    }

    public function products_get() {
        $catslug = $this->get('category');
        $offset = $this->get('offset');
        $baseURL = base_url() . 'public/product_image/';
        if ($catslug != "") {
            $limit = '4';

            $this->db->select('pro.Name,pro.url_slug,pro.images,pro.quantity,pro.price,ct.cat_name,sc.name,sc.url_slug as subcat_slug,co.color');
            $this->db->from('procucts pro');
            $this->db->join('sub_categroy sc', 'sc.id = pro.sub_cat_id');
            $this->db->join('category ct', 'ct.id = pro.cat_id');
            $this->db->join('color co', 'co.color_id = pro.color_id');
            $this->db->where('ct.url_slug', $catslug);
            $this->db->limit($offset, '0');
            $query = $this->db->get();
            //echo $this->db->last_query();exit;
            if ($query->num_rows() > 0) {
                $resultSet = $query->result();
                $lastRow = $query->num_rows();
                $nextOffset = $offset + $limit;
                if ($offset > $lastRow) {
                    $nextOffset1 = -1;
                } else {
                    $nextOffset1 = $nextOffset;
                }

                $this->set_response(['status' => TRUE, 'nextOffset' => $nextOffset1, 'baseURL' => $baseURL, 'resultSet' => $resultSet], REST_Controller::HTTP_OK);
            } else {
                $this->set_response(['status' => FALSE,'nextOffset' => -1, 'message' => 'User could not be found'], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response(['status' => FALSE,'nextOffset' => -1, 'message' => 'catagory name not found'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    public function productsSearchMenu_get() {
        $catslug = $this->get('category');

        if ($catslug != "") {
            $this->db->distinct();
            $this->db->select('sc.id,sc.url_slug,sc.name');
            $this->db->from('procucts pro');
            $this->db->join('category ct', 'ct.id = pro.cat_id');
            $this->db->join('sub_categroy sc', 'sc.id = pro.sub_cat_id');
            $this->db->where('ct.url_slug', $catslug);
            $querySubCategory = $this->db->get();

            $this->db->distinct();
            $this->db->select('co.color_id,co.color');
            $this->db->from('procucts pro');
            $this->db->join('category ct', 'ct.id = pro.cat_id');
            $this->db->join('color co', 'co.color_id = pro.color_id');
            $this->db->where('ct.url_slug', $catslug);
            $queryColor = $this->db->get();

            $this->db->distinct();
            $this->db->select('id,cat_name,url_slug as category_slug');
            $this->db->from('category');
            $this->db->where_not_in('url_slug', $catslug);
            $queryCategory = $this->db->get();

            $this->db->select('MAX(pro.price) as maxamount');
            $this->db->from('procucts pro');
            $this->db->join('category ct', 'ct.id = pro.cat_id');
            $this->db->where('ct.url_slug', $catslug);
            $queryMaxPrice = $this->db->get();


            $sub_category = $category = $color = $maxAmount = array();
            if ($queryMaxPrice->num_rows() > 0) {
                $max = $queryMaxPrice->row();
                $maxAmount = $max->maxamount + 1000;
            }

            if ($querySubCategory->num_rows() > 0) {
                $sub_category = $querySubCategory->result();
            }

            if ($querySubCategory->num_rows() > 0) {
                $category = $queryCategory->result();
            }

            if ($queryColor->num_rows() > 0) {
                $color = $queryColor->result();
            }
            $this->set_response(['maxAmount' => $maxAmount, 'category' => $category, 'sub_category' => $sub_category, 'color' => $color], REST_Controller::HTTP_OK);
        } else {
            $this->response(['status' => FALSE, 'message' => 'catagory name not found'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    public function addUser_post() {

        if ($this->post('name') != "" && $this->post('phone') != "" && $this->post('password') != "" && $this->post('email') != "") {
            $postUserData = array(
                'name' => $this->post('name'),
                'phone' => $this->post('phone'),
                'email' => $this->post('email'),
                'password' => md5($this->post('password')),
                'created_on' => date('Y-m-d H:i:s'),
            );
            if ($this->db->insert('users', $postUserData)) {
                $user_id = $this->db->insert_id();
                $this->set_response(['status' => TRUE, 'lastId' => $user_id, 'message' => 'Data Inserted Successfully'], REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
            } else {
                $this->response(['status' => FALSE, 'message' => 'Something wents wrong !!'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }
        } else {
            $this->response(['status' => FALSE, 'message' => 'Please enter required field'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    public function updateUser_put() {
        if ($this->put('name') != "" && $this->put('phone') != "" && $this->put('id') != "" && $this->put('email') != "") {
            $id = $this->put('id');
            $postUserData = array(
                'name' => $this->put('name'),
                'phone' => $this->put('phone'),
                'email' => $this->put('email'),
                'updated_on' => date('Y-m-d H:i:s'),
            );
            $this->db->where('id', $id);
            if ($this->db->update('users', $postUserData)) {
                $this->set_response(['status' => TRUE, 'message' => 'Data Updated Successfully'], REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
            } else {
                $this->response(['status' => FALSE, 'message' => 'Something wents wrong !!'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }
        } else {
            $this->response(['status' => FALSE, 'message' => 'Please enter required field'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    public function deleteUser_delete($id) {

        if ($id) {
            //delete post           
            $this->db->where('id', $id);
            if ($this->db->delete('users')) {
                //set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'User has been removed successfully.'
                        ], REST_Controller::HTTP_OK);
            } else {
                //set the response and exit
                $this->response("Some problems occurred, please try again.", REST_Controller::HTTP_BAD_REQUEST);
            }
        } else {
            //set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'No user were found.'
                    ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

}

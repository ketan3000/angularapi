<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

/*
 * Changes:
 * 1. This project contains .htaccess file for windows machine.
 *    Please update as per your requirements.
 *    Samples (Win/Linux): http://stackoverflow.com/questions/28525870/removing-index-php-from-url-in-codeigniter-on-mandriva
 *
 * 2. Change 'encryption_key' in application\config\config.php
 *    Link for encryption_key: http://jeffreybarke.net/tools/codeigniter-encryption-key-generator/
 * 
 * 3. Change 'jwt_key' in application\config\jwt.php
 *
 */

class Auth extends REST_Controller {

    function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    }

    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: GET
     */
    public function token_get() {
        $tokenData['id'] = 1; //TODO: Replace with data for token
        $tokenData['timestamp'] = time();
        $output['token'] = AUTHORIZATION::generateToken($tokenData);
        $this->set_response($output, REST_Controller::HTTP_OK);
    }

    /**
     * URL: http://localhost/CodeIgniter-JWT-Sample/auth/token
     * Method: POST
     * Header Key: Authorization
     * Value: Auth token generated in GET call
     */
    public function token_post() {
        $headers = $this->input->request_headers();
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            print_r($decodedToken);
            if ($decodedToken != false) {
                $this->set_response($decodedToken, REST_Controller::HTTP_OK);
                return;
            }
        }
        $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
    }

    public function validtoken_post() {
        $headers = $this->input->request_headers();
        $decodedToken = AUTHORIZATION::validateTimestamp($headers['Authorization']);
        print_r($decodedToken);
        exit;
    }

    public function signup_post() {
        if ($this->post('name') != "" && $this->post('email') != "" && $this->post('password') != "") {
            $postUserData = array(
                'name' => $this->post('name'),
                'email' => $this->post('email'),
                'password' => md5($this->post('password')),
                'uuid' => uniqid(),
            );
            $this->db->select('uuid');
            $this->db->from('users');
            $this->db->where('email', $this->post('email'));           
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $this->response(['status' => FALSE, 'message' => 'Email Id already Exists !!'], REST_Controller::HTTP_OK); // BAD_REQUEST (400) being the HTTP response code
            } else {
                if ($this->db->insert('users', $postUserData)) {
                    $user_id = $this->db->insert_id();
                    $this->set_response(['status' => TRUE, 'lastId' => $user_id, 'message' => 'User Inserted Successfully'], REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code
                } else {
                    $this->response(['status' => FALSE, 'message' => 'Something wents wrong !!'], REST_Controller::HTTP_OK); // BAD_REQUEST (400) being the HTTP response code
                }
            }
        } else {
            $this->response(['status' => FALSE, 'message' => 'Please enter required field'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    private function generateToken($payload) {
        $tokenData = array();
        $tokenData['payload'] = $payload;
        $tokenData['timestamp'] = time();
        return AUTHORIZATION::generateToken($tokenData);
    }

    public function login_post() {
        if ($this->post('email') != "" && $this->post('password') != "") {
            $this->db->select('uuid');
            $this->db->from('users');
            $this->db->where('email', $this->post('email'));
            $this->db->where('password', md5($this->post('password')));
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $userRecord = $query->row();
                $token = $this->generateToken($userRecord->uuid);
                $this->set_response(['status' => TRUE, 'token' => $token], REST_Controller::HTTP_OK);
            } else {
                $this->set_response(['status' => FALSE, 'message' => 'Email or Password not valid'], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }
        } else {
            $this->response(['status' => FALSE, 'message' => 'Please enter required field'], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }
    }

    private function isvalidtoken() {
        $headers = $this->input->request_headers();
        //pr($headers);
        if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
            $decodedToken = AUTHORIZATION::validateToken($headers['Authorization']);
            if ($decodedToken != false) {
                $validTimeToken = AUTHORIZATION::validateTimestamp($headers['Authorization']);
                if ($validTimeToken['status'] == 'true') {
                    $return['payload'] = $validTimeToken['payload'];
                    $return['error_type'] = REST_Controller::HTTP_OK;
                } else {
                    $return['error_type'] = REST_Controller::HTTP_UNAUTHORIZED;
                }
            } else {
                $return['error_type'] = REST_Controller::HTTP_UNAUTHORIZED;
            }
        } else {

            $return['error_type'] = REST_Controller::HTTP_UNAUTHORIZED;
        }
        return $return;
    }

    public function isvalidPage_get() {
        $tokenValid = $this->isvalidtoken();
        if ($tokenValid['error_type'] == '200') {
            $token = $this->generateToken($tokenValid['payload']);
            $this->set_response(['status' => TRUE, 'token' => $token], REST_Controller::HTTP_OK);
        } else {
            $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

    public function dashboard_get() {
        $tokenValid = $this->isvalidtoken();
        if ($tokenValid['error_type'] == '200') {
            $token = $this->generateToken($tokenValid['payload']);
            $this->set_response(['status' => TRUE, 'token' => $token], REST_Controller::HTTP_OK);
        } else {
            $this->set_response("Unauthorised", REST_Controller::HTTP_UNAUTHORIZED);
        }
    }

}

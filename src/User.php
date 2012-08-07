<?php
/**
 * Data Class of Users for communication with Kerio Operator
 *
 * @author FPSquad
 */
require(dirname(__FILE__) . '\../src_Kerio/KerioOperatorApi.php');
require_once 'config.php';

class User{
    
    
    var $api;
    var $config;

    public function __construct() {
        try {
            $this->api = new KerioOperatorApi('FPSquad', 'Sprava uzivatelu a linek', '1.0');
            $this->api->setJsonRpc('2.0', 443, '/myphone/api/jsonrpc/');
            $this->config = new config();
        } catch (Exception $exc) {
            printf('Server communication error. Try again later.');
        }  
    }
  
    
    /**
     * Information about logged user
     * @return structure of User
     */
    function getUserInfo() {
        try {
            return $this->api->sendRequest('Session.whoAmI');    
        } catch (Exception $exc){
            printf('Server communication error. Try again later.');
        }
    }
    
    /**
     * Load user detail by ID
     * @param $id - ID of user
     * @return array with user details
     */
    function getUserByIdDB($id) {
        include 'connect.php';
        $sql = "SELECT *
                FROM user
                WHERE id = $id 
                ";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        return $row;
    }
    
    /**
     * Load users details from local database
     * @return list of user
     */
    function getUsersDetailDB() {
        include 'connect.php';
        $sql = "SELECT *
                FROM user, line
                WHERE line.user_id = user.id
                ORDER BY fullname 
                ";
        $result = mysql_query($sql);
        
        $last_name = '';
        $y = 0;
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
           
            if($row['fullname'] != $last_name){
                $y = 0;
                $userList['userList'][$i]['FULL_NAME'] = $row['fullname'];
                $userList['userList'][$i]['EXTENSIONS'][$y]['TEL_NUM'] = $row['number'];
                $y++;
            }else{
                $i--;
                $userList['userList'][$i]['EXTENSIONS'][$y]['TEL_NUM'] = $row['number'];
                $y++;
            }


            $last_name = $row['fullname'];
            $i++;
            
        }
        $userList['totalItems']= $i;
        
        
        return $userList;
    }
    
    /**
     * Get information about users and their telephone lines from Kerio Operator
     * @return structure of User with extensions 
     */
    function getUsersDetail() {
        
        $params = array('query' => array(
          'fields' => array('FULL_NAME', 'USERNAME', 'EMAIL', 'EXTENSIONS'),                                            
          'orderBy' => array(array(                                                         
            'columnName' => 'FULL_NAME',                                                     
            'direction' => 'Asc'                                                          
          ))                                                                              
        ));
        
        try {
            
            $this->api->login($this->config->server_ip, $this->config->admin_login, $this->config->admin_password);
            $result = $this->api->sendRequest('Users.get', $params);   
        } catch (Exception $exc) {
            die('Server communication error.'.$exc);
        }
        //$this->setGenerateTime(1);
        //$this->setUsersDetailDB($result);
        return $result;
    }
    
    /**
     *
     * @param $tab_id 1 - telephone list
     *                2 - free lines list
     */
    function setGenerateTime($tab_id) {
         include 'connect.php';
         $time = Time();
        
         $sql = "DELETE FROM date
                 WHERE id = '".$tab_id."'";
         mysql_query($sql);
         
         $sql = "INSERT INTO date (id, time) 
                 VALUES ('".$tab_id."','".$time."')";
         $result = mysql_query($sql);
    }
    
    /**
     * Delete all data from line
     */
    function deleteUsersDB() {
        include 'connect.php';
        $sql = "DELETE FROM user
                ";
        $result = mysql_query($sql);
        if (!$result) {
          echo "SQL server returns: ".mysql_error();
          exit ("<br />processing query error, quit");
        }
    }

    
    /**
     * Insert new users to local database
     * @param type $data - array of users
     */
    function setUsersDetailDB ($data) {
        include 'connect.php';
        
        $count = $data['totalItems'];
        for ($i = 0; $i < $count; $i++) {
            $id = $data['userList'][$i]['GUID'];
            $login = $data['userList'][$i]['USERNAME'];
            $fullname = $data['userList'][$i]['FULL_NAME'];
            $email = $data['userList'][$i]['EMAIL'];

            if (!$this->checkEntryDB('user', $id) && $data['userList'][$i]['EXTENSIONS'] != null) {
                $sql = "INSERT INTO user (id, login, fullname, email) 
                    VALUES ('".$id."','".$login."','".$fullname."','".$email."')";
                $result = mysql_query($sql);
                if (!$result) {
                  echo "SQL server reports: ".mysql_error();
                  exit ("<br />processing query error, quit");
                }
            }
            
            for ($y = 0; $y < count($data['userList'][$i]['EXTENSIONS']); $y++) {
                $line_id = $data['userList'][$i]['EXTENSIONS'][$y]['GUID'];
                $number = $data['userList'][$i]['EXTENSIONS'][$y]['TEL_NUM'];
                $description = $data['userList'][$i]['EXTENSIONS'][$y]['DESCRIPTION'];
                $status = '1';
                
                if (!$this->checkEntryDB('line', $line_id)) {
                    $sql = "INSERT INTO line (id, user_id, number, description, status) 
                        VALUES ('".$line_id."','".$id."','".$number."','".$description."','".$status."')";
                    $result = mysql_query($sql);
                    if (!$result) {
                      echo "SQL server vrací: ".mysql_error();
                      exit ("<br />processing query error, quit");
                    }
                }
            }
        } 
    }
    
    /**
     * Check if exist user in local database
     * @param type $id - identification of user
     * @return type boolean
     */
    function checkEntryDB($tab, $id) {
        include 'connect.php';
        
        $sql = "SELECT *  
                    FROM $tab
                    WHERE id = '".$id."'
                    ";
                    
           
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        //echo $row['id'];
        if(is_numeric($row['id'])) {
            return true;
        }else {
            return false;
        }
    }
    

    /**
     * Search user id by username
     * @param $username
     * @return id of user
     */
    function getUserIdByUsername($username) {
        $guid = 0;
        $users = $this->getUsersDetail();
        $count = $users['totalItems'];
        for ($i = 0; $i < $count; $i++) {
            if($users['userList'][$i]['USERNAME'] == $username) {
                $guid = $users['userList'][$i]['GUID'];
                $i = $count;
            }
        }
        return $guid;
    }


    
    /**
     * Authentication of user
     * @param  $username - login
     * @param  $password 
     */
    function loginMyphone($username, $password) {
        //echo 'loguju uzivatele '.$username.' s heslem '.$password;
        try {
            $this->api->login($this->config->server_ip, $username, $password);
            $info = $this->getUserInfo();

            $_SESSION['login'] = $username;
 
        } catch (Exception $exc) {
            die('Invalid username or password.');
        } 
    }
  
}

?>

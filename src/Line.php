<?php
/**
 * Telephone Lines
 *
 * @author FPSquad
 */
require_once(dirname(__FILE__) . '\../src_Kerio/KerioOperatorApi.php');

class Line {
    
    var $api;
    var $config;
    
    public function __construct() {
        try {
            $this->api = new KerioOperatorApi('FPSquad', 'Sprava uzivatelu a linek', '1.0');
            $this->api->setJsonRpc('2.0', 443, '/myphone/api/jsonrpc/');
            $this->config = new config();
        } catch (Exception $exc) {
            die('Server communication error. Try again later.');
        }  
 
    }
    
    /**
     * Get list of telephone lines 
     * @return structure of Extensions
     */
    function getTelephoneLines() {
        $params = array(
            'query' => array(
                'fields' => array('TEL_NUM', 'DESCRIPTION','USERS_ID'), 
                'orderBy' => array(array(
                'columnName' => 'TEL_NUM',
                'direction' => 'Asc'
            ))
        ));
        
        try {
            $this->api->login($this->config->server_ip, $this->config->admin_login, $this->config->admin_password);
            $result = $this->api->sendRequest('Extensions.get', $params);
        } catch (Exception $exc) {
            die('Server communication error. Try again later.');
        }
        //$this->setGenerateTime(2);
        //$this->setTelephoneLinesDB($result);
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
         //echo $time;
         $sql = "DELETE FROM date
                 WHERE id = '".$tab_id."'";
         mysql_query($sql);
         
         $sql = "INSERT INTO date (id, time) 
                 VALUES ('".$tab_id."','".$time."')";
         $result = mysql_query($sql);
    }
    
    /**
     * Load generation time
     * @param $tab_id - ID of table 
     * @return time 
     */
    function getGenerateTime($tab_id) {
        include 'connect.php';
        
        $sql = "SELECT * FROM date
                 WHERE id = '".$tab_id."'";
        $result = mysql_query($sql);
        if (!$result) {
            echo "SQL server reports: ".mysql_error();
            exit ("<br />processing query error, quit");
        }else{
            $row = mysql_fetch_array($result, MYSQL_ASSOC);
            return $row['time'];
        } 
    }
    
    /**
     * Load information about telephone lines from local database
     * @return List of lines with details
     */
    function getTelephoneLinesDB() {
        include 'connect.php';
        
        $sql = "SELECT *
                FROM line
                WHERE status = 0
                ORDER BY number 
                ";
        $result = mysql_query($sql);
        if (!$result) {
            echo "SQL server returns: ".mysql_error();
            exit ("<br />processing query error, quit");
        }
        $i = 0;
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $lineList['sipExtensionList'][$i]['TEL_NUM'] = $row['number'];
            $lineList['sipExtensionList'][$i]['DESCRIPTION'] = $row['description'];
            $i++;
            
        }
        $lineList['totalItems'] = $i;

        return $lineList;   
    }
    
    
    function getTelephoneLineByIdDB($id) {
        include 'connect.php';
        $sql = "SELECT *
                FROM line
                WHERE id = $id 
                ";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        return $row;

    }
    
    /**
     * Check if exist user in local database
     * @param type $id - identification of user
     * @return 0 - user doesn't exist,
     *         1 - wrong users detail,
     *         2 - users detail OK      
     */
    function checkEntryDB($tab, $id, $status) {
        include 'connect.php';
        
        $sql = "SELECT *  
                    FROM $tab
                    WHERE id = '".$id."'
                    ";
                    
           
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result, MYSQL_ASSOC);
        
        //echo $row['id'];
        if(is_numeric($row['id']) && $status != $row['status']) {
            return 1;
        }elseif (!is_numeric($row['id'])) {
            return 0;
        }else{
            return 2;
        }
    }
    
    
    /**
     * Delete all data from line
     */
    function deleteLinesDB() {
        include 'connect.php';
        $sql = "DELETE FROM line
                ";
        $result = mysql_query($sql);
        if (!$result) {
          echo "SQL server returns: ".mysql_error();
          exit ("<br />processing query error, quit");
        }
    }
   
    
    /**
     * Insert new lines to local database
     * @param $data - array of lines
     */
    function setTelephoneLinesDB($data) {
        include 'connect.php';
        
        
        $count = $data['totalItems'];
        for ($i = 0; $i < $count; $i++) {
            $id = $data['sipExtensionList'][$i]['GUID'];
            $user_id = $data['sipExtensionList'][$i]['USERS_ID'];
            $number = $data['sipExtensionList'][$i]['TEL_NUM'];
            $description = $data['sipExtensionList'][$i]['DESCRIPTION'];

            if($data['sipExtensionList'][$i]['USERS_ID'] == ''){
                $status = 0;
            } else{
                $status = 1;
            }

            if ($this->checkEntryDB('line', $id, $status) == 1) {
                $sql = "UPDATE line SET 
                        status = $status, description = '".$description."'
                        WHERE id = $id
                    ";
                $result = mysql_query($sql);
                if (!$result) {
                  echo "SQL server returns: ".mysql_error();
                  exit ("<br />processing query error, quit");
                }

            }elseif ($this->checkEntryDB('line', $id, $status) == 0) {

                $sql = "INSERT INTO line (id, user_id, number, description, status) 
                    VALUES ('".$id."','".$user_id."','".$number."','".$description."','".$status."')";
                $result = mysql_query($sql);
                if (!$result) {
                  echo "SQL server returns: ".mysql_error();
                  exit ("<br />processing query error, quit");
                }
            } 
        }
        $this->setGenerateTime(2);
    }
    
    
    /**
     * Set free line to selected user
     * @param  $UserGuid - user ID
     * @param  $LinkGuid - line ID
     * @param  $description - description of line
     */
    function setLineToUser($UserGuid,$LinkGuid,$description){

        $params = array('detail' => array(
                'CALL_PERMISSION' => 1,
                'DESCRIPTION' => $description,
                'CALLER_ID' => "",
                'CALLER_ID_TYPE' => 0,
                'USER_GUID' => (int)$UserGuid,
                'MULTIPLE_REGISTRATION_ENABLED' =>  false,
                'MULTIPLE_REGISTRATION_COUNT' =>  1,
                'NAT_SUPPORT' =>  false,
                'SIP_PASSWORD' =>  "aaa"
                ),
                'guid' => (int)$LinkGuid
            );
        try {
            $session = $this->api->login($this->config->server_ip, $this->config->admin_login, $this->config->admin_password);
            $this->api->sendRequest('Extensions.set', $params);      
        } catch (Exception $exc) {
            printf('Server communication error. Try again later.');
        }
        
        $this->getTelephoneLines();
        

        
    
    }
    
}

?>

<?php

/**
 * Authentization class
 * Rights - 1=admin, 2=user, 3=not-logged
 */
class Auth{
    
    var $rights;
    var $config;
    
    function __construct() {
       
        $this->config = new config();
    }
    
    /**
     * Select logged user from database
     * @return rights
     */
    function selectUser() {
        $admin_users = $this->config->admin_users;
        
        for ($i = 0; $i < count($admin_users); $i++) {
            if ($_SESSION['login'] == $admin_users[$i]) {
                $this->rights = 1;
                break;
            }  else {
                $this->rights = 2;
            }
        }  
    }
    
    /**
     * Authentization of ADMIN
     * @return boolean
     */
    function authAdmin() {
        $this->selectUser();
        if ($this->rights == 1){
            return true;
        }else {
            return false;
        } 
    }
    
    /**
     * Authentization of USER
     * @return boolean 
     */
    function authUser() {
        $this->selectUser();
        if ($this->rights == 2){
            return true;
        }else {
            return false;
        }
    }
    
    
}




?>

<?php
/**
 * For configuration variables in Application 
 */

class config{
    
    /**
     * User with read/write rights in Kerio Operator 
     * and IP address of Kerio Operator
     */
    var $admin_login;
    var $admin_password;
    var $server_ip;
    
    /**
     * Settings of MySQL server
     */
    var $mysql_server_ip;
    var $mysql_user_login; 
    var $mysql_user_password;
    var $mysql_database; // name of database
    
    /**
     * Array with login names
     * Set admin rights in this application
     */
    var $admin_users;
    
    /**
     * Local settings
     */
    function __construct() {
        $this->admin_login = 'admin';
        $this->admin_password = 'admin';
        $this->server_ip = '192.168.225.101';
        
        $this->admin_users = array('admin', 'merat', 'ahemsky');
        
        $this->mysql_server_ip = '127.0.0.1';
        $this->mysql_user_login = 'root';
        $this->mysql_user_password = '';
        $this->mysql_database = 'kerio_sprava_uzivatelu_a_linek';
    }
    
}



?>

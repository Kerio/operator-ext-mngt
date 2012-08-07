<?php
/**
 * Connection settings to local MySQL database 
 */

$config = new config();
$connect = mysql_connect($config->mysql_server_ip, $config->mysql_user_login, $config->mysql_user_password) or die ("database communication error");
mysql_select_db($config->mysql_database) or die("Database select error");

?>

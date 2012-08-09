External administration of users and extensions
=================

A tiny application which would allow any person to administer users and extensions on the Kerio Operator server without the need to authenticate at the web administration.

The application was developed by a students of the [ZČU Plzeň](http://www.zcu.cz/en/) (University of West Bohemia in Pilsen) as a thesis on regular basic. 

For more information see [http://www.kerio.com/blog/kerio-api-university](http://www.kerio.com/blog/kerio-api-university)

Setting up the application
----------------

Before you start, you need to configure the config.php file, which is in the main application directory. It includes settings for connecting to Kerio Operator and local MySQL database.

Kerio Operator
----------------

    $admin_login  Administrator login, the existing account in Kerio Operator with read and write
    $admin_password - Password of the account
    $server_ip - IP address of the server running Kerio Operator
    $admin_users - a list of authorized user (see chapter 3 the distribution of rights of access)

Database
----------------

Setting up the MySQL database tables can be imported from a file 'kerio_sprava_uzivatelu_a_linek.sql' from the "database" or manually set the database as described in main documentation.

	$mysql_server_ip - IP address of the MySQL server
	$mysql_user_login - login to connect to database
	$mysql_user_password - password to connect to database
	$mysql_database - database name

License
----------------

This software is provided under BSD license as is and without any express or implied warranties, including, without limitation, the implied warranties of merchantability and fitness for a particular purpose.
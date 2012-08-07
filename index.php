<?php
    @session_start();
    require_once(dirname(__FILE__) . '/src/Control.php');
    $view = new Control();
 
  ?>  
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"    
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">  
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs" lang="cs"> 
 
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="Kerio - External management of users and telephone lines" />
    <meta name="author" content="ZSWI project ZČU Plzeň - team FPSquad" />  
    
    <title> 
    <?php
      
      
                    
      @$stranka = $_GET['stranka']; 
      
      switch ($stranka) {
      	case "getTelephoneList":
      	 echo "Kerio - Management of users and lines - Telephone book";break;
        case "getFreeTelephoneLinesList":
      	 echo "Kerio - Management of users and lines - Free telephone lines";break;
      	default:
      	 echo "Kerio - Management of users and lines";
      }
    ?>
    </title>
    
    <link rel="stylesheet"  type="text/css" href="style/css.css" /> 
     
  </head>
  <body>
    <div id="all">
             
        <div id="head">
          <img src="images/kerio_logo.jpg" alt="kerio_logo.jpg, 32kB" title="kerio_logo" height="90" />
          <h1> Management of users and lines</h1>    
        </div>

        <div id="menu">
          <h2>Menu</h2>
          <ul>
            <li><a href="index.php?stranka=getTelephoneList">Telephone book</a></li>
            <li><a href="index.php?stranka=getFreeTelephoneLinesList">Free lines</a></li>
            <?php
                if(isset ($_SESSION['login'])){
                    printf('<li><a href="index.php?stranka=setLine">Line assignment</a></li>');
                    printf('<li><a href="index.php?stranka=quickAdd">QuickAdd</a></li>');
                }
            ?>
  
          </ul>
          <h2>Login:</h2>
       
          <?php
            
            if (isset($_SESSION['login']) && $_SESSION['login'] != 'ReadOnlyUser') {
               
                printf('Logged as: '.$_SESSION['login'].'<br />');
                printf('<a href="index.php?stranka=logout">Log Out</a>');
                $view->loginMyphone();

            }  else {
                printf('
                    <form action="index.php?stranka=loginMyphone" method="post" enctype="multipart/form-data">
                    <p>
                        <label>Login: </label>
                        <input type="text" name="login" value="" /><br /> 
                        <label>Password: </label>
                        <input type="password" name="password" value="" /><br /> 
                        <input type="submit" value="Log In" />
                    </p>
                    </form>'
                );
            }
          ?>
          

        </div>
        
        <div id="obsah">
          <div id="obsah_text">
          
          <?php

                    
                    
                    if(@$_GET['stranka'] == null){
                        $stranka = 'getTelephoneList';
                    }else{
                        $stranka = $_GET['stranka'];    
                    }
                    
                    
                    
                    if ($view->line->getGenerateTime(1)+3600 < Time()) {
                        $view->refreshTelephoneList();  
                    }else {
                        $view->$stranka();
                    }
       
          
          ?>
          

            </div> 
        </div>
        <div id="box_clear">
        </div>
        <div id="footer">
          <p>Copyright &copy; FPSquad 2012</p>
        </div>    
    </div>
  </body>
</html>  
    
    
   





<?php
/**
 * Controller class to change data and view
 *
 * @author FPSquad
 */
require_once ('User.php');
require_once ('Line.php');
require_once ('Auth.php');
require_once ('mPDF/Pdf.php');
require_once ('mail.php');



class Control{

    /**
     * data class User
     * @var User
     */
    var $user;
    
    /**
     * data class Line
     * @var Line
     */
    var $line;
    
    /**
     * authentization class Auth
     * @var Auth 
     */
    var $auth;
    
    /**
     * Making pdf from html
     * @var pdf 
     */
    var $pdf;
    
    
    /**
     * Construct of Control
     */
    public function __construct() {
        $this->user = new User();
        $this->line = new Line();
        $this->auth = new Auth();
        $this->pdf = new Pdf();
        
    }
    
    /**
     * Generating telephone list
     * Values: Full name of user and their lines
     */
    function getTelephoneList() {

        $result = $this->user->getUsersDetailDB();
        $time = $this->line->getGenerateTime(1);       
        $data = ''; // data for PDF export

        printf('<h2>Telephone book</h2>');
       
        printf('<div id="box_left"><a href="index.php?stranka=refreshTelephoneList"><img src="images/refresh.png" height="15" /> Refresh</a>');
        printf('<br />Export list: <a href="index.php?stranka=generateTelephoneListPDF"><img src="images/pdf.png" height="15" /> TelephoneBook.pdf</a><br /><br /></div>');
        printf('<div id="box_right">Last update: '.strftime("%d. %m. %Y  %H:%M", $time).'<br />');
        printf('<a href="javascript:window.print()" ><img src="images/print.png" height="15"/> Print</a></div>');
        
        
        printf('<table><tr id=\'table_top\'><td><b>User name</b></td><td><b>User telephone lines</b></td></tr>');
       
        $totalItems = $result['totalItems'];
        for ($i = 0; $i < $totalItems; $i++){ 
          if (count($result['userList'][$i]['EXTENSIONS']) == 0) {
            continue;	
          }

          $number_of_lines = count($result['userList'][$i]['EXTENSIONS']);
          $data = $data.'<tr><td>'.$result['userList'][$i]['FULL_NAME'].'</td><td>';
          printf('<tr><td>'.$result['userList'][$i]['FULL_NAME'].'</td><td>');
          
          for ($y = 0; $y < $number_of_lines; $y++ ) {
              $data = $data.''.$result['userList'][$i]['EXTENSIONS'][$y]['TEL_NUM'];
              printf($result['userList'][$i]['EXTENSIONS'][$y]['TEL_NUM']);
              
              if($y != $number_of_lines - 1) {
                  printf(', ');               
                  $data = $data.', ';
              }
          }
          printf('</td></tr>');
          $data = $data.'</td></tr>';
        }
        printf('</table>');
        
        
               
        if ($_GET['stranka'] == 'generateTelephoneListPDF') {
            $this->pdf->makePDF($time, $data, $totalItems);

            $host  = $_SERVER['HTTP_HOST'];
            $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $file = 'export/PhoneBook.pdf';
            header("Location: http://$host$uri/$file");       
        }
    }
    

    /**
     * Print list of free telephone lines
     * Values: number of line, description
     */
    function getFreeTelephoneLinesList() {
        
        printf('<h2>Free lines list</h2>');
        $time = $this->line->getGenerateTime(2);
        
        printf('<div id="box_left"><a href="index.php?stranka=refreshFreeLines"><img src="images/refresh.png" height="15" /> Refresh</a></div>');
        printf('<div id="box_right">Last update: '.strftime("%d. %m. %Y  %H:%M", $time).'</div><br /><br />');
        
        $result = $this->line->getTelephoneLinesDB();
        $stop = $result['totalItems'];
        
        if($stop == 0){
            printf('There are currently no lines available.');
        }else{
            printf('<table><tr id=\'table_top\'><td class=\'table_left\'><b>Line number</b></td><td><b>Description</b></td></tr>');
            for ($i = 0; $i < $stop; $i++) {  
                printf('<tr class=\'table_line\'><td class=\'table_left\'>'.$result['sipExtensionList'][$i]['TEL_NUM'].'</td>');
                printf('<td>'.$result['sipExtensionList'][$i]['DESCRIPTION'].'</td></tr>');

            }
            printf('</table>');    
        }

    }
    
    /**
     * Synchronize FreeLines data Kerio Operator and local DB
     */
    function refreshFreeLines() {
        $this->line->deleteLinesDB();
        $data = $this->line->getTelephoneLines();
        $this->line->setTelephoneLinesDB($data);
        $this->line->setGenerateTime(2);
        
        $this->getFreeTelephoneLinesList();    
    }
    

    /**
     * Synchronize TelephoneList data Kerio Operator and local DB
     */
    function refreshTelephoneList() {
        $this->line->deleteLinesDB();
        $data = $this->line->getTelephoneLines();
        $this->line->setTelephoneLinesDB($data);
        
        $this->user->deleteUsersDB();
        $data = $this->user->getUsersDetail();
        $this->user->setUsersDetailDB($data);
        $this->user->setGenerateTime(1);
        
        $this->getTelephoneList();    
    }
    

    /**
     * Set free telephone line to selected user
     */
    function setLine() {
       
        printf('<h2>Line assignment</h2>');
        
        $lines = $this->line->getTelephoneLines();
        //var_dump($lines);
        $users = $this->user->getUsersDetail();
        $print = 0;
        printf('<form action="index.php?stranka=setData" method="post" enctype="multipart/form-data">');
        
        
        if ($this->auth->authAdmin()) {
            printf('<label>Select user and line: </label><br><select name="user" size="20">');
            $count = $users['totalItems'];
            for ($i = 0; $i < $count; $i++) {
                printf('<option value='.$users['userList'][$i]['GUID'].'>'.$users['userList'][$i]['FULL_NAME'].', '.$users['userList'][$i]['USERNAME'].'</option>');
            }         
            printf('</select>');
            $print = 1;
            
        } else if($this->auth->authUser()){
           $guid = $this->user->getUserIdByUsername($_SESSION['login']); 
           printf('Line assignment to the current user: '.$_SESSION['login'].'<br />');
           printf('<input type="hidden" name="user" value='.$guid.'>');
           $print = 1;
           
        } else {
            printf('Access forbidden. You do not have sufficient privileges to access this section.');
        }
        
        if ($print == 1){
            printf('<select name="line" size="20">');
            $count = $lines['totalItems'];
            $full = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($lines['sipExtensionList'][$i]['USERS_ID'] == '') {
                    printf('<option value='.$lines['sipExtensionList'][$i]['GUID'].'>'.$lines['sipExtensionList'][$i]['TEL_NUM'].', '.$lines['sipExtensionList'][$i]['DESCRIPTION'].'</option>');   
                }else{
                    $full++;
                }
            }
            printf('</select><br />');
             if($full == $count){
                printf('<br /><b>No free line available.</b>');
            }else {
                 
                printf('<label>Line description: </label><input type="text" name="description"></input><br />');
                //printf('<label>Zadejte email pro potvrzení</label><input type="text" name="email" '></input><br />');
                printf('<input type="submit" value="Assign">');
            }
            printf('</form>');
        }
    }
    
    
    /**
     * Function to quick add new line to logged user
     */
    function quickAdd() {
        
        printf('<h2>Fast assignment of free line</h2>');
        
        if($this->auth->authUser() || $this->auth->authAdmin()){
            $lines = $this->line->getTelephoneLines();
            $users = $this->user->getUsersDetail();

            printf('<form action="index.php?stranka=setData" method="post" enctype="multipart/form-data">');

            $guid = $this->user->getUserIdByUsername($_SESSION['login']); 
            printf('Line assignment to the current user: '.$_SESSION['login'].'<br />');
            printf('<input type="hidden" name="user" value='.$guid.'>');

            $count = $lines['totalItems'];
            $full = 0;
            for ($i = 0; $i < $count; $i++) {
                if ($lines['sipExtensionList'][$i]['USERS_ID'] == '') {
                    //printf('<option value='.$lines['sipExtensionList'][$i]['GUID'].'>'.$lines['sipExtensionList'][$i]['TEL_NUM'].', '.$lines['sipExtensionList'][$i]['DESCRIPTION'].'</option>');
                    printf('<input type="hidden" name="line" value='.$lines['sipExtensionList'][$i]['GUID'].'></input>');
                    printf('Line number: '.$lines['sipExtensionList'][$i]['TEL_NUM']);
                    $i = $count;
                }else {
                    $full++;
                }
            }         
            
            if($full == $count){
                printf('<br /><b>No free lines available</b>');
            }else {
                printf('<br />');
                printf('<label>Line description</label><input type="text" name="description"></input><br />');
                printf('<input type="submit" value="Assign">');
            }
            
            
            printf('</form>');    
        }else {
            printf('Access forbidden. You do not have sufficient privileges to access this section.');
        }
        
        
    }
    

    /**
     * Set link to specified user
     */
    function setData(){
        
        
        if(isset($_POST['user']) && isset ($_POST['line'])){
            $this->line->setLineToUser($_POST['user'],$_POST['line'], $_POST['description']);
            $this->refreshTelephoneList();
            $user_detail = $this->user->getUserByIdDB($_POST['user']);
            $line_detail = $this->line->getTelephoneLineByIdDB($_POST['line']);
            printf('<br />Line <b>'.$line_detail['number'].'</b> was successfully added to <b>'.$user_detail['fullname'].'</b>');
            //refresh ????
            if($user_detail['email'] != '') {
                $message = 'Line <b>'.$line_detail['number'].'</b> was successfully added to <b>'.$user_detail['fullname'].'</b>';
                $mail = new s_mail();
                $mail->m_from = "users_and_lines_management@kerio.com";
                $mail->m_to = $user_detail['email'];
                $mail->m_subject = "Line assignment";
                $mail->m_title = "Kerio";
                $mail->message($message);
                 ////////TESTOVACI VYPIS MAILU///////
                $soubor = fopen("./test_mailu.txt", "w"); 
                fwrite($soubor, $mail->m_title."\n Subject: ".$mail->m_subject."\n From: ".$mail->m_from."\n To: ".$mail->m_to."\n Message: ".$message);                                                        //"Confirming email"<br>"Subject: ".'$this->m_subject'<br>"From: ".'$this->m_from'<br>"To: ".'$this->m_to'<br>"Message: ".'$this->m_msg'
                fclose($soubor);
                /////////////////////////////////////
                $m = $mail->send();
            }
        }else{
            printf('Select line and user.');
        }  
    }
    
    
    function generateTelephoneListPDF () {
        $this->getTelephoneList();
    }
  
    
    /**
     * Login to Kerio Myphone
     */
    function loginMyphone() {
        
        if (isset($_POST['login'])) {
            $this->user->loginMyphone($_POST['login'], $_POST['password']);
            echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php?stranka=getTelephoneList\">";
        }
    }
    
    
    /**
     * Logout user and redirect to main page
     */
    function logout() {
        session_destroy();
        echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php?stranka=getTelephoneList\">";
    }
    
}

?>


   


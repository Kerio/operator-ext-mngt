<?php

function autoUTF($s)
{
    if (preg_match('#[\x80-\x{1FF}\x{2000}-\x{3FFF}]#u', $s)) // detect UTF-8
    {
        return $s;
    }
    elseif (preg_match('#[\x7F-\x9F\xBC]#', $s)) // detect WINDOWS-1250
    {
        return iconv('WINDOWS-1250', 'UTF-8', $s);
    }
    else // assume ISO-8859-2
    {
        return iconv('ISO-8859-2', 'UTF-8', $s);
    }
}
  //trida pro odesilani mailu
class s_mail {
 
var $m_from = "";
var $m_to = "";
var $m_subject = "";
var $m_msg = "";
var $m_title = "";
var $m_headers = "";
var $html_title = "";
 
    function header() {
        //Message
        $m_msg = base64_encode(autoUTF($this->m_msg));
        $this->m_msg = $m_msg;
        //Subject
        $subject = "=?utf-8?B?".base64_encode(autoUTF($this->m_subject))."?=";
        $this->m_subject = $subject;
        //Title
        $title = "=?utf-8?B?".base64_encode(autoUTF($this->m_title))."?=";
        $this->m_title = $title;


        $head = "MIME-Version: 1.0".PHP_EOL;
        $head .= "From: ".$this->m_title." <".$this->m_from.">".PHP_EOL;
        $head .= "Return-Path: ".$this->m_from."".PHP_EOL;
        $head .= "Content-Type: text/html; charset=\"utf-8\"".PHP_EOL;
        $head .= "Content-Transfer-Encoding: base64".PHP_EOL;
        $this->m_headers = $head;
    }
 
    function preview() {
        $this->header();
        return htmlspecialchars("mail(".$this->m_to.",".$this->m_subject.",".$this->m_msg.",".$this->m_headers.");");
    }

    function addAdress($c_email) {
        if($c_email) {
            $m_to .= ",".trim($c_email);
        }
    }
 
    function send() {
        $this->header();
        
        $m = @mail($this->m_to,$this->m_subject,$this->m_msg,$this->m_headers);
        if($m){
            printf('<br />Confirming email has been sent.');

        }else {
            printf('<br />Confirming email could not be sent.');
            
        }   

        
        if(!$m) {
            return false;
        } else {
            return true;
        }
    }
 
    function message($msg) {
        $this->html_title = $this->m_title;
        global $baseurl;
        $c = "
        <html>
            <head>
                <title>".$this->html_title."</title>
            </head>
            <body>
                <style>
                body {
                color: black;
                background: white;
                }
                </style>
                <h3>".$this->html_title."</h3>
                <div>
                $msg
                </div>
            </body>
        </html>
        ";

        $this->m_msg = $c;
    }
 
}

?>

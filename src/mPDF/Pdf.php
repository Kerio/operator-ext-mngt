<?php

require_once 'mpdf.php';

class Pdf {

    
    var $mdpf;
    
    function __construct() {
        
        $this->mdpf = new mPDF('utf-8', 'A4', '', '', 20, 15, 48, 25, 10, 10);
        $this->mdpf->useOnlyCoreFonts = true;    // false is default
        $this->mdpf->SetProtection(array('print'));
        $this->mdpf->SetTitle("Telephone book - Kerio Technologies");
        $this->mdpf->SetAuthor("Kerio Technologies");
        $this->mdpf->SetWatermarkText("Kerio technologies");
        $this->mdpf->showWatermarkText = true;
        $this->mdpf->watermark_font = 'DejaVuSansCondensed';
        $this->mdpf->watermarkTextAlpha = 0.1;
        $this->mdpf->SetDisplayMode('fullpage');
    }

    function makePDF($time, $data, $totalItems) {
        
        $html = '
         <html>
         <head>
         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
         <style>
         body {font-family: sans-serif;
             font-size: 10pt;
         }
         p {    margin: 0pt;
         }
         td { vertical-align: top; }
         .items td {
             border-left: 0.1mm solid #000000;
             border-right: 0.1mm solid #000000;
         }
         .items {
            border: 0.1mm solid #000000;
         }
         table thead td { background-color: #EEEEEE;
             text-align: center;
             border: 0.1mm solid #000000;
         }
         .items td.blanktotal {
             background-color: #FFFFFF;
             border: 0mm none #000000;
             border-top: 0.1mm solid #000000;
             border-right: 0.1mm solid #000000;
             border-bottom: 0.1mm solid #000000;
         }
         .items td.totals {
             text-align: right;
             border: 0.1mm solid #000000;
         }
         </style>
         </head>
         <body>

         <!--mpdf
         <htmlpageheader name="myheader">
         <table width="100%"><tr>
         <td width="70%" style="color:#0000BB;"><span style="font-weight: bold; font-size: 25pt;">List of phone book users</span><br /></td>
         <td width="5%" style="text-align: right;"><img src="images/kerio_logo.jpg"><br /></td>
         </tr></table>
         </htmlpageheader>

         <htmlpagefooter name="myfooter">
         <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
         Page {PAGENO} of {nb}
         </div>
         </htmlpagefooter>

         <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
         <sethtmlpagefooter name="myfooter" value="on" />
         mpdf-->

         <div style="text-align: right">Printout date: ' . strftime("%d. %m. %Y  %H:%M", $time) . '</div>




         <table class="items" width="100% height=100%" style="font-size: 10pt; border-collapse: collapse;" cellpadding="8">
         <thead>
         <tr>
         <td width="25%">User name</td>
         <td width="45%">Telephone number</td>
         </tr>
         </thead>

         ' . $data . '

         </tbody>
         </table>
         <div style="text-align: left; font-style: italic;"><br />Number of records: ' . $totalItems . '</div>
         </body>
         </html>
         ';

        $this->mdpf->WriteHTML($html);

        $this->mdpf->Output('export/PhoneBook.pdf', '');
        
    }

}

//exit;
?>
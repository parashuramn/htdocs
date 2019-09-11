<?php
App::uses('AppController', 'Controller');
App::import('Controller', 'Reports');
class AddonsController extends AppController
{
    public $uses = array("Statement");
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login', 'logout');
        
        if (User::get('id') != "") $this->Auth->allow('test', 'pdfDownload');
    }
    function generatePdf($export_pdf) {        
       set_time_limit (0);
        if (empty($export_pdf) || empty($export_pdf['table_headers']) || empty($export_pdf['table_data'])) return;
        
        if (!empty($export_pdf['header_image'])) $header_image = $export_pdf['header_image'];
        else $header_image = 'header.png';
        
        if (!empty($export_pdf['footer_image'])) $footer_image = $export_pdf['footer_image'];
        else $footer_image = 'footer.png';
        if (!empty($export_pdf['report_type'])) {
            $report_type = ucfirst($export_pdf['report_type']);
            $filename = $export_pdf['report_type'] . "_" . time() . ".pdf";
        } else {
            $report_type = "";
            $filename = time() . ".pdf";
        }
        $filter_html = "";
        if (!empty($export_pdf['filtered_by'])) {
            $name_replace = array(
                    "objectid"  => __("Activity"),
                    "agent_id"  => __("User"),
                    "timestamp >="  => __("Timestamp From"),
                    "timestamp <="  => __("Timestamp To"),
                );
 
             foreach ($export_pdf['filtered_by'] as $name => $value) {
                if(is_array($value))
                    $value = implode(", ", $value);

                if(intval($name) == $name)
                {
                    if(strpos('a'.$value, "parent_ids LIKE"))
                    {
                        $name = __("Parent Level");
                        $value = __("Yes");
                    }
                    else
                    if(strpos('a'.$value, "grouping_ids LIKE"))
                    {
                        $name = __("Group Level");
                        $value = __("Yes");
                    }
                }
                if($name == "timestamp >=" || $name == "timestamp <=")
                {
                    $value = date("F j, Y H:i:s", strtotime($value));
                    $value = str_replace("00:00:00", "", $value);
                }
                $name = isset($name_replace[$name])? $name_replace[$name]:$name;
                $name = str_replace("_", " ", $name);
                $name = ucwords($name);
                $name = str_replace("Id", "ID", $name);

                $filter_arr[] = "<li><b>".htmlspecialchars($name)."</b>: ".htmlspecialchars($value)."</li>";
            }

            $filter_html = implode("", $filter_arr);
        }
        if (empty($export_pdf['return_type'])) $return_type = 'download';
        else $return_type = $export_pdf['return_type'];
        
        // tcpdf
        
        require_once (APP . DS . "Vendor" . DS  . "pdfexport" . DS . "tcpdf/pdfFactory.php");
        
        // Include the Advanced Table Class
        require_once (APP . DS . "Vendor" . DS  . "pdfexport" . DS . "tcpdf/classes/pdftable.php");
        
        /**
         * Include my Custom PDF class This is required only to overwrite the header
         */
        require_once (APP . DS . "Vendor" . DS  . "pdfexport" . DS . "tcpdf/mypdf.php");
        $total_headers = sizeof($export_pdf['table_headers']);
        if ($total_headers > 6) {
            $GLOBALS['page_type'] = "landscape";
        } else {
            $GLOBALS['page_type'] = "portrait";
        }
        $factory = new pdfFactory();
        
        // create new PDF document
        $oPdf = new myPdf();
        $oPdf = modified("pdf", $oPdf, $export_pdf);

        $factory->initPdfObject($oPdf);
        if ($total_headers > 6) {
            $oPdf->SetAutoPageBreak(true, 50);
            
            // claculte column size
            $column_size = 275 / $total_headers;
            for ($i = 0; $i < $total_headers; $i++) {
                $cols[$i] = $column_size;
            }
        } else {
            $oPdf->SetAutoPageBreak(true, 35);
            
            // calculate column size
            $column_size = 190 / $total_headers;
            for ($i = 0; $i < $total_headers; $i++) {
                $cols[$i] = $column_size;
            }
        }
        
        /**
         * Create the pdf Table object
         * Alternative you can use the Singleton Instance
         *
         * @example : $oTable = PdfTable::getInstance($oPdf);
         */
        
        /** include table  */
        
        $html = <<<EOD
<div style="color: black;">
<ul style="list-style-type: none;">
<li><span style="font-weight: bold;">Report : </span>{$report_type}</li>
EOD;
if(!empty($filter_html)) {
$html .=<<<EOD
<li><span style="font-weight: bold;">Filters:</span></li>
<ul style="list-style-type: none;">
{$filter_html}
</ul>
EOD;
}
$html .=<<<EOD
</ul>
</div>
EOD;
        
        // Print text using writeHTMLCell()
        $oPdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
        $oTable = new Pdf_Table($oPdf);
        
        $oTable->SetStyle("p", $oPdf->getDefaultFontName(), "", 7, "130,0,30");
        $oTable->SetStyle("b", $oPdf->getDefaultFontName(), "B", 7, "130,0,30");
        
        $nColumns = 3;
        
        /**
         * Set the tag styles
         */
        $aCustomConfiguration = array('TABLE' => array('TABLE_ALIGN' => 'L',
         //left align
        'BORDER_COLOR' => array(221, 221, 221),
         //border color
        'BORDER_SIZE' => '0.5',
         //border size
        ), 
        'HEADER' => array('TEXT_COLOR' => array(61, 61, 61),
         //text color
        'TEXT_SIZE' => 9,
         //font size
        'LINE_SIZE' => 10,
         //line size for one row
        'BACKGROUND_COLOR' => array(15, 161, 224),
         //background color
        'BORDER_SIZE' => 0.5,
         //border size
        'BORDER_TYPE' => 'B',
         //border type, can be: 0, 1 or a combination of: "LRTB"
        'BORDER_COLOR' => array(221, 221, 221),
         //border color
        ), 
        'ROW' => array('TEXT_COLOR' => array(61, 61, 61),
         //text color
        'TEXT_SIZE' => 6,
         //font size
        'BACKGROUND_COLOR' => array(255, 255, 255),
         //background color
        'BORDER_COLOR' => array(221, 221, 221),
         //border color
        ),);
        
        $oTable->initialize($cols, $aCustomConfiguration);
        
        //prepare table headers
        foreach ($export_pdf['table_headers'] as $key => $value) {
            $aHeader[$key]['TEXT'] = $value;
        }
        
        //add the header row
        $oTable->addHeader($aHeader);
        
        $aColor = array(array(255, 255, 255), array(245, 245, 245));
        $fill = 0;
        foreach ($export_pdf['table_data'] as $table_data) {
            $aRow = array();
            for ($i = 0; $i < count($table_data); $i++) {
                $aRow[$i]['TEXT'] = (!empty($table_data[$i])) ? $table_data[$i] : "";
                $aRow[$i]['BACKGROUND_COLOR'] = $aColor[$fill];
            }
            
            //add the data row
            $oTable->addRow($aRow);
            if ($fill == 0) $fill = 1;
            else $fill = 0;
        }
        
        //close the table
        $oTable->close();
        
        if ($return_type == "path" || $return_type == "url") {
            
            if ($return_type == "url") {
                $filepath = "pdf";
                $return_path = Router::url('/', true) . "pdf/" . $filename;
            } else {
                $filepath = TMP . "pdf";
                $return_path = TMP . "pdf" . DS . $filename;
            }
            if (!file_exists($filepath)) mkdir($filepath, 0777, true);
            
            $oPdf->Output($filepath . DS . $filename, "F");
            return $return_path;
        } else {
            $oPdf->Output($filename, "D");
        }
    }
}

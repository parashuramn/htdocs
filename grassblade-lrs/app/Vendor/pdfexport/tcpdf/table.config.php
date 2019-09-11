<?php
/**
 * Default configuration values for the PDF Advanced table
 */

$aDefaultConfiguration = array(

    'TABLE' => array(
        'TABLE_ALIGN' => 'L', //table align on page
        'TABLE_LEFT_MARGIN' => 10, //space to the left margin
        'BORDER_COLOR' => array( 0, 92, 177 ), //border color
        'BORDER_SIZE' => '0.3', //border size
        'BORDER_TYPE' => '1', //border type, can be: 0, 1
    ),

    'HEADER' => array(
        'TEXT_COLOR' => array( 220, 230, 240 ), //text color
        'TEXT_SIZE' => 8, //font size
        'TEXT_FONT' => 'dejavusans', //font family
        'TEXT_ALIGN' => 'C', //horizontal alignment, possible values: LRCJ (left, right, center, justified)
        'VERTICAL_ALIGN' => 'M', //vertical alignment, possible values: TMB(top, middle, bottom)
        'TEXT_TYPE' => 'B', //font type
        'LINE_SIZE' => 4, //line size for one row
        'BACKGROUND_COLOR' => array( 41, 80, 132 ), //background color
        'BORDER_COLOR' => array( 0, 92, 177 ), //border color
        'BORDER_SIZE' => 0.2, //border size
        'BORDER_TYPE' => '1', //border type, can be: 0, 1 or a combination of: "LRTB"
        'TEXT' => ' ', //default text
        //padding
        'PADDING_TOP' => 0, //padding top
        'PADDING_RIGHT' => 1, //padding right
        'PADDING_LEFT' => 1, //padding left
        'PADDING_BOTTOM' => 0, //padding bottom
    ),

    'ROW' => array(
        'TEXT_COLOR' => array( 0, 0, 0 ), //text color
        'TEXT_SIZE' => 6, //font size
        'TEXT_FONT' => 'dejavusans', //font family
        'TEXT_ALIGN' => 'C', //horizontal alignment, possible values: LRCJ (left, right, center, justified)
        'VERTICAL_ALIGN' => 'M', //vertical alignment, possible values: TMB(top, middle, bottom)
        'TEXT_TYPE' => '', //font type
        'LINE_SIZE' => 4, //line size for one row
        'BACKGROUND_COLOR' => array( 255, 255, 255 ), //background color
        'BORDER_COLOR' => array( 0, 92, 177 ), //border color
        'BORDER_SIZE' => 0.1, //border size
        'BORDER_TYPE' => '1', //border type, can be: 0, 1 or a combination of: "LRTB"
        'TEXT' => ' ', //default text
        //padding
        'PADDING_TOP' => 1,
        'PADDING_RIGHT' => 1,
        'PADDING_LEFT' => 1,
        'PADDING_BOTTOM' => 1,
    ),
);

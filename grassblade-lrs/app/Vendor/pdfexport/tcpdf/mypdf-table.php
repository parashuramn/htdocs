<?php

/**
 * Custom PDF class extention for Header and Footer Definitions
 *
 * @author andy@interpid.eu
 *
 */

require_once( 'myPdf.php' );

class myPdfTable extends myPdf
{
    protected $headerSource = 'header-table.txt';
}


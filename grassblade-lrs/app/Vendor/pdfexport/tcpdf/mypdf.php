<?php

/**
 * Custom PDF class extention for Header and Footer Definitions
 *
 * @author andy@interpid.eu
 *
 */
class myPdf extends Pdf
{

   

    /**
     * Custom Header
     *
     * @see Pdf::Header()
     */
    public function Header()
    {

        $pageN = $this->page;//getAliasNumPage();
        $pdf_header = modified("pdf_header", APP . '/webroot/img/header.png', $pageN);
        $pdf_header_logo = modified("pdf_header_logo", APP . '/webroot/img/header_logo.png', $pageN);
        if(empty($pdf_header))
            return;

        $this->SetY( 10 );

        /**
         * yes, even here we can use the multicell tag! this will be a local object
         */
        $oMulticell = PdfMulticell::getInstance( $this );
    
        if($GLOBALS['page_type'] == "portrait")
        {
            $this->Image($pdf_header, 0, 0, 215, 0, 'PNG');
            $this->Image($pdf_header_logo, 72, 10, 72, 0, 'PNG');
        }
        else {
            $this->Image($pdf_header, 0, 0, 300, 0, 'PNG');
            $this->Image($pdf_header_logo, 114, 10, 72, 0, 'PNG');
        }
        $this->SetMargins(0,40);
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM-15);       
       
    }


    /**
     * Custom Footer
     *
     * @see Pdf::Footer()
     */
    public function Footer()
    {
        $pageN = $this->page;//getAliasNbPages();
        //echo $pageN;
        $pdf_footer = modified("pdf_footer", APP . '/webroot/img/footer.png', $pageN);
        if(empty($pdf_footer))
            return;

        $this->SetY( -10 );

        if($GLOBALS['page_type'] == "portrait")
            $this->Image($pdf_footer, 0, 260, 215, 0, 'PNG');
        else
            $this->Image($pdf_footer, 0, 160, 300, 0, 'PNG');
        
    }

    /**
     * Returns the default Font to be used
     *
     * @return string
     */
    public function getDefaultFontName()
    {
        return 'helvetica';
    }

    /**
     * Draws the margin lines.
     * It's helpful during development
     */
    public function drawMarginLines()
    {
        //draw the top and bottom margins
        $ytop = $this->tMargin;
        $ybottom = $this->h - 20;

        $this->SetLineWidth( 0.1 );
        $this->SetDrawColor( 150, 150, 150 );
        $this->Line( 0, $ytop, $this->w, $ytop );
        $this->Line( 0, $ybottom, $this->w, $ybottom );
        $this->Line( $this->rMargin, 0, $this->rMargin, $this->h );
        $this->Line( $this->w - $this->rMargin, 0, $this->w - $this->rMargin, $this->h );
    }
}


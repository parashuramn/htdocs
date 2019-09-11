<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Pdf_Table_Cell_ImageSVG
 *
 * @author PMan
 */
class Pdf_Table_Cell_ImageSVG extends Pdf_Table_Cell_Image
{

    public function isImageSVG( $file )
    {
        return TCPDF_IMAGES::getImageFileType( $file ) == 'svg';
    }

    public function setImage( $file = '', $width = 0, $height = 0, $type = '', $link = '' )
    {
        if ( $this->isImageSVG( $file ) )
        {
            $this->sFile = $file;
            $this->sType = $type;
            $this->sLink = $link;

            //check if file exists etc...
            $this->doChecks();

            list ( $width, $height ) = $this->getImageParamsSVG( $file, $width, $height );
            //list ( $width, $height ) = $this->oPdfi->getImageParams( $file, $width, $height );

            $this->setContentWidth( $width );
            $this->setContentHeight( $height );
            //$this->ImageSVG(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
        } else
        {
            parent::setImage( $file, $width, $height, $type, $link );
        }
    }

    private function getImageParamsSVG( $file, $w = 0, $h = 0 )
    {
        //$this->svgdir = dirname($file);
        $svgdata = TCPDF_STATIC::fileGetContents( $file );

        if ( $svgdata === false )
        {
            $this->oPdf->Error( 'SVG file not found: ' . $file );
        }
        $k = $this->oPdf->k;
        $ow = $w;
        $oh = $h;
        $regs = array();
        // get original image width and height
        preg_match( '/<svg([^\>]*)>/si', $svgdata, $regs );
        if ( isset( $regs[ 1 ] ) AND !empty( $regs[ 1 ] ) )
        {
            $tmp = array();
            if ( preg_match( '/[\s]+width[\s]*=[\s]*"([^"]*)"/si', $regs[ 1 ], $tmp ) )
            {
                $ow = $this->oPdf->getHTMLUnitToUnits( $tmp[ 1 ], 1, $this->oPdf->svgunit, false );
            }
            $tmp = array();
            if ( preg_match( '/[\s]+height[\s]*=[\s]*"([^"]*)"/si', $regs[ 1 ], $tmp ) )
            {
                $oh = $this->oPdf->getHTMLUnitToUnits( $tmp[ 1 ], 1, $this->oPdf->svgunit, false );
            }
        }
        if ( $ow <= 0 )
        {
            $ow = 1;
        }
        if ( $oh <= 0 )
        {
            $oh = 1;
        }
        // calculate image width and height on document
        if ( ( $w <= 0 ) AND ( $h <= 0 ) )
        {
            // convert image size to document unit
            $w = $ow;
            $h = $oh;
        } elseif ( $w <= 0 )
        {
            $w = $h * $ow / $oh;
        } elseif ( $h <= 0 )
        {
            $h = $w * $oh / $ow;
        }

        return array(
            $w,
            $h
        );
    }


    /**
     * Renders the image in the pdf Object at the specified position
     *
     * @param PdfInterface $pdf
     * @param numeric $x The X Position
     * @param numeric $y The Y Position
     */
    public function render()
    {

        $this->renderCellLayout();

        $x = $this->oPdf->GetX() + $this->getBorderSize();
        $y = $this->oPdf->GetY() + $this->getBorderSize();

        $width = $this->getContentWidth();
        $height = $this->getContentHeight();

        //Horizontal Alignment
        if ( strpos( $this->sAlignment, 'J' ) !== false )
        {
            //justified - image is fully streched

            //var_dump($this->getCellDrawWidth());

            $x += $this->PADDING_LEFT;
            $this->setContentWidth( $this->getCellDrawWidth() - 2 * $this->getBorderSize() - $this->getPaddingLeft() - $this->getPaddingRight() );
        } elseif ( strpos( $this->sAlignment, 'C' ) !== false )
        {
            //center
            $x += ( $this->getCellDrawWidth() - $this->getContentWidth() ) / 2;
        } elseif ( strpos( $this->sAlignment, 'R' ) !== false )
        {
            //right
            $x += $this->getCellDrawWidth() - $this->getContentWidth() - $this->getPaddingRight();
        } else
        {
            //left, this is default
            $x += $this->getPaddingLeft();
        }

        //Vertical Alignment
        if ( strpos( $this->sAlignment, 'T' ) !== false )
        {
            //top
            $y += $this->getPaddingTop();
        } elseif ( strpos( $this->sAlignment, 'B' ) !== false )
        {
            //bottom
            $y += $this->getCellDrawHeight() - $this->getContentHeight() - $this->getPaddingBottom();
        } else
        {
            //middle, this is default
            $y += ( $this->getCellDrawHeight() - $this->getContentHeight() ) / 2;
        }

        if ( $this->isImageSVG( $this->sFile ) )
        {
            $this->oPdf->ImageSVG( $this->sFile, $x, $y, $this->getContentWidth(), $this->getContentHeight(), $this->sType, $this->sLink );
        } else
        {
            $this->oPdf->Image( $this->sFile, $x, $y, $this->getContentWidth(), $this->getContentHeight(), $this->sType, $this->sLink );
        }
    }
}
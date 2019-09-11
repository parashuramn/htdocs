<?php

/**
 * Pdf Class Interface
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.
 *
 * IN NO EVENT SHALL WE OR OUR SUPPLIERS BE LIABLE FOR ANY SPECIAL, INCIDENTAL, INDIRECT
 * OR CONSEQUENTIAL DAMAGES WHATSOEVER (INCLUDING, WITHOUT LIMITATION, DAMAGES FOR LOSS
 * OF BUSINESS PROFITS, BUSINESS INTERRUPTION, LOSS OF BUSINESS INFORMATION OR ANY OTHER
 * PECUNIARY LAW) ARISING OUT OF THE USE OF OR INABILITY TO USE THE SOFTWARE, EVEN IF WE
 * HAVE BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.
 *
 * @version   : 5.1.0
 * @author    : Andrei Bintintan <andy@interpid.eu>
 * @copyright : Copyright (c) 2014, Andrei Bintintan, http://www.interpid.eu
 * @license   : http://www.interpid.eu/pdf-addons/eula
 */
class Pdf_Interface
{

    /**
     * Pointer to the pdf object
     *
     * @var Pdf
     */
    protected $oPdf;


    public function __construct( $pdf )
    {

        $this->oPdf = $pdf;
    }

    /**
     * Returns the PDF object of the Interface
     *
     * @return Pdf
     */
    public function getPdfObject()
    {
        return $this->oPdf;
    }


    /**
     * Returns the page width
     */
    public function getPageWidth()
    {

        return (int)$this->oPdf->w - $this->oPdf->rMargin - $this->oPdf->lMargin;
    }


    /**
     * Returns the current X position
     *
     * @return number
     */
    public function getX()
    {

        return $this->oPdf->GetX();
    }


    /**
     * Returns the remaining width to the end of the current line
     *
     * @return number The remaining width
     */
    public function getRemainingWidth()
    {

        $n = $this->getPageWidth() - $this->getX();

        if ( $n < 0 )
            $n = 0;

        return $n;
    }


    /**
     * Returns the character width for the specified input parameters
     *
     * @param $char string
     * @param $fontfamily string
     * @param $fontstyle string
     * @param $fontsize string
     * @return number The character width
     */
    public function getCharStringWidth( $char, $fontfamily, $fontstyle, $fontsize )
    {

        return $this->oPdf->GetArrStringWidth( array(
            $char
        ), $fontfamily, $fontstyle, $fontsize );
    }


    /**
     * Split string into array of equivalent codes and return the result array
     *
     * @param string $str The input string
     * @return array List of codes
     */
    public function stringToArray( $str )
    {
        return TCPDF_FONTS::UTF8StringToArray( $str, $this->oPdf->isunicode, $this->oPdf->CurrentFont );
    }


    /**
     * Returns the active font family
     *
     * @return string The font family
     */
    public function getFontFamily()
    {

        return $this->oPdf->getFontFamily();
    }


    /**
     * Returns the active font style
     *
     * @return string the font style
     */
    public function getFontStyle()
    {

        return $this->oPdf->getFontStyle();
    }


    /**
     * Returns the active font size in PT
     *
     * @return number The font size
     */
    public function getFontSizePt()
    {

        return $this->oPdf->getFontSizePt();
    }


    /**
     * Adds an image to the pdf document
     *
     * @param string $file File Path
     * @param number $x
     * @param number $y
     * @param int $w Width
     * @param int $h Height
     * @param string $type Type
     * @param string $link Link
     */
    public function Image( $file, $x = null, $y = null, $w = 0, $h = 0, $type = '', $link = '' )
    {

        $this->oPdf->Image( $file, $x, $y, $w, $h, $type, $link );
    }


    /**
     * Returns the image width and height in PDF values!
     *
     * @param string $file Image file
     * @param int|number $w
     * @param int|number $h
     * @return array(width, height)
     */
    public function getImageParams( $file, $w = 0, $h = 0 )
    {

        $info = @getimagesize( $file );

        if ( false === $info )
        {
            $this->oPdf->Error( "Couln't get image size: $file" );
        }

        $info[ 'w' ] = $info[ 0 ];
        $info[ 'h' ] = $info[ 1 ];

        // Automatic width and height calculation if needed
        if ( $w == 0 && $h == 0 )
        {
            // Put image at 300 dpi
            $w = -300;
            $h = -300;
        }
        if ( $w < 0 )
            $w = -$info[ 'w' ] * 72 / $w / $this->oPdf->k;
        if ( $h < 0 )
            $h = -$info[ 'h' ] * 72 / $h / $this->oPdf->k;
        if ( $w == 0 )
            $w = $h * $info[ 'w' ] / $info[ 'h' ];
        if ( $h == 0 )
            $h = $w * $info[ 'h' ] / $info[ 'w' ];

        return array(
            $w,
            $h
        );
    }

    /**
     * Wrapper for the cell function
     */
    public function Cell( $w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '' )
    {
        $this->oPdf->Cell( $w, $h, $txt, $border, $ln, $align, $fill, $link, 0, true );
    }
}


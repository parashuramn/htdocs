<?php

/**
 * String Tag extraction class
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
class Pdf_String_Tags
{

    /**
     * Contains the Tag/String Correspondence
     *
     * @var array
     */
    protected $aTAGS = array();

    /**
     * Contains the links for the tags that have specified this parameter
     *
     * @var array
     */
    protected $aHREF;

    /**
     * The maximum number of chars for a tag
     *
     * @var integer
     */
    protected $iTagMaxElem;


    /**
     * Constructor
     *
     * @param int|number $p_tagmax number - the number of characters allowed in a tag
     * @return \Pdf_String_Tags
     */
    public function __construct( $p_tagmax = 10 )
    {
        $this->aTAGS = array();
        $this->aHREF = array();
        $this->iTagMaxElem = $p_tagmax;
    }


    /**
     * Returns TRUE if the specified tag name is an "<open tag>", (it is not already opened)
     *
     * @param $p_tag string - tag name
     * @param $p_array array - tag arrays
     * @return boolean
     */
    protected function OpenTag( $p_tag, $p_array )
    {

        $aTAGS = & $this->aTAGS;
        $aHREF = & $this->aHREF;
        $maxElem = & $this->iTagMaxElem;

        if ( !preg_match( "/^<([a-zA-Z0-9]{1,$maxElem}) *(.*)>$/i", $p_tag, $reg ) ) return false;

        $p_tag = $reg[ 1 ];

        $sHREF = array();
        if ( isset( $reg[ 2 ] ) )
        {
            preg_match_all( "|([^ ]*)=[\"'](.*)[\"']|U", $reg[ 2 ], $out, PREG_PATTERN_ORDER );
            for ( $i = 0; $i < count( $out[ 0 ] ); $i++ )
            {
                $out[ 2 ][ $i ] = preg_replace( "/(\"|')/i", "", $out[ 2 ][ $i ] );
                array_push( $sHREF, array( $out[ 1 ][ $i ], $out[ 2 ][ $i ] ) );
            }
        }

        if ( in_array( $p_tag, $aTAGS ) ) return false; //tag already opened


        if ( in_array( "</$p_tag>", $p_array ) )
        {
            array_push( $aTAGS, $p_tag );
            array_push( $aHREF, $sHREF );

            return true;
        }

        return false;
    }


    /**
     * returnes true if $p_tag is a "<close tag>"
     *
     * @param $p_tag - tag string $p_array - tag array;
     * @return true/false
     */
    /**
     * Returns true if $p_tag is a "<close tag>"
     *
     * @param $p_tag string - tag name
     * @return boolean
     */
    protected function CloseTag( $p_tag )
    {
        $aTAGS = & $this->aTAGS;
        $aHREF = & $this->aHREF;
        $maxElem = & $this->iTagMaxElem;

        if ( !preg_match( "/^<\/([a-zA-Z0-9]{1,$maxElem})>$/i", $p_tag, $reg ) ) return false;

        $p_tag = $reg[ 1 ];

        if ( in_array( "$p_tag", $aTAGS ) )
        {
            array_pop( $aTAGS );
            array_pop( $aHREF );

            return true;
        }

        return false;
    }


    /**
     * Expands the paramteres that are kept in Href field
     *
     * @param $pResult struct
     * @return string
     */
    protected function expand_parameters( $pResult )
    {
        $aTmp = $pResult[ 'params' ];
        if ( $aTmp != '' ) for ( $i = 0; $i < count( $aTmp ); $i++ )
        {
            $pResult[ $aTmp[ $i ][ 0 ] ] = $aTmp[ $i ][ 1 ];
        }

        unset( $pResult[ 'params' ] );

        return $pResult;
    }


    /**
     * Optimizes the result of the tag result array In the result array there can be strings that are consecutive and have the same tag, they are concatenated.
     *
     * @param $result array - the array that has to be optimized
     * @return array - optimized result
     */
    protected function optimize_tags( $result )
    {

        if ( count( $result ) == 0 ) return $result;

        $res_result = array();
        $current = $result[ 0 ];
        $i = 1;

        while ( $i < count( $result ) )
        {

            //if they have the same tag then we concatenate them
            if ( ( $current[ 'tag' ] == $result[ $i ][ 'tag' ] ) && ( $current[ 'params' ] == $result[ $i ][ 'params' ] ) )
            {
                $current[ 'text' ] .= $result[ $i ][ 'text' ];
            } else
            {
                $current = $this->expand_parameters( $current );
                array_push( $res_result, $current );
                $current = $result[ $i ];
            }

            $i++;
        }

        $current = $this->expand_parameters( $current );
        array_push( $res_result, $current );

        return $res_result;
    }


    /**
     * Parses a string and returnes an array of TAG - SRTING correspondent array The result has the following structure: array( array (string1, tag1), array (string2, tag2), ... etc )
     *
     * @param $p_str string - the Input String
     * @return array - the result array
     */
    public function get_tags( $p_str )
    {

        $aTAGS = & $this->aTAGS;
        $aHREF = & $this->aHREF;
        $aTAGS = array();
        $result = array();

        $reg = preg_split( '/(<.*>)/U', $p_str, -1, PREG_SPLIT_DELIM_CAPTURE );

        $sTAG = "";
        $sHREF = "";

        while ( false != ( list ( $key, $val ) = each( $reg ) ) )
        {
            if ( $val == "" ) continue;

            if ( $this->OpenTag( $val, $reg ) )
            {
                $sTAG = ( ( $temp = end( $aTAGS ) ) != null ) ? $temp : "";
                $sHREF = ( ( $temp = end( $aHREF ) ) != null ) ? $temp : "";
            } elseif ( $this->CloseTag( $val ) )
            {
                $sTAG = ( ( $temp = end( $aTAGS ) ) != null ) ? $temp : "";
                $sHREF = ( ( $temp = end( $aHREF ) ) != null ) ? $temp : "";
            } else
            {
                if ( $val != "" ) array_push( $result, array( 'text' => $val, 'tag' => $sTAG, 'params' => $sHREF ) );
            }
        } //while

        return $this->optimize_tags( $result );
    }
}


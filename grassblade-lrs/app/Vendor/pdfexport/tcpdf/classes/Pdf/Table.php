<?php
/**
 * Pdf Table
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
 * @property
 * @author    : Andrei Bintintan <andy@interpid.eu>
 * @copyright : Copyright (c) 2014, Andrei Bintintan, http://www.interpid.eu
 * @license   : http://www.interpid.eu/pdf-addons/eula
 */


require_once( dirname( __FILE__ ) . '/Tools.php' );
require_once( dirname( __FILE__ ) . '/Multicell.php' );
require_once( dirname( __FILE__ ) . '/Table/Cell/Empty.php' );
require_once( dirname( __FILE__ ) . '/Table/Cell/Multicell.php' );
require_once( dirname( __FILE__ ) . '/Table/Cell/Image.php' );


class Pdf_Table
{

    const TB_DATA_TYPE_DATA = 'data';
    const TB_DATA_TYPE_HEADER = 'header';
    const TB_DATA_TYPE_NEW_PAGE = 'new_page';
    const TB_DATA_TYPE_INSERT_NEW_PAGE = 'insert_new_page';

    /**
     * Text Color.
     * Array. @example: array(220,230,240)
     */
    const TEXT_COLOR = 'TEXT_COLOR';

    /**
     * Text Font Size.
     * number. @example: 8
     */
    const TEXT_SIZE = 'TEXT_SIZE';

    /**
     * Text Fond Family.
     * String. @example: 'Arial'
     */
    const TEXT_FONT = 'TEXT_FONT';

    /**
     * Text Align.
     * String. Possible values: LRC (left, right, center). @example 'C'
     */
    const TEXT_ALIGN = 'TEXT_ALIGN';

    /**
     * Text Font Type(Bold/Italic).
     * String. Possible values: BI. @example: 'B'
     */
    const TEXT_TYPE = 'TEXT_TYPE';

    /**
     * Vertical alignment of the text.
     * String. Possible values: TMB(top, middle, bottom). @example: 'M'
     */
    const VERTICAL_ALIGN = 'VERTICAL_ALIGN';

    /**
     * Line size for one row.
     * number. @example: 5
     */
    const LINE_SIZE = 'LINE_SIZE';

    /**
     * Cell background color.
     * Array. @example: array(41, 80, 132)
     */
    const BACKGROUND_COLOR = 'BACKGROUND_COLOR';

    /**
     * Cell border color.
     * Array. @example: array(0,92,177)
     */
    const BORDER_COLOR = 'BORDER_COLOR';

    /**
     * Cell border size.
     * number. @example: 0.2
     */
    const BORDER_SIZE = 'BORDER_SIZE';

    /**
     * Cell border type.
     * Mixed. Possible values: 0, 1 or a combination of: "LRTB". @example 'LRT'
     */
    const BORDER_TYPE = 'BORDER_TYPE';

    /**
     * Cell text.
     * The text that will be displayed in the cell. String. @example: 'This is a cell'
     */
    const TEXT = 'TEXT';

    /**
     * Padding Top.
     * number. Expressed in units. @example: 5
     */
    const PADDING_TOP = 'PADDING_TOP';

    /**
     * Padding Right.
     * number. Expressed in units. @example: 5
     */
    const PADDING_RIGHT = 'PADDING_RIGHT';

    /**
     * Padding Left.
     * number. Expressed in units. @example: 5
     */
    const PADDING_LEFT = 'PADDING_LEFT';

    /**
     * Padding Bottom.
     * number. Expressed in units. @example: 5
     */
    const PADDING_BOTTOM = 'PADDING_BOTTOM';

    /**
     * Table aling on page.
     * String. @example: 'C'
     */
    const TABLE_ALIGN = 'TABLE_ALIGN';

    /**
     * Table left margin.
     * number. @example: 20
     */
    const TABLE_LEFT_MARGIN = 'TABLE_LEFT_MARGIN';

    /**
     * Table draw header.
     * Boolean @example: true or false
     */
    const TABLE_DRAW_HEADER = 'DRAW_HEADER';

    /**
     * Table draw header.
     * Boolean @example: true or false
     */
    const TABLE_DRAW_BORDER = 'DRAW_BORDER';

    /**
     * Number of Columns of the Table
     *
     * @var int
     */
    protected $nColumns = 0;

    /**
     * Table configuration array
     */
    protected $aConfiguration = array();

    /**
     * Contains the Header Data - header characteristics and texts Characteristics constants for Header Type: EVERY CELL FROM THE TABLE IS A MULTICELL TEXT_COLOR - text color = array(r,g,b); TEXT_SIZE
     * - text size TEXT_FONT - text font - font type = "Arial", "Times" TEXT_ALIGN - text align - "RLCJ" VERTICAL_ALIGN - text vertical alignment - "TMB" TEXT_TYPE - text type (Bold Italic etc)
     * LN_SPACE - space between lines BACKGROUND_COLOR - background color = array(r,g,b); BORDER_COLOR - border color =
     * array(r,g,b); BORDER_SIZE - border size -- BORDER_TYPE - border size -- up down, with border without!!! etc BRD_TYPE_NEW_PAGE - border type on new page - this is user only if specified(<>'')
     * TEXT - header text -- THIS ALSO BELONGS ONLY TO THE HEADER!!!!
     * all these setting conform to the settings from the multicell functions!!!!
     *
     * @var array
     *
     */
    protected $aTableHeaderType = array();

    /**
     * Header is drawed or not
     *
     * @var boolean
     */
    protected $bDrawHeader = true;

    /**
     * True if the header will be added on a new page.
     *
     * @var boolean
     *
     */
    protected $bHeaderOnNewPage = true;

    /**
     * Header is parsed or not
     *
     * @var boolean
     *
     */
    protected $bHeaderParsed = false;

    /**
     * Page Split Variable - if the table does not have enough space on the current page then the cells will be splitted.
     * This only if $bTableSplit == TRUE If $bTableSplit == FALSE then the current cell will be drawed on the next page
     *
     * @var boolean
     */
    protected $bTableSplit = false;

    /**
     * TRUE - if on current page was some data written
     *
     * @var boolean
     */
    protected $bDataOnCurrentPage = false;

    /**
     * TRUE - if on current page the header was written
     *
     * @var boolean
     */
    protected $bHeaderOnCurrentPage = false;

    /**
     * Table Data Cache.
     * Will contain the information about the rows of the table
     *
     * @var array
     */
    protected $aDataCache = array();

    /**
     * TRUE - if there is a Rowspan in the Data Cache
     *
     * @var boolean
     */
    protected $bRowSpanInCache = false;

    /**
     * Sequence for Rowspan ID's.
     * Every Rowspan gets a unique ID
     *
     * @var int
     */
    protected $iRowSpanID = 0;

    /**
     * Table Header Cache.
     * Will contain the information about the header of the table
     *
     * @var array
     */
    protected $aHeaderCache = array();

    /**
     * Header Height.
     * In user units!
     *
     * @var int
     */
    protected $nHeaderHeight = 0;

    /**
     * Table Start X Position
     *
     * @var int
     */
    protected $iTableStartX = 0;

    /**
     * Table Start Y Position
     *
     * @var int
     */
    protected $iTableStartY = 0;

    /**
     * Multicell Object
     *
     * @var object
     *
     */
    protected $oMulticell = null;

    /**
     * Pdf Object
     *
     * @var Pdf
     *
     */
    protected $oPdf = null;

    /**
     * Contains the Singleton Object
     *
     * @var object
     */
    private static $_singleton = array(); //implements the Singleton Pattern


    /**
     * Column Widths
     *
     * @var array
     *
     */
    protected $aColumnWidth = array();

    protected $aTypeMap = array(
        'EMPTY' => 'Pdf_Table_Cell_Empty',
        'MULTICELL' => 'Pdf_Table_Cell_Multicell',
        'IMAGE' => 'Pdf_Table_Cell_Image'
    );

    /**
     * If set to true then page-breaks will be disabled
     *
     * @var bool
     */
    protected $bDisablePageBreak = false;


    /**
     * Class constructor.
     *
     * @param $oPdf object Instance of the PDF class
     * @return Pdf_Table
     */
    public function __construct( $oPdf )
    {
        //pdf object
        $this->oPdf = $oPdf;
        //call the multicell instance
        $this->oMulticell = new Pdf_Multicell( $oPdf );

        //get the default configuration
        $this->aConfiguration = $this->getDefaultConfiguration();
    }


    /**
     * Returnes the Singleton Instance of this class.
     *
     * @param $oPdf object the pdf Object
     * @return Pdf_Table
     */
    static function getInstance( $oPdf )
    {
        $oInstance = & self::$_singleton[ spl_object_hash( $oPdf ) ];

        if ( !isset( $oInstance ) )
        {
            $oInstance = new self( $oPdf );
        }

        return $oInstance;
    }


    /**
     * Returns the Pdf_Multicell instance
     *
     * @return Pdf_Multicell
     */
    public function getMulticellInstance()
    {
        return $this->oMulticell;
    }


    /**
     * Table Initialization Function
     *
     * @param array $aColumnWidths
     * @param array $aConfiguration
     */
    public function initialize( array $aColumnWidths, $aConfiguration = array() )
    {
        //set the no of columns
        $this->nColumns = count( $aColumnWidths );
        $this->setColumnsWidths( $aColumnWidths );

        //heeader is not parsed
        $this->bHeaderParsed = false;

        $this->aTableHeaderType = Array();

        $this->aDataCache = Array();
        $this->aHeaderCache = Array();

        $this->iTableStartX = $this->oPdf->GetX();
        $this->iTableStartY = $this->oPdf->GetY();

        $this->bDataOnCurrentPage = false;
        $this->bHeaderOnCurrentPage = false;

        $aKeys = array(
            'TABLE',
            'HEADER',
            'ROW'
        );

        foreach ( $aKeys as $val )
        {
            if ( !isset( $aConfiguration[ $val ] ) )
                continue;

            $this->aConfiguration[ $val ] = array_merge( $this->aConfiguration[ $val ], $aConfiguration[ $val ] );
        }

        $this->markMarginX();
    }


    /**
     * Closes the table.
     * This function writes the table content to the PDF Object.
     */
    public function close()
    {
        //output the table data to the pdf
        $this->ouputData();

        //draw the Table Border
        $this->drawBorder();
    }


    /**
     * Set the width of all columns with one function call
     *
     * @param $aColumnWidths array the width of columns, example: 50, 40, 40, 20
     */
    public function setColumnsWidths( $aColumnWidths = null )
    {
        if ( is_array( $aColumnWidths ) )
        {
            $this->aColumnWidth = $aColumnWidths;
        } else
        {
            $this->aColumnWidth = func_get_args();
        }
    }


    /**
     * Set the Width for the specified Column
     *
     * @param $nColumnIndex number the column index, 0 based ( first column starts with 0)
     * @param $nWidth number
     *
     */
    public function setColumnWidth( $nColumnIndex, $nWidth )
    {
        $this->aColumnWidth[ $nColumnIndex ] = $nWidth;
    }


    /**
     * Get the Width for the specified Column
     *
     * @param integer $nColumnIndex the column index, 0 based ( first column starts with 0)
     * @return number $nWidth The column Width
     */
    public function getColumnWidth( $nColumnIndex )
    {
        if ( !isset( $this->aColumnWidth[ $nColumnIndex ] ) )
        {
            trigger_error( "Undefined width for column $nColumnIndex" );

            return 0;
        }

        return $this->aColumnWidth[ $nColumnIndex ];
    }


    /**
     * Returns the current page Width
     *
     * @return integer - the Page Width
     */
    protected function PageWidth()
    {
        return (int)$this->oPdf->w - $this->oPdf->rMargin - $this->oPdf->lMargin;
    }


    /**
     * Returns the current page Height
     *
     * @return number - the Page Height
     */
    protected function PageHeight()
    {
        return (int)$this->oPdf->h - $this->oPdf->tMargin - $this->oPdf->bMargin;
    }


    /**
     * Sets the Split Mode of the Table.
     * Default is ON(true)
     *
     * @param $bSplit boolean - if TRUE then Split is Active
     */
    public function setSplitMode( $bSplit = true )
    {
        $this->bTableSplit = $bSplit;
    }


    /**
     * Enable or disables the header on a new page
     *
     * @param $bValue boolean
     *
     */
    public function setHeaderNewPage( $bValue )
    {
        $this->bHeaderOnNewPage = (bool)$bValue;
    }


    /**
     * Adds a Header Row to the table
     *
     * Example of a header row input array:
     * array(
     * 0 => array(
     * "TEXT" => "Header Text 1"
     * "TEXT_COLOR" => array(120,120,120),
     * "TEXT_SIZE" => 5,
     * ...
     * ),
     * 1 => array(
     * ...
     * ),
     * );
     *
     * @param $aHeaderRow array
     */
    public function addHeader( $aHeaderRow = array() )
    {
        $this->aTableHeaderType[ ] = $aHeaderRow;
    }


    /**
     * Sets a specific value for a header row
     *
     * @param $nColumn integer the Cell Column. Starts with 0.
     * @param $sPropertyKey string the Property Identifierthat should be set
     * @param $sPropertyValue mixed the Property Value value for the Key Index
     * @param $nRow integer The header Row. If the header row does not exists, then they will be created with default values.
     */
    public function setHeaderProperty( $nColumn, $sPropertyKey, $sPropertyValue, $nRow = 0 )
    {
        for ( $i = 0; $i <= $nRow; $i++ )
        {
            if ( !isset( $this->aTableHeaderType[ $i ] ) )
                $this->aTableHeaderType[ $i ] = array();
        }

        if ( !isset( $this->aTableHeaderType[ $nRow ][ $nColumn ] ) )
        {
            $this->aTableHeaderType[ $nRow ][ $nColumn ] = array();
        }

        $this->aTableHeaderType[ $nRow ][ $nColumn ][ $sPropertyKey ] = $sPropertyValue;
    }


    /**
     * Parses the header data and adds the data to the cache
     *
     * @param $bForce boolean
     */
    protected function parseHeader( $bForce = false )
    {
        //if the header was parsed don't parse it again!
        if ( $this->bHeaderParsed && !$bForce )
        {
            return;
        }

        //empty the header cache
        $this->aHeaderCache = Array();

        //create the header cache data
        foreach ( $this->aTableHeaderType as $val )
        {
            $this->_addDataToCache( $val, 'header' );
        }

        $this->_cacheParseRowspan( 0, 'header' );
        $this->headerHeight();
    }


    /**
     * Calculates the Header Height.
     * If the Header height is bigger than the page height then the script dies.
     */
    protected function headerHeight()
    {
        $this->nHeaderHeight = 0;

        $iItems = count( $this->aHeaderCache );
        for ( $i = 0; $i < $iItems; $i++ )
        {
            $this->nHeaderHeight += $this->aHeaderCache[ $i ][ 'HEIGHT' ];
        }

        if ( $this->nHeaderHeight > $this->PageHeight() )
        {
            die( "Header Height({$this->nHeaderHeight}) bigger than Page Height({$this->PageHeight()})" );
        }
    }


    /**
     * Calculates the X margin of the table depending on the ALIGN
     */
    protected function markMarginX()
    {
        $tb_align = $this->getTableConfig( 'TABLE_ALIGN' );

        //set the table align
        switch ( $tb_align )
        {
            case 'C':
                $this->iTableStartX = $this->oPdf->lMargin + $this->getTableConfig( 'TABLE_LEFT_MARGIN' ) + ( $this->PageWidth() - $this->getWidth() ) / 2;
                break;
            case 'R':
                $this->iTableStartX = $this->oPdf->lMargin + $this->getTableConfig( 'TABLE_LEFT_MARGIN' ) + ( $this->PageWidth() - $this->getWidth() );
                break;
            default:
                $this->iTableStartX = $this->oPdf->lMargin + $this->getTableConfig( 'TABLE_LEFT_MARGIN' );
                break;
        }
    }


    /**
     * Draws the Table Border
     */
    public function drawBorder()
    {
        if ( 0 == $this->getTableConfig( 'BORDER_TYPE' ) )
            return;

        if ( !$this->bDataOnCurrentPage )
            return; //there was no data on the current page


        //set the colors
        list ( $r, $g, $b ) = $this->getTableConfig( 'BORDER_COLOR' );
        $this->oPdf->SetDrawColor( $r, $g, $b );

        if ( 0 == $this->getTableConfig( 'BORDER_SIZE' ) )
            return;

        //set the line width
        $this->oPdf->SetLineWidth( $this->getTableConfig( 'BORDER_SIZE' ) );

        //draw the border
        $this->oPdf->Rect( $this->iTableStartX, $this->iTableStartY, $this->getWidth(), $this->oPdf->GetY() - $this->iTableStartY );
    }


    /**
     * End Page Special Border Draw.
     * This is called in the case of a Page Split
     */
    protected function _tbEndPageBorder()
    {
        if ( '' != $this->getTableConfig( 'BRD_TYPE_END_PAGE' ) )
        {

            if ( strpos( $this->getTableConfig( 'BRD_TYPE_END_PAGE' ), 'B' ) >= 0 )
            {

                //set the colors
                list ( $r, $g, $b ) = $this->getTableConfig( 'BORDER_COLOR' );
                $this->oPdf->SetDrawColor( $r, $g, $b );

                //set the line width
                $this->oPdf->SetLineWidth( $this->getTableConfig( 'BORDER_SIZE' ) );

                //draw the line
                $this->oPdf->Line( $this->iTableStartX, $this->oPdf->GetY(), $this->iTableStartX + $this->getWidth(), $this->oPdf->GetY() );
            }
        }
    }


    /**
     * Returns the table width in user units
     *
     * @return integer - table width
     */
    public function getWidth()
    {
        //calculate the table width
        $tb_width = 0;

        for ( $i = 0; $i < $this->nColumns; $i++ )
        {
            $tb_width += $this->getColumnWidth( $i );
        }

        return $tb_width;
    }


    /**
     * Aligns the table to the Start X point
     */
    protected function _tbAlign()
    {
        $this->oPdf->SetX( $this->iTableStartX );
    }


    /**
     * "Draws the Header".
     * More specific puts the data from the Header Cache into the Data Cache
     *
     */
    public function drawHeader()
    {
        $this->parseHeader();

        foreach ( $this->aHeaderCache as $val )
        {
            $this->aDataCache[ ] = $val;
        }

        $this->bHeaderOnCurrentPage = true;
    }


    /**
     * Adds a line to the Table Data or Header Cache.
     * Call this function after the table initialization, table, header and data types are set
     *
     * @param array $aRowData Data to be Drawed
     */
    public function addRow( $aRowData = array() )
    {
        if ( !$this->bHeaderOnCurrentPage )
        {
            $this->drawHeader();
        }

        $this->_addDataToCache( $aRowData );
    }


    /**
     * Adds a Page Break in the table.
     */
    public function addPageBreak()
    {
        //$this->insertNewPage();
        $aData = array();
        $aData[ 'ADD_PAGE_BREAK' ] = true;
        $this->aDataCache[ ] = array(
            'HEIGHT' => 0,
            'DATATYPE' => self::TB_DATA_TYPE_INSERT_NEW_PAGE
        );
        //$this->addRow($aData);
    }


    /**
     * Applies the default values for a header or data row
     *
     * @param $aData array Data Row
     * @param $sDataType string
     * @return array The Data with default values
     */
    protected function applyDefaultValues( $aData, $sDataType )
    {
        switch ( $sDataType )
        {
            case 'header':
                $aReference = $this->aConfiguration[ 'HEADER' ];
                break;

            default:
                $aReference = $this->aConfiguration[ 'ROW' ];
                break;
        }

        return array_merge( $aReference, $aData );
    }


    /**
     * Returns the default values
     *
     * @param $sDataType string
     * @return array The Data with default values
     */
    protected function getDefaultValues( $sDataType )
    {
        switch ( $sDataType )
        {
            case 'header':
                return $this->aConfiguration[ 'HEADER' ];
                break;

            default:
                return $this->aConfiguration[ 'ROW' ];
                break;
        }
    }


    protected function getCellObject( $data = null )
    {
        if ( null === $data )
        {
            $oCell = new Pdf_Table_Cell_Multicell( $this->oPdf );
        } elseif ( is_object( $data ) )
        {
            $oCell = $data;
        } else
        {

            $type = isset( $data[ 'TYPE' ] ) ? $data[ 'TYPE' ] : 'MULTICELL';
            $type = strtoupper( $type );

            if ( !isset( $this->aTypeMap[ $type ] ) )
            {
                trigger_error( "Invalid cell type: $type", E_USER_ERROR );
            }

            $class = $this->aTypeMap[ $type ];

            $oCell = new $class( $this->oPdf );
            /** @var $oCell Pdf_Table_Cell_Interface */
            $oCell->setProperties( $data );
        }

        if ( $oCell instanceof Pdf_Table_Cell_Multicell )
        {
            /** @var $oCell Pdf_Table_Cell_Multicell */
            $oCell->attachMulticell( $this->oMulticell );
        }

        return $oCell;
    }


    /**
     * Adds the data to the cache
     *
     * @param $data array - array containing the data to be added
     * @param $sDataType string - data type. Can be 'data' or 'header'. Depending on this data the $data is put in the selected cache
     */
    protected function _addDataToCache( $data, $sDataType = 'data' )
    {
        if ( !is_array( $data ) )
        {
            //this is fatal error
            trigger_error( "Invalid data value 0x00012. (not array)", E_USER_ERROR );
        }

        if ( $sDataType == 'header' )
        {
            $aRefCache = & $this->aHeaderCache;
        } else
        { //data
            $aRefCache = & $this->aDataCache;
        }

        $aRowSpan = array();

        $hm = 0;

        /**
         * If datacache is empty initialize it
         */
        if ( count( $aRefCache ) > 0 )
            $aLastDataCache = end( $aRefCache );
        else
            $aLastDataCache = array();

        //this variable will contain the active colspans
        $iActiveColspan = 0;

        $aRow = array();

        //calculate the maximum height of the cells
        for ( $i = 0; $i < $this->nColumns; $i++ )
        {

            if ( isset( $data[ $i ] ) )
            {
                $oCell = $this->getCellObject( $data[ $i ] );
            } else
            {
                $oCell = $this->getCellObject();
            }

            $aRow[ $i ] = $oCell;

            $oCell->setDefaultValues( $this->getDefaultValues( $sDataType ) );
            $oCell->setCellDrawWidth( $this->getColumnWidth( $i ) ); //copy this from the header settings

            //if there is an active colspan on this line we just skip this cell
            if ( $iActiveColspan > 1 )
            {
                $oCell->setSkipped( true );
                $iActiveColspan--;
                continue;
            }

            if ( !empty( $aLastDataCache ) )
            {

                //there was at least one row before and was data or header
                $cell = & $aLastDataCache[ 'DATA' ][ $i ];
                /** @var $cell Pdf_Table_Cell_Interface */


                if ( isset( $cell ) && ( $cell->getRowSpan() > 1 ) )
                {
                    /**
                     * This is rowspan over this cell.
                     * The cell will be ignored but some characteristics are kept
                     */

                    //this cell will be skipped
                    $oCell->setSkipped( true );
                    //decrease the rowspan value... one line less to be spanned
                    $oCell->setRowSpan( $cell->getRowSpan() - 1 );

                    //copy the colspan from the last value
                    $oCell->setColSpan( $cell->getColSpan() );

                    //cell width is the same as the one from the line before it
                    $oCell->setCellDrawWidth( $cell->getCellDrawWidth() );

                    if ( $oCell->getColSpan() > 1 )
                    {
                        $iActiveColspan = $oCell->getColSpan();
                    }

                    continue; //jump to the next column


                }
            }

            //set the font settings
            //$this->oPdf->SetFont($data[$i]TEXT_FONT'], $data[$i]['TEXT_TYPE'], $data[$i]['TEXT_SIZE']);


            /**
             * If we have colspan then we ignore the "colspanned" cells
             */
            if ( $oCell->getColSpan() > 1 )
            {

                for ( $j = 1; $j < $oCell->getColSpan(); $j++ )
                {
                    //if there is a colspan, then calculate the number of lines also with the with of the next cell
                    if ( ( $i + $j ) < $this->nColumns )
                        $oCell->setCellDrawWidth( $oCell->getCellDrawWidth() + $this->getColumnWidth( $i + $j ) );
                }
            }

            //add the cells that are with rowspan to the rowspan array - this is used later
            if ( $oCell->getRowspan() > 1 )
            {
                $aRowSpan[ ] = $i;
            }

            $oCell->processContent();

            //@todo: check this condition
            /**
             * IF THERE IS ROWSPAN ACTIVE Don't include this cell Height in the calculation.
             * This will be calculated later with the sum of all heights
             */
            if ( 1 == $oCell->getRowspan() )
            {
                $hm = max( $hm, $oCell->getCellDrawHeight() ); //this would be the normal height
            }

            if ( $oCell->getColSpan() > 1 )
            {
                //just skip the other cells
                $iActiveColspan = $oCell->getColSpan();
            }
        }

        //for every cell, set the Draw Height to the maximum height of the row
        foreach ( $aRow as $aCell )
        {
            /** @var $aCell Pdf_Table_Cell_Interface */
            $aCell->setCellDrawHeight( $hm );
        }

        //@formatter:off
        $aRefCache[ ] = array(
            'HEIGHT' => $hm, //the line maximum height
            'DATATYPE' => $sDataType, //The data Type - Data/Header
            'DATA' => $aRow, //this line's data
            'ROWSPAN' => $aRowSpan //rowspan ID array
        );
        //@formatter:on


        //we set the rowspan in cache variable to true if we have a rowspan
        if ( !empty( $aRowSpan ) && ( !$this->bRowSpanInCache ) )
        {
            $this->bRowSpanInCache = true;
        }
    }


    /**
     * Parses the Data Cache and calculates the maximum Height of each row.
     * Normally the cell Height of a row is calculated when the data's are added, but when that row is involved in a Rowspan then it's Height can change!
     *
     * @param $iStartIndex integer - the index from which to parse
     * @param $sCacheType string - what type has the cache - possible values: 'header' && 'data'
     */
    protected function _cacheParseRowspan( $iStartIndex = 0, $sCacheType = 'data' )
    {
        if ( $sCacheType == 'data' )
            $aRefCache = & $this->aDataCache;
        else
            $aRefCache = & $this->aHeaderCache;

        $aRowSpans = array();

        $iItems = count( $aRefCache );

        for ( $ix = $iStartIndex; $ix < $iItems; $ix++ )
        {

            $val = & $aRefCache[ $ix ];

            if ( !in_array( $val[ 'DATATYPE' ], array(
                'data',
                'header'
            ) )
            )
                continue;

            //if there is no rowspan jump over
            if ( empty( $val[ 'ROWSPAN' ] ) )
                continue;

            foreach ( $val[ 'ROWSPAN' ] as $k )
            {

                /** @var $cell Pdf_Table_Cell_Interface */
                $cell = & $val[ 'DATA' ][ $k ];

                if ( $cell->getRowSpan() < 1 )
                    continue; //skip the rows without rowspan


                //@formatter:off
                $aRowSpans[ ] = array(
                    'row_id' => $ix,
                    'cell_id' => &$cell
                );
                //@formatter:on


                $h_rows = 0;

                //calculate the sum of the Heights for the lines that are included in the rowspan
                for ( $i = 0; $i < $cell->getRowSpan(); $i++ )
                {
                    if ( isset( $aRefCache[ $ix + $i ] ) )
                    {
                        $h_rows += $aRefCache[ $ix + $i ][ 'HEIGHT' ];
                    }
                }

                //this is the cell height that makes the rowspan
                //$h_cell = $val['DATA'][$k]['HEIGHT'];
                //$h_cell = $val['DATA'][$k]->getCellDrawHeight();
                $h_cell = $cell->getCellDrawHeight();

                /**
                 * The Rowspan Cell's Height is bigger than the sum of the Rows Heights that he
                 * is spanning In this case we have to increase the height of each row
                 */
                if ( $h_cell > $h_rows )
                {
                    //calculate the value of the HEIGHT to be added to each row
                    $add_on = ( $h_cell - $h_rows ) / $cell->getRowSpan();
                    for ( $i = 0; $i < $cell->getRowSpan(); $i++ )
                    {
                        if ( isset( $aRefCache[ $ix + $i ] ) )
                        {
                            $aRefCache[ $ix + $i ][ 'HEIGHT' ] += $add_on;
                        }
                    }
                }
            }
        }

        /**
         * Calculate the height of each cell that makes the rowspan.
         * The height of this cell is the sum of the heights of the rows where the rowspan occurs
         */

        foreach ( $aRowSpans as $val1 )
        {
            $h_rows = 0;
            //calculate the sum of the Heights for the lines that are included in the rowspan
            for ( $i = 0; $i < $val1[ 'cell_id' ]->getRowSpan(); $i++ )
            {
                if ( isset( $aRefCache[ $val1[ 'row_id' ] + $i ] ) )
                    $h_rows += $aRefCache[ $val1[ 'row_id' ] + $i ][ 'HEIGHT' ];
            }

            $val1[ 'cell_id' ]->setCellDrawHeight( $h_rows );

            if ( false == $this->bTableSplit )
            {
                $aRefCache[ $val1[ 'row_id' ] ][ 'HEIGHT_ROWSPAN' ] = $h_rows;
            }
        }
    }


    /**
     * Splits the Data Cache into Pages.
     * Parses the Data Cache and when it is needed then a "new page" command is inserted into the Data Cache.
     */
    protected function _cachePaginate()
    {
        $iPageHeight = $this->PageHeight();

        /**
         * This Variable will contain the remained page Height
         */
        //$iLeftHeight = $iPageHeight - $this->oPdf->GetY() + $this->oPdf->tMargin;
        $iLeftHeight = $iPageHeight - $this->oPdf->GetY() + $this->oPdf->tMargin;

        $bWasData = true; //can be deleted
        $iLastOkKey = 0; //can be deleted


        $bDataOnThisPage = false;
        $bHeaderOnThisPage = false;
        $iLastDataKey = 0;

        //will contain the rowspans on the current page, EMPTY THIS VARIABLE AT EVERY NEW PAGE!!!
        $aRowSpans = array();

        $aDC = & $this->aDataCache;

        $iItems = count( $aDC );

        for ( $i = 0; $i < $iItems; $i++ )
        {

            $val = & $aDC[ $i ];

            switch ( $val[ 'DATATYPE' ] )
            {
                case self::TB_DATA_TYPE_INSERT_NEW_PAGE:
                    $aRowSpans = array();
                    $iLeftHeight = $iPageHeight;
                    $bDataOnThisPage = false; //new page
                    $this->insertNewPage( $i, null, true, true );
                    continue;
                    break;
            }

            $bIsHeader = $val[ 'DATATYPE' ] == 'header';

            if ( ( $bIsHeader ) && ( $bWasData ) )
            {
                $iLastDataKey = $iLastOkKey;
            }

            if ( isset( $val[ 'ROWSPAN' ] ) )
            {
                foreach ( $val[ 'ROWSPAN' ] as $v )
                {
                    $aRowSpans[ ] = array(
                        $i,
                        $v
                    );
                    $aDC[ $i ][ 'DATA' ][ $v ]->HEIGHT_LEFT_RW = $iLeftHeight;
                }
            }

            $iLeftHeightLast = $iLeftHeight;

            $iRowHeight = $val[ 'HEIGHT' ];
            $iRowHeightRowspan = 0;
            if ( ( false == $this->bTableSplit ) && ( isset( $val[ 'HEIGHT_ROWSPAN' ] ) ) )
            {
                $iRowHeightRowspan = $val[ 'HEIGHT_ROWSPAN' ];
            }

            $iLeftHeightRowspan = $iLeftHeight - $iRowHeightRowspan;
            $iLeftHeight -= $iRowHeight;

            //if (isset($val['DATA'][0]['IGNORE_PAGE_BREAK']) && ($iLeftHeight < 0)) {
            if ( isset( $val[ 'DATA' ][ 0 ]->IGNORE_PAGE_BREAK ) && ( $iLeftHeight < 0 ) )
            {
                $iLeftHeight = 0;
            }

            if ( ( $iLeftHeight >= 0 ) && ( $iLeftHeightRowspan >= 0 ) )
            {
                //this row has enough space on the page
                if ( true == $bIsHeader )
                {
                    $bHeaderOnThisPage = true;
                } else
                {
                    $iLastDataKey = $i;
                    $bDataOnThisPage = true;
                }
                $iLastOkKey = $i;
            } else
            {

                //@formatter:off

                /**
                 * THERE IS NOT ENOUGH SPACE ON THIS PAGE - HAVE TO SPLIT
                 * Decide the split type
                 *
                 * SITUATION 1:
                 * IF
                 *         - the current data type is header OR
                 *         - on this page we had no data(that means untill this point was nothing or just header) AND bTableSplit is off AND $iLastDataKey is NOT the first row(>0)
                 * THEN we just add new page on the positions of LAST DATA KEY ($iLastDataKey)
                 *
                 * SITUATION 2:
                 * IF
                 *         - TableSplit is OFF and the height of the current data is bigger than the Page Height minus (-) Header Height
                 * THEN we split the current cell
                 *
                 * SITUATION 3:
                 *         - normal split flow
                 *
                 */
                //@formatter:on


                //use this switch for flow control
                switch ( 1 )
                {
                    case 1:

                        //SITUATION 1:
                        if ( ( true == $bIsHeader ) or
                            ( ( false == $bHeaderOnThisPage ) and ( false == $bDataOnThisPage ) and ( false == $this->bTableSplit ) and ( $iLastDataKey > 0 ) )
                        )
                        {
                            $iItems = $this->insertNewPage( $iLastDataKey, null, ( !$bIsHeader ) && ( !$bHeaderOnThisPage ) );
                            break; //exit from switch(1);
                        }

                        $bSplitCommand = $this->bTableSplit;

                        //SITUATION 2:
                        if ( $val[ 'HEIGHT' ] > ( $iPageHeight - $this->nHeaderHeight ) )
                        {
                            //even if the bTableSplit is OFF - split the data!!!
                            $bSplitCommand = true;
                        }

                        if ( $this->bDisablePageBreak )
                        {
                            $bSplitCommand = false;
                        }

                        if ( true == $bSplitCommand )
                        {
                            /**
                             * *************************************************
                             * * * * * * * * * * * * * * * * * * * * * * * * * *
                             * SPLIT IS ACTIVE
                             * * * * * * * * * * * * * * * * * * * * * * * * * *
                             * *************************************************
                             */

                            //if we can draw on this page at least one line from the cells

                            $aData = $val[ 'DATA' ];

                            $fRowH = $iLeftHeightLast;
                            #$fRowH = 0;
                            $fRowHTdata = 0;

                            $aTData = array();

                            //parse the data's on this line
                            for ( $j = 0; $j < $this->nColumns; $j++ )
                            {

                                /** @var $cell Pdf_Table_Cell_Interface */
                                /** @var $cellSplit Pdf_Table_Cell_Interface */

                                $aTData[ $j ] = $aData[ $j ];
                                $cellSplit = & $aTData[ $j ];
                                $cell = & $aData[ $j ];

                                /**
                                 * The cell is Skipped or is a Rowspan.
                                 * For active split we handle rowspanned cells later
                                 */
                                if ( ( $cell->getSkipped() === true ) || ( $cell->getRowSpan() > 1 ) )
                                    continue;

                                if ( $cell->isSplittable() )
                                {
                                    list ( $cellSplit ) = $cell->split( $val[ 'HEIGHT' ], $iLeftHeightLast );
                                    $cell->setCellDrawHeight( $iLeftHeightLast );
                                } else
                                {
                                    $cellSplit = clone $cell;

                                    $o = new Pdf_Table_Cell_Empty( $this->oPdf );
                                    $o->copyProperties( $cell );
                                    $o->setCellDrawWidth( $cell->getCellDrawWidth() );
                                    $o->setCellHeight( $iLeftHeightLast );
                                    $cell = $o;
                                }

                                $fRowH = max( $fRowH, $cell->getCellDrawHeight() );
                                $fRowHTdata = max( $fRowHTdata, $cellSplit->getCellDrawHeight() );
                            }

                            $val[ 'HEIGHT' ] = $fRowH;
                            $val[ 'DATA' ] = $aData;

                            $v_new = $val;
                            $v_new[ 'HEIGHT' ] = $fRowHTdata;
                            $v_new[ 'ROWSPAN' ] = array();
                            /**
                             * Parse separately the rows with the ROWSPAN
                             */

                            $bNeedParseCache = false;

                            $aRowSpan = $aDC[ $i ][ 'ROWSPAN' ];

                            foreach ( $aRowSpans as $rws )
                            {

                                $rData = & $aDC[ $rws[ 0 ] ][ 'DATA' ][ $rws[ 1 ] ];
                                /** @var $rData Pdf_Table_Cell_Interface */

                                if ( $rData->isPropertySet( 'HEIGHT_LEFT_RW' ) && $rData->getCellDrawHeight() > $rData->HEIGHT_LEFT_RW )
                                {
                                    /**
                                     * This cell has a rowspan in IT
                                     * We have to split this cell only if its height is bigger than the space to the end of page
                                     * that was set when the cell was parsed.
                                     * HEIGHT_LEFT_RW
                                     */

                                    //list ($aTData[$rws[1]], $fHeightSplit) = $this->splitCell($rData, $rData->HEIGHT_MAX, $rData->HEIGHT_LEFT_RW);
                                    if ( $rData->isSplittable() )
                                    {
                                        list ( $aTData[ $rws[ 1 ] ], $fHeightSplit ) = $rData->split(
                                            $rData->getCellDrawHeight(), $rData->HEIGHT_LEFT_RW );
                                        $rData->setCellDrawHeight( $rData->HEIGHT_LEFT_RW );
                                    } else
                                    {
                                        $aTData[ $rws[ 1 ] ] = clone $rData;

                                        $o = new Pdf_Table_Cell_Empty( $this->oPdf );
                                        $o->copyProperties( $rData );
                                        $o->setCellDrawWidth( $rData->getCellDrawWidth() );
                                        $o->setCellDrawHeight( $rData->HEIGHT_LEFT_RW );
                                        $rData = $o;
                                        //$rData->setSkipped(true);
                                    }

                                    $aTData[ $rws[ 1 ] ]->setRowSpan( $aTData[ $rws[ 1 ] ]->getRowSpan() - ( $i - $rws[ 0 ] ) );

                                    $v_new[ 'ROWSPAN' ][ ] = $rws[ 1 ];

                                    $bNeedParseCache = true;
                                }
                            }

                            $v_new[ 'DATA' ] = $aTData;

                            //Insert the new page, and get the new number of the lines
                            $iItems = $this->insertNewPage( $i, $v_new );

                            if ( $bNeedParseCache )
                                $this->_cacheParseRowspan( $i + 1 );
                        } else
                        {

                            /**
                             * *************************************************
                             * * * * * * * * * * * * * * * * * * * * * * * * * *
                             * SPLIT IS INACTIVE
                             * * * * * * * * * * * * * * * * * * * * * * * * * *
                             * *************************************************
                             */

                            /**
                             * Check if we have a rowspan that needs to be splitted
                             */

                            $bNeedParseCache = false;

                            $aRowSpan = $aDC[ $i ][ 'ROWSPAN' ];

                            foreach ( $aRowSpans as $rws )
                            {

                                $rData = & $aDC[ $rws[ 0 ] ][ 'DATA' ][ $rws[ 1 ] ];
                                /** @var $rData Pdf_Table_Cell_Interface */

                                if ( $rws[ 0 ] == $i )
                                    continue; //means that this was added at the last line, that will not appear on this page


                                if ( $rData->getCellDrawHeight() > $rData->HEIGHT_LEFT_RW )
                                {
                                    /**
                                     * This cell has a rowspan in IT
                                     * We have to split this cell only if its height is bigger than the space to the end of page
                                     * that was set when the cell was parsed.
                                     * HEIGHT_LEFT_RW
                                     */

                                    list ( $aTData, $fHeightSplit ) = $rData->split( $rData->getCellDrawHeight(),
                                        $rData->HEIGHT_LEFT_RW - $iLeftHeightLast );

                                    /** @var $aTData Pdf_Table_Cell_Interface */

                                    $rData->setCellDrawHeight( $rData->HEIGHT_LEFT_RW - $iLeftHeightLast );

                                    $aTData->setRowSpan( $aTData->getRowSpan() - ( $i - $rws[ 0 ] ) );

                                    $aDC[ $i ][ 'DATA' ][ $rws[ 1 ] ] = $aTData;

                                    $aRowSpan[ ] = $rws[ 1 ];
                                    $aDC[ $i ][ 'ROWSPAN' ] = $aRowSpan;

                                    $bNeedParseCache = true;
                                }
                            }

                            if ( $bNeedParseCache )
                                $this->_cacheParseRowspan( $i );

                            //Insert the new page, and get the new number of the lines
                            $iItems = $this->insertNewPage( $i );
                        }
                }

                $iLeftHeight = $iPageHeight;
                $aRowSpans = array();
                $bDataOnThisPage = false; //new page


            }
        }
    }


    /**
     * Inserts a new page in the Data Cache, after the specified Index.
     * If sent then also a new data is inserted after the new page
     *
     * @param $iIndex integer - after this index the new page inserted
     * @param $rNewData resource - default null. If specified this data is inserted after the new page
     * @param $bInsertHeader boolean - true then the header is inserted, false - no header is inserted
     * @param bool $bRemoveCurrentRow
     * @return integer the new number of lines that the Data Cache Contains.
     */
    protected function insertNewPage( $iIndex = 0, $rNewData = null, $bInsertHeader = true, $bRemoveCurrentRow = false )
    {
        if ( $this->bDisablePageBreak ) return 0;

        $this->bHeaderOnCurrentPage = false;

        //parse the header if for some reason it was not parsed!?
        $this->parseHeader();

        //the number of lines that the header contains
        if ( ( true == $this->bDrawHeader ) && ( true == $bInsertHeader ) && ( $this->bHeaderOnNewPage ) )
        {
            $nHeaderLines = count( $this->aHeaderCache );
        } else
        {
            $nHeaderLines = 0;
        }

        $aDC = & $this->aDataCache;
        $iItems = count( $aDC ); //the number of elements in the cache


        //if we have a NewData to be inserted after the new page then we have to shift the data with 1
        if ( null != $rNewData )
            $iShift = 1;
        else
            $iShift = 0;

        $nIdx = 0;
        if ( $bRemoveCurrentRow )
        {
            $nIdx = 1;
        }

        //shift the array with the number of lines that the header contains + one line for the new page
        for ( $j = $iItems; $j > $iIndex; $j-- )
        {
            $aDC[ $j + $nHeaderLines + $iShift - $nIdx ] = $aDC[ $j - 1 ];
        }

        $aDC[ $iIndex + $iShift ] = array(
            'HEIGHT' => 0,
            'DATATYPE' => 'new_page'
        );

        $j = $iShift;

        if ( $nHeaderLines > 0 )
        {
            //only if we have a header


            //insert the header into the corresponding positions
            foreach ( $this->aHeaderCache as $rHeaderVal )
            {
                $j++;
                $aDC[ $iIndex + $j ] = $rHeaderVal;
            }

            $this->bHeaderOnCurrentPage = true;
        }

        if ( 1 == $iShift )
        {
            $j++;
            $aDC[ $iIndex + $j ] = $rNewData;
        }

        $this->bDataOnCurrentPage = false;

        return count( $aDC );
    }


    /**
     * Sends all the Data Cache to the PDF Document.
     * This is the Function that Outputs the table data to the pdf document
     */
    protected function _cachePrepOutputData()
    {
        //save the old auto page break value
        $oldAutoPageBreak = $this->oPdf->AutoPageBreak;
        $oldbMargin = $this->oPdf->bMargin;

        //disable the auto page break
        $this->oPdf->SetAutoPageBreak( false, $oldbMargin );

        $aDataCache = & $this->aDataCache;

        $iItems = count( $aDataCache );

        for ( $k = 0; $k < $iItems; $k++ )
        {

            $val = & $aDataCache[ $k ];

            //each array contains one line
            $this->_tbAlign();

            if ( $val[ 'DATATYPE' ] == 'new_page' )
            {
                //add a new page
                $this->addPage();
                continue;
            }

            $data = & $val[ 'DATA' ];

            //Draw the cells of the row
            for ( $i = 0; $i < $this->nColumns; $i++ )
            {

                /** @var $cell Pdf_Table_Cell_Interface */
                $cell = & $data[ $i ];

                //Save the current position
                $x = $this->oPdf->GetX();
                $y = $this->oPdf->GetY();

                if ( $cell->getSkipped() === false )
                {

                    //render the cell to the pdf
                    //$data[$i]->render($rowHeight = $val['HEIGHT']);


                    if ( $val[ 'HEIGHT' ] > $cell->getCellDrawHeight() )
                    {
                        $cell->setCellDrawHeight( $val[ 'HEIGHT' ] );
                    }

                    $cell->render();
                }

                $this->oPdf->SetXY( $x + $this->getColumnWidth( $i ), $y );

                //if we have colspan, just ignore the next cells
                if ( $cell->getColspan() > 1 )
                {
                    //$i = $i + (int)$cell->getColspan() - 1;
                }
            }

            $this->bDataOnCurrentPage = true;

            //Go to the next line
            $this->oPdf->Ln( $val[ 'HEIGHT' ] );
        }

        $this->oPdf->SetAutoPageBreak( $oldAutoPageBreak, $oldbMargin );
    }


    /**
     * Prepares the cache for Output.
     * Parses the cache for Rowspans, Paginates the cache and then send the data to the pdf document
     */
    protected function _cachePrepOutput()
    {
        if ( $this->bRowSpanInCache )
            $this->_cacheParseRowspan();

        $this->_cachePaginate();

        $this->_cachePrepOutputData();
    }


    /**
     * Adds a new page in the pdf document and initializes the table and the header if necessary.
     */
    protected function addPage( $bHeader = true )
    {
        $this->drawBorder(); //draw the table border
        $this->_tbEndPageBorder(); //if there is a special handling for end page??? this is specific for me

        $this->oPdf->AddPage( $this->oPdf->CurOrientation ); //add a new page

        $this->bDataOnCurrentPage = false;

        $this->iTableStartX = $this->oPdf->GetX();
        $this->iTableStartY = $this->oPdf->GetY();
        $this->markMarginX();
    }


    /**
     * Sends to the pdf document the cache data
     */
    public function ouputData()
    {
        $this->_cachePrepOutput();
    }


    /**
     * Sets current tag to specified style
     *
     * @param $tag string - tag name
     * @param $family string - text font family name
     * @param $style string - text font style
     * @param $size number - text font size
     * @param $color array - text color
     *
     */
    public function setStyle( $tag, $family, $style, $size, $color )
    {
        $this->oMulticell->setStyle( $tag, $family, $style, $size, $color );
    }


    /**
     * Returns the array value if set otherwise the default
     *
     * @param $var mixed
     * @param $index mixed
     * @param $default mixed
     * @return array value or default
     */
    public static function getValue( $var, $index = '', $default = '' )
    {
        if ( is_array( $var ) )
        {
            if ( isset( $var[ $index ] ) )
            {
                return $var[ $index ];
            }
        }

        return $default;
    }


    /**
     * Returns the table configuration value specified by the input key
     *
     * @param $key string
     * @return mixed
     *
     */
    protected function getTableConfig( $key )
    {
        return self::getValue( $this->aConfiguration[ 'TABLE' ], $key );
    }


    /**
     * Sets the Table Config
     * $aConfig = array( "BORDER_COLOR" => array (120,120,120), //border color "BORDER_SIZE" => 5), //border line width "TABLE_ALIGN" => "L"), //the align of the table, possible values = L, R, C
     * equivalent to Left, Right, Center 'TABLE_LEFT_MARGIN' => 0// left margin...
     * reference from this->lmargin values );
     *
     * @param $aConfig array - array containing the Table Configuration
     *
     */
    public function setTableConfig( $aConfig )
    {
        $this->aConfiguration[ 'TABLE' ] = array_merge( $this->aConfiguration[ 'TABLE' ], $aConfig );
    }

    /**
     * Sets Header configuration values
     *
     * @param array $aConfig
     */
    public function setHeaderConfig( $aConfig )
    {
        $this->aConfiguration[ 'HEADER' ] = array_merge( $this->aConfiguration[ 'HEADER' ], $aConfig );
    }

    /**
     * Sets Row configuration values
     *
     * @param array $aConfig
     */
    public function setRowConfig( $aConfig )
    {
        $this->aConfiguration[ 'ROW' ] = array_merge( $this->aConfiguration[ 'ROW' ], $aConfig );
    }


    /**
     * Returns the header configuration value specified by the input key
     *
     * @param $key string
     * @return mixed
     *
     */
    protected function getHeaderConfig( $key )
    {
        return self::getValue( $this->aConfiguration[ 'HEADER' ], $key );
    }


    /**
     * Returns the row configuration value specified by the input key
     *
     * @param $key string
     * @return mixed
     *
     */
    protected function getRowConfig( $key )
    {
        return self::getValue( $this->aConfiguration[ 'ROW' ], $key );
    }


    /**
     * Returns the default configuration array of the table.
     * The array contains values for the Table style, Header Style and Data Style.
     * All these values can be overwritten when creating the table or in the case of CELLS for every individual cell
     *
     * @return array The Defalt Configuration
     */
    protected function getDefaultConfiguration()
    {
        $aDefaultConfiguration = array();

        require dirname( __FILE__ ) . '/../../table.config.php';

        return $aDefaultConfiguration;
    }


    /**
     * Returns the compatibility map between STRINGS and Constrants
     *
     * @return array
     */
    protected function compatibilityMap()
    {
        //@formatter:off
        return array(
            'TEXT_COLOR' => self::TEXT_COLOR,
            'TEXT_SIZE' => self::TEXT_SIZE,
            'TEXT_FONT' => self::TEXT_FONT,
            'TEXT_ALIGN' => self::TEXT_ALIGN,
            'VERTICAL_ALIGN' => self::VERTICAL_ALIGN,
            'TEXT_TYPE' => self::TEXT_TYPE,
            'LINE_SIZE' => self::LINE_SIZE,
            'BACKGROUND_COLOR' => self::BACKGROUND_COLOR,
            'BORDER_COLOR' => self::BORDER_COLOR,
            'BORDER_SIZE' => self::BORDER_SIZE,
            'BORDER_TYPE' => self::BORDER_TYPE,
            'TEXT' => self::TEXT,
            'PADDING_TOP' => self::PADDING_TOP,
            'PADDING_RIGHT' => self::PADDING_RIGHT,
            'PADDING_LEFT' => self::PADDING_LEFT,
            'PADDING_BOTTOM' => self::PADDING_BOTTOM,
            'TABLE_ALIGN' => self::TABLE_ALIGN,
            'TABLE_LEFT_MARGIN' => self::TABLE_LEFT_MARGIN,
        );
        //@formatter:on
    }


    /**
     * Returns the current type map
     *
     * @return array
     */
    protected function getTypeMap()
    {
        return $this->aTypeMap;
    }


    /**
     * Adds a type/class relationship
     *
     * @param string $name
     * @param string $class
     */
    public function addTypeMap( $name, $class )
    {
        if ( !class_exists( $class ) )
        {
            //fatal error
            trigger_error( "Invalid class specified: $class", E_USER_ERROR );
        }

        $this->aTypeMap[ strtoupper( $name ) ] = $class;
    }


    /**
     * Sets the disable page break value. If TRUE then page-breaks are disabled
     *
     * @param boolean $value
     * @return $this
     */
    public function setDisablePageBreak( $value )
    {
        $this->bDisablePageBreak = (bool)$value;

        return $this;
    }
}


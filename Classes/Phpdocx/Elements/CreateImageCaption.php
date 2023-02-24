<?php
namespace Phpdocx\Elements;
/**
 * Create image caption using text strings
 *
 * @category   Phpdocx
 * @package    elements
 * @copyright  Copyright (c) Narcea Producciones Multimedia S.L.
 *             (http://www.2mdc.com)
 * @license    phpdocx LICENSE
 * @link       https://www.phpdocx.com
 */
class CreateImageCaption extends CreateElement
{
    /**
     *
     * @access private
     * @static
     */
    private static $_instance = null;

    /**
     *
     * @access private
     * @var string
     */
    private $_label;

    /**
     *
     * @access private
     * @var bool
     */
    private $_showLabel;

    /**
     *
     * @access private
     * @var string
     */
    private $_styleName;

    /**
     *
     * @access private
     * @var string
     */
    private $_text;

    /**
     * Construct
     *
     * @access public
     */
    public function __construct()
    {
        
    }

    /**
     * Destruct
     *
     * @access public
     */
    public function __destruct()
    {
        
    }

    /**
     * Magic method, returns current XML
     *
     * @access public
     * @return string Return current XML
     */
    public function __toString()
    {
        return $this->_xml;
    }

    /**
     * Singleton, return instance of class
     *
     * @access public
     * @return CreateCaption
     * @static
     */
    public static function getInstance()
    {
        if (self::$_instance == NULL) {
            self::$_instance = new CreateImageCaption();
        }
        return self::$_instance;
    }

    /**
     * Getter. Access to label value var
     *
     * @access public
     * @return string
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Getter. Access to show label var
     *
     * @access public
     * @return bool
     */
    public function getShowLabel()
    {
        return $this->_showLabel;
    }

    /**
     * Getter. Access to text value var
     *
     * @access public
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Create Caption
     *
     * @access public
     * @param string $arrArgs[0] Text to add
     */
    public function createCaption()
    {
        $this->_xml = '';
        $args = func_get_args();

        $this->generateP();
        $this->generatePPR();
        $this->generatePSTYLE($this->_styleName);
        $this->generateR();
        
        if ($this->_showLabel) {
            $this->generateT($this->_label . ' ');
        } else {
            $this->generateT('');
        }

        $this->generateFldSimple();
        if($this->_text != ''){
            $this->generateR();
            $this->generateT($this->_text);
        }
    }

    /**
     * Init a link to assign values to variables
     *
     * @access public
     * @param bool $arrArgs[0]['showLabel'] Text to add
     * @param string $arrArgs[0]['text'] URL to add
     */
    public function initCaption()
    {
        $args = func_get_args();

        if (!isset($args[0]['showLabel'])) {
            $args[0]['showLabel'] = true;
        }
        if (!isset($args[0]['text'])) {
            $args[0]['text'] = '';
        }

        $this->_showLabel = $args[0]['showLabel'];
        $this->_text = $args[0]['text'];
        $this->_label = $args[0]['label'];
        $this->_styleName = $args[0]['styleName'];
    }

    /**
     * Create fldSimple 
     *
     * @access private
     */
    private function generateFldSimple()
    {
        $begin = '<'. CreateElement::NAMESPACEWORD .':fldSimple '. CreateElement::NAMESPACEWORD  .':instr=" SEQ '.$this->_styleName.' \* ARABIC ">
        <'. CreateElement::NAMESPACEWORD .':r><'. CreateElement::NAMESPACEWORD .':rPr><'. CreateElement::NAMESPACEWORD .':noProof/></'. CreateElement::NAMESPACEWORD .':rPr><'. CreateElement::NAMESPACEWORD .':t>';
        $end = '</'. CreateElement::NAMESPACEWORD .':t></'. CreateElement::NAMESPACEWORD .':r></'. CreateElement::NAMESPACEWORD  .':fldSimple>__GENERATESUBR__';

        $simpleField = $begin . \Phpdocx\Create\CreateDocx::$captionsIds . $end;
        $this->_xml = str_replace('__GENERATESUBR__', $simpleField, $this->_xml);
    }
}

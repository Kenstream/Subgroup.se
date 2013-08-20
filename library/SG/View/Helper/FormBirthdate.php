<?php
/**
 *
 * @category   Subgroup
 * @package    SG_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 Agustinus Prasetyo (agustinus.widodo@gmail.com)
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a "birthdate" element
 *
 * @category   Subgroup
 * @package    SG_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 Agustinus Prasetyo (agustinus.widodo@gmail.com)
 */
class SG_View_Helper_FormBirthdate extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'date' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are used in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formBirthdate($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // build the element
        $disabled = '';
        if ($disable) {
            // disabled
            $disabled = ' disabled="disabled"';
        }

        $dateScript = '<script>'
            . '$(function() {'
            . ' $( "#' . $this->view->escape($id) . '" ).datepicker({changeMonth: true,changeYear: true,yearRange: "-60:-15",dateFormat:"yy-mm-dd"});'
            . '});'
            . '</script>';

        $xhtml = '<input type="text"'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . ' value="' . $this->view->escape($value) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs)
                . $this->getClosingBracket()
                . $dateScript;

        return $xhtml;
    }
}

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
 * Helper to generate a "submit" element
 *
 * @category   Subgroup
 * @package    SG_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 Agustinus Prasetyo (agustinus.widodo@gmail.com)
 */
class SG_View_Helper_FormSubmitWithContainer extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'submit' button.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formSubmitWithContainer($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable, id
        // check if disabled
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        if ($id) {
            $id = ' id="' . $this->view->escape($id) . '"';
        }

        // Render the button.
        $xhtml = '<div class="form-actions"><input type="submit"'
               . ' name="' . $this->view->escape($name) . '"'
               . $id
               . ' value="' . $this->view->escape($value) . '"'
               . $disabled
               . $this->_htmlAttribs($attribs)
               . $this->getClosingBracket()
               . '</div>';

        return $xhtml;
    }
}

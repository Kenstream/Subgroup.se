<?php

/**
* Send Mail
*
* @param    string  $email      email destination
* @param    string  $to         receiver name
* @param    string  $subject    subject of the email
* @param    string  $filename   filename to the HTML template
* @param    array   $params     additional param values to use in the template
*
* @category   Subgroup
* @package    SG_Controller_Action_Helper
* @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
* @copyright  2013 Agustinus Prasetyo
* @version    SVN: $Id$
*/

class SG_Controller_Action_Helper_SendMail extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($email, $to, $subject, $filename, $params = array())
    {
        $html = new Zend_View();
        $html->setScriptPath(Zend_Registry::get('setting')->email->templatePath);

        // assign valeues
        $html->assign('email', $email);
        $html->assign('name', $to);
        $html->assign('params', $params);

        // create mail object
        $mail = new Zend_Mail('utf-8');

        // render view
        $bodyText = $html->render($filename);

        // configure base stuff
        $mail->addTo($email, $to);
        $mail->setSubject($subject);
        $mail->setBodyHtml($bodyText);

        return $mail->send(Zend_Registry::get('mailTransport'));
    }
}

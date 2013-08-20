<?php

/**
 * Controller that handles the notification for the admin.
 *
 * This controller handles the notification by the project managed by the admin.
 *
 * @category   Subgroup
 * @package    Controller
 * @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
 * @copyright  2013 Agustinus Prasetyo
 * @version    SVN: $Id$
 * @link       https://cloud.subgroup.se
 */

class NotificationController extends SG_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view->leftMenuIndex = 7;
    }

    /**
     * Show the list of the user result if any result occured
     */
    public function indexAction()
    {

    }

}


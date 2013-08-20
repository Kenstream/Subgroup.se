<?php

class SG_View_Helper_PrintCategories extends Zend_View_Helper_Abstract
{
    public $view;

    public function PrintCategories($assessmentCategory, $htmlToPrint = '')
    {
        if (!is_null($assessmentCategory->getParentCategory())) {
            $this->view->loopIndex += 1;
            $htmlToPrint .= '<tr>' .
                                '<td>'. $this->view->loopIndex .'</td>' .
                                '<td>'. $assessmentCategory->getName() .'</td>' .
                                '<td style="max-width: 350px;">'. $assessmentCategory->getDescription() .'</td>' .
                                '<td style="min-width: 150px;"><i class="icon-tag"></i> '. $assessmentCategory->getParentCategory()->getName() .'</td>' .
                                '<td>' .
                                    '<div class="btn-group">' .
                                      '<a href="'. $this->view->routeUrl('cloud-assessment-edit', array('id' => $assessmentCategory->getId())) . '" class="btn"><i class="icon-edit"></i></a>' .
                                      '<a href="'. $this->view->routeUrl('cloud-assessment-delete', array('id' => $assessmentCategory->getId())) . '" class="btn btn-remove"><i class="icon-remove"></i></a>' .
                                    '</div>' .
                                '</td>' .
                            '</tr>';

        }

        if (sizeof($assessmentCategory->getChildCategories()) > 0) {
            foreach($assessmentCategory->getChildCategories() as $childCategory) {
                $htmlToPrint = $this->PrintCategories($childCategory, $htmlToPrint);
            }
        }

        return $htmlToPrint;
    }

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }
}
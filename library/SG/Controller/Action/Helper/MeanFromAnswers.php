<?php

/**
* Calculate the means/average of collections of Answers
*
* @category   Subgroup
* @package    SG_Controller_Action_Helper
* @author     Agustinus Prasetyo Widodo <agustinus.widodo@gmail.com>
* @copyright  2013 Agustinus Prasetyo
* @version    SVN: $Id$
*/

class SG_Controller_Action_Helper_MeanFromAnswers extends Zend_Controller_Action_Helper_Abstract
{
    public function direct($answers)
    {
        $answerValues = array();
        foreach($answers as $answer) {
            if($answer->getElement()->getType() == Entities\Scenario\Element::TYPE_RADIO ||
                $answer->getElement()->getType() == Entities\Scenario\Element::TYPE_SELECT) {
                $decodedJson = json_decode($answer->getElement()->getJsonDefaultValue(),true);
                $maxJsonDefaultValue = 0;
                foreach($decodedJson['values'] as $key => $value) {
                    if ($key > $maxJsonDefaultValue)
                        $maxJsonDefaultValue = $key;
                }

                if($maxJsonDefaultValue != 5) {
                    $answerValues[] = ((int)$answer->getValue() != 0) ? ((int)$answer->getValue()/$maxJsonDefaultValue)*5 : 0;
                } else {
                    $answerValues[] = (int)$answer->getValue();
                }
            }
        }

        $zeroKeys = array_keys($answerValues, 0);
        return (sizeof($answerValues) > 0) ? array_sum($answerValues)/(sizeof($answerValues) - sizeof($zeroKeys)) : 0;
    }
}

<?php

class SG_View_Helper_Alert extends Zend_View_Helper_Abstract
{
    const TYPE_WARNING  = 'alert-warning';
    const TYPE_ERROR    = 'alert-error';
    const TYPE_SUCCESS  = 'alert-success';
    const TYPE_INFO     = 'alert-info';

    public function alert($messages = array())
    {
        $alertHtml = '';

        if (sizeof($messages) > 0) {
            foreach($messages as $message) {

                $headerHtml = '';
                $messageText = '';
                $messageType = self::TYPE_INFO;

                if (is_array($message)) {
                    $messageText = $message['text'];
                    $messageType = $message['type'];

                    if(array_key_exists('headerText', $message))
                        $headerHtml = sprintf('<h4>%s</h4>', $message['headerText']);

                } else {
                    $messageText = $message;
                }

                $alertHtml .= sprintf('<div class="alert alert-block %s"><button type="button" class="close" data-dismiss="alert">&times;</button>%s %s</div>',
                    $messageType, $headerHtml, $messageText);

            }
        }

        return $alertHtml;
    }
}
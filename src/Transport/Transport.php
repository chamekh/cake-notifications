<?php
namespace Notifications\Transport;

use Notifications\Notification\Notification;

abstract class Transport
{

    /**
     * Performs the before- or after send callback of the notification
     *
     * @param array $items Contains the class and function name and optional, function params
     * @param Notification $notificationInstance Reference to the notification instance for a possible callbacks callback
     * @throws InvalidArgumentException
     */
    protected static function _performCallback(array $items, Notification &$notificationInstance = null)
    {
        $success = false;
        foreach ($items as $item) {
            if (!isset($item['class']) || !is_callable($item['class'])) {
                $class = $item['class'];
                if (is_array($item['class'])) {
                    $class = implode($item['class']);
                }
                throw new \InvalidArgumentException("{$class} is not callable");
            }

            $args = [];
            if (isset($item['args']) && is_array($item['args'])) {
                $args = $item['args'];
            }

            if (is_array($item['class']) && count($item['class']) == 2) {
                $className = $item['class'][0];
                $methodName = $item['class'][1];
                $instance = new $className;
                $success = call_user_func_array([$instance, $methodName], $args);
            } elseif (is_string($item['class'])) {
                $success = call_user_func_array($item['class'], $args);
            }
            if (is_callable($success)) {
                $success($notificationInstance);
            }
        }
    }
}

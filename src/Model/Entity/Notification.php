<?php
/**
 * Bakkerij (https://github.com/bakkerij)
 * Copyright (c) https://github.com/bakkerij
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) https://github.com/bakkerij
 * @link          https://github.com/bakkerij Bakkerij Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bakkerij\Notifier\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Utility\Text;

/**
 * Notification Entity.
 */
class Notification extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'template' => true,
        'vars' => true,
        'tracking_id' => true,
        'user_id' => true,
        'state' => false,
        'user' => false,
    ];

    /**
     * _getVars
     *
     * Getter for the vars-column.
     *
     * @param string $vars Data.
     * @return mixed
     */
    protected function _getVars($vars)
    {
        $array = json_decode($vars, true);

        if (is_object($array)) {
            return $array;
        }

        return $vars;
    }

    /**
     * _setVars
     *
     * Setter for the vars-column
     *
     * @param array $vars Data.
     * @return string
     */
    protected function _setVars($vars)
    {
        if (is_array($vars)) {
            return json_encode($vars);
        }

        return $vars;
    }

    /**
     * _getTitle
     *
     * Getter for the title.
     * Data is used from the vars-column.
     * The template is used from the configurations.
     *
     * @return string
     */
    protected function _getTitle()
    {
        $templates = Configure::read('Notifier.templates');

        if (array_key_exists($this->_properties['template'], $templates)) {
            $template = $templates[$this->_properties['template']];

            $vars = json_decode($this->_properties['vars'], true);

            return Text::insert($template['title'], $vars);
        }

        return '';
    }

    /**
     * _getBody
     *
     * Getter for the body.
     * Data is used from the vars-column.
     * The template is used from the configurations.
     *
     * @return string
     */
    protected function _getBody()
    {
        $templates = Configure::read('Notifier.templates');

        if (array_key_exists($this->_properties['template'], $templates)) {
            $template = $templates[$this->_properties['template']];

            $vars = json_decode($this->_properties['vars'], true);

            return Text::insert($template['body'], $vars);
        }

        return '';
    }

    /**
     * _getUnread
     *
     * Boolean if the notification is read or not.
     *
     * @return bool
     */
    protected function _getUnread()
    {
        if ($this->_properties['state'] === 1) {
            return true;
        }

        return false;
    }

    /**
     * _getRead
     *
     * Boolean if the notification is read or not.
     *
     * @return bool
     */
    protected function _getRead()
    {
        if ($this->_properties['state'] === 0) {
            return true;
        }

        return false;
    }

    /**
     * Virtual fields
     *
     * @var array
     */
    protected $_virtual = ['title', 'body', 'unread', 'read'];
}

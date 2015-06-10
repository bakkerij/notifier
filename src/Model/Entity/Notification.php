<?php
/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Notifier\Model\Entity;

use Cake\ORM\Entity;
use Cake\Utility\Text;
use Cake\Core\Configure;

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
        'user_id' => true,
        'state' => false,
        'user' => false,
    ];

    protected function _getVars($vars)
    {
        $array = json_decode($vars, true);

        if(is_object($array)) {
            return $array;
        }

        return $vars;
    }

    protected function _setVars($vars)
    {
        if(is_array($vars)) {
            return json_encode($vars);
        }

        return $vars;
    }

    protected function _getTitle()
    {
        $templates = Configure::read('Notifier.templates');

        $template = $templates[$this->_properties['template']];

        $vars = json_decode($this->_properties['vars'], true);

        return Text::insert($template['title'], $vars);
    }

    protected function _getBody()
    {
        $templates = Configure::read('Notifier.templates');

        $template = $templates[$this->_properties['template']];

        $vars = json_decode($this->_properties['vars'], true);

        return Text::insert($template['body'], $vars);
    }

    protected function _getUnread()
    {
        if($this->_properties['state'] === 1) {
            return true;
        }
        return false;
    }

    protected function _getRead()
    {
        if($this->_properties['state'] === 0) {
            return true;
        }
        return false;
    }
}

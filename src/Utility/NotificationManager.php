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
namespace Notifier\Utility;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

/**
 * Notifier component
 */
class NotificationManager
{

    protected static $_generalManager = null;

    public static function instance($manager = null)
    {
        if ($manager instanceof NotificationManager) {
            static::$_generalManager = $manager;
        }
        if (empty(static::$_generalManager)) {
            static::$_generalManager = new NotificationManager();
        }
        return static::$_generalManager;
    }

    /**
     * notify
     *
     * Sends notifications to specific users.
     * The first parameter `$data` is an array with muliple options.
     *
     * ### Options
     * - `users` - An array or int with id's of users who will receive a notification.
     * - `roles` - An array or int with id's of roles which all users ill receive a notification.
     * - `template` - The template wich will be used.
     * - `vars` - The variables used in the template.
     *
     * @param array $data Data with options.
     * @return void
     */
    public function notify($data)
    {
        $model = TableRegistry::get('Notifier.Notifications');

        $_data = [
            'users' => [],
            'recipientLists' => [],
            'template' => 'default',
            'vars' => [],
            'tracking_id' => $this->getTrackingId()
        ];

        $data = array_merge($_data, $data);

        foreach ((array)$data['recipientLists'] as $recipientList) {
            $list = $this->getRecipientList($recipientList);
            $data['users'] = $data['users'] + $list;
        }

        foreach ($data['users'] as $user) {
            $entity = $model->newEntity();

            $entity->set('template', $data['template']);
            $entity->set('tracking_id', $data['tracking_id']);
            $entity->set('vars', $data['vars']);
            $entity->set('user_id', $user);

            $model->save($entity);
        }

        return $data['tracking_id'];
    }

    public function addRecipientList($name, $userIds)
    {
        Configure::write('Notifier.recipientLists.' . $name, $userIds);
    }

    public function getRecipientList($name)
    {
        return Configure::read('Notifier.recipientLists.' . $name);
    }

    public function getTrackingId()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $trackingId = '';
        for ($i = 0; $i < 10; $i++) {
            $trackingId .= $characters[rand(0, $charactersLength - 1)];
        }
        return $trackingId;
    }
}

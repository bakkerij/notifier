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

    /**
     * instance
     *
     * The singleton class uses the instance() method to return the instance of the NotificationManager.
     *
     * @param null $manager Possible different manager. (Helpfull for testing).
     * @return Notifier\Utility\NotificationManager
     */
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
     * The first parameter `$data` is an array with multiple options.
     *
     * ### Options
     * - `users` - An array or int with id's of users who will receive a notification.
     * - `roles` - An array or int with id's of roles which all users ill receive a notification.
     * - `template` - The template wich will be used.
     * - `vars` - The variables used in the template.
     *
     * ### Example
     * ```
     *  NotificationManager::instance()->notify([
     *      'users' => 1,
     *      'template' => 'newOrder',
     *      'vars' => [
     *          'receiver' => $receiver->name
     *          'total' => $order->total
     *      ],
     *  ]);
     * ```
     *
     * @param array $data Data with options.
     * @return string The tracking_id to follow the notification.
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
            $list = (array)$this->getRecipientList($recipientList);
            $data['users'] = array_merge($data['users'], $list);
        }

        foreach ((array)$data['users'] as $user) {
            $entity = $model->newEntity();

            $entity->set('template', $data['template']);
            $entity->set('tracking_id', $data['tracking_id']);
            $entity->set('vars', $data['vars']);
            $entity->set('state', 1);
            $entity->set('user_id', $user);

            $model->save($entity);
        }

        return $data['tracking_id'];
    }
    

    /**
     * notifyI18n
     *
     * Sends notifications to specific users.
     * The first parameter `$data` is an array with multiple options.
     *
     * ### Options
     * - `users` - An array or int with id's of users who will receive a notification.
     * - `roles` - An array or int with id's of roles which all users ill receive a notification.
     * - `template` - The template wich will be used.
     * - `vars` - The localized variables used in the template.
     *
     * ### Example
     * ```
     *  NotificationManager::instance()->notify([
     *      'users' => 1,
     *      'template' => 'newOrder',
     *      'vars' => [
     *          'en' => [
     *              'receiver' => $receiver->name
     *              'link' => '/en/order/],
     *          'fr' => [
     *              'receiver' => $receiver->name
     *              'link' => '/fr/order/],
     *      ],
     *  ]);
     * ```
     *
     * @param array $data Data with options.
     * @return string The tracking_id to follow the notification.
     */
    public function notifyI18n($data)
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

        foreach ((array) $data['recipientLists'] as $recipientList) {
            $list = (array) $this->getRecipientList($recipientList);
            $data['users'] = array_merge($data['users'], $list);
        }

        foreach ((array) $data['users'] as $user) {
            $entity = $model->newEntity();
            
            $entity->set('template', $data['template']);
            $entity->set('tracking_id', $data['tracking_id']);
                
            $entity->set('vars', current($data['vars']));
            foreach ($data['vars'] as $lang => $vars) {
                $entity->translation($lang)->set(['vars' => $vars], ['guard' => false]);
            }
            
            $entity->set('state', 1);
            $entity->set('user_id', $user);
            
            $model->save($entity);
        }

        return $data['tracking_id'];
    }

    /**
     * addRecipientList
     *
     * Method to add a new recipient list.
     * Recipient lists are used to create presets of users to write notifications to.
     *
     * ### Example
     * ```
     *  $notificationManager->addRecipientList('administrators', [1,2,3,4]);
     * ```
     *
     * The data will be stored in Cake's Configure: `Notifier.recipientLists.{name}`
     *
     * @param string $name Name of the list.
     * @param array $userIds Array with id's of users.
     * @return void
     */
    public function addRecipientList($name, $userIds)
    {
        Configure::write('Notifier.recipientLists.' . $name, $userIds);
    }

    /**
     * getRecipientList
     *
     * Returns a requested recipient list from Cake's Configure.
     * Will return `null` if the list doesn't exist.
     *
     * @param string $name The name of the list.
     * @return array|null
     */
    public function getRecipientList($name)
    {
        return Configure::read('Notifier.recipientLists.' . $name);
    }

    /**
     * addTemplate
     *
     * Adds a template to the storage.
     *
     * ### Variables
     * Titles and bodies can contain variables. For that the
     * `Cake\Utilities\Text::insert($string, $data)` is used:
     * http://book.cakephp.org/3.0/en/core-libraries/text.html#Cake\Utility\Text::insert
     *
     * ### Options
     * - `title` - The title.
     * - `body` - The body.
     *
     * ### Example
     *
     * $this->Notifier->addTemplate('newUser', [
     *  'title' => 'New User: :name',
     *  'body' => 'The user :email has been registered'
     * ]);
     *
     * This code contains the variables `title` and `body`.
     *
     * @param string $name Unique name.
     * @param array $options Options.
     * @return void
     */
    public function addTemplate($name, $options = [])
    {
        $_options = [
            'title' => 'Notification',
            'body' => '',
        ];

        $options = array_merge($_options, $options);

        Configure::write('Notifier.templates.' . $name, $options);
    }

    /**
     * addI18nTemplate
     *
     * Adds localized template.
     *
     * ### Example
     *
     * $this->Notifier->addTemplate('newUser', [
     *  'en' => [
     *    'title' => 'New User: :name',
     *    'body' => 'The user :email has been registered'],
     *  'fr' => [
     *    'title' => 'Nouvel utilisateur : :name',
     *    'body' => 'L\'utilisateur :email vient de s\'inscrire'],
     * ]);
     *
     * This code contains the variables `title` and `body`.
     *
     * @param string $name Unique name.
     * @param array $options Options.
     * @return void
     */
    public function addI18nTemplate($name, $options = [])
    {
        $_options = [
            'en' => [
                'title' => 'Notification',
                'body' => '',
            ]
        ];

        $options = array_merge($_options, $options);

        Configure::write('Notifier.templates.i18n.' . $name, $options);
    }

    /**
     * getTemplate
     *
     * Returns the requested template.
     * If the template or type does not exists, `false` will be returned.
     *
     * @param string $name Name of the template.
     * @param string|null $type The type like `title` or `body`. Leave empty to get the whole template.
     * @return array|string|bool
     */
    public function getTemplate($name, $type = null)
    {
        $templates = Configure::read('Notifier.templates');

        if (array_key_exists($name, $templates)) {
            if ($type == 'title') {
                return $templates[$name]['title'];
            }
            if ($type == 'body') {
                return $templates[$name]['body'];
            }
            return $templates[$name];
        }

        return false;
    }

    /**
     * getI18nTemplate
     *
     * Returns the requested localized template.
     * If the template or type does not exists, `false` will be returned.
     *
     * @param string $lang Language code.
     * @param string $name Name of the template.
     * @param string|null $type The type like `title` or `body`. Leave empty to get the whole template.
     * @return array|string|bool
     */
    public function getI18nTemplate($lang, $name, $type = null)
    {
        $templates = Configure::read('Notifier.templates.i18n');

        if (array_key_exists($name, $templates)) {
            if ($type == 'title') {
                return $templates[$name]['title'];
            }
            if ($type == 'body') {
                return $templates[$name]['body'];
            }
            return $templates[$name];
        }

        return false;
    }

    /**
     * getTrackingId
     *
     * Generates a tracking id for a notification.
     *
     * @return string
     */
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

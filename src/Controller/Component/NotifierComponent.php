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
namespace Notifier\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

/**
 * Notifier component
 */
class NotifierComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'UsersModel' => 'Users'
    ];

    /**
     * The controller.
     *
     * @var \Cake\Controller\Controller
     */
    private $Controller = null;

    /**
     * initialize
     *
     * @param array $config Config.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->Controller = $this->_registry->getController();

        Configure::write('Notifier.config', $this->config());
    }

    /**
     * setController
     *
     * Setter for the Controller property.
     *
     * @param \Cake\Controller\Controller $controller Controller.
     * @return void
     */
    public function setController($controller)
    {
        $this->Controller = $controller;
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

        Configure::write('Notifier.templates.'.$name, $options);
    }

    /**
     * notificationList
     *
     * @param int $user User id.
     * @return void
     */
    public function notificationList($user = null)
    {
//        if(!$user) {
//            $user = $this->Controller->Auth->user();
//        } else {
//
//        }
    }

    /**
     * notificationCount
     *
     * @param int $user User id.
     * @return int
     */
    public function notificationCount($user = null)
    {
        if (!$user) {
            $user = $this->Controller->Auth->user('id');
        }

        $model = TableRegistry::get('Notifier.Notifications');

        $query = $model->find('all')->where([
            'user_id' => $user,
            'state' => 1
        ]);

        return $query->count();
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

        $model->notify($data);
    }
}

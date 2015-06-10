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
namespace Notifier\Model\Table;

use Cake\Core\Configure;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * Notifications Model
 */
class NotificationsTable extends Table
{

    /**.
     * Configurations
     * @var array
     */
    public $config = [];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('notifications');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->config = Configure::read('Notifier.config');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->allowEmpty('title')
            ->allowEmpty('body')
            ->add('state', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('state');

        return $validator;
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
        $_data = [
            'users' => [],
            'roles' => [],
            'template' => 'default',
            'vars' => []
        ];

        $data = array_merge($_data, $data);

        $receivers = $this->_getReceivers($data);

        foreach ($receivers as $receiver) {
            $entity = $this->newEntity();

            $entity->set('template', $data['template']);
            $entity->set('vars', $data['vars']);
            $entity->set('user_id', $receiver);

            $this->save($entity);
        }
    }

    /**
     * _getReceivers
     *
     * Generates a list of all receivers based on users and roles.
     *
     * @param array $data Data.
     * @return array
     */
    protected function _getReceivers($data)
    {
        $receivers = [];

        if (!is_array($data['users'])) {
            $data['users'] = (array)$data['users'];
        }
        $users = [];
        foreach ($data['users'] as $user) {
            $users[$user] = $user;
        }

        $receivers = $receivers + $users;

        $roles = $this->_roleToUsers($data['roles']);

        $receivers = $receivers + $roles;

        return $receivers;
    }

    /**
     * _roleToUsers
     *
     * Converts an array with role_id's into an array with all user-id's.
     *
     * @param array|int $roles Role-id's.
     * @return array
     */
    protected function _roleToUsers($roles)
    {
        $model = TableRegistry::get($this->config['UsersModel']);

        $data = [];

        if (!is_array($roles)) {
            $roles = (array)$roles;
        }

        foreach ($roles as $role) {
            $query = $model->find('list', [
                'keyField' => 'id',
                'valueField' => 'id'
            ])->where(['Users.role_id' => $role])->toArray();

            $data = $data + $query;
        }

        return $data;
    }

    /**
     * getTemplate
     *
     * Getter for template-variables.
     * If the key or template couldn't be found, `false` will be returned.
     *
     * @param string $template Template to use.
     * @param string|null $key The key to get (like `body` or `title`)
     * @return mixed
     */
    public function getTemplate($template, $key = null)
    {
        $templates = Configure::read('Notifier.templates');

        if (!key_exists($template, $templates)) {
            return false;
        }

        $template = $templates[$template];

        if ($key === null) {
            return $template;
        }

        if (!key_exists($key, $template)) {
            return false;
        }

        return $template[$key];
    }
}

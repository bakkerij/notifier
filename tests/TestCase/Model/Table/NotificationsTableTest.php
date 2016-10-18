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
namespace Bakkerij\Notifier\Test\TestCase\Model\Table;

use Bakkerij\Notifier\Utility\NotificationManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Notifier\Model\Table\NotificationsTable Test Case
 */
class NotificationsTableTest extends TestCase
{

    public $fixtures = [
        'plugin.bakkerij\Notifier.notifications',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Notifications = TableRegistry::get('Bakkerij/Notifier.Notifications');
    }

    public function tearDown()
    {
        unset($this->Notifications);

        parent::tearDown();
    }

    public function testEntity()
    {
        NotificationManager::instance()->addTemplate('newNotification', [
            'title' => 'New Notification',
            'body' => ':from has sent :to a notification about :about'
        ]);

        $notify = NotificationManager::instance()->notify([
            'users' => 1,
            'template' => 'newNotification',
            'vars' => [
                'from' => 'Bob',
                'to' => 'Leonardo',
                'about' => 'Programming Stuff'
            ]
        ]);

        $entity = $this->Notifications->get(2);

        $this->assertEquals('newNotification', $entity->template);
        $this->assertEquals('New Notification', $entity->title);
        $this->assertEquals('Bob has sent Leonardo a notification about Programming Stuff', $entity->body);
    }
}

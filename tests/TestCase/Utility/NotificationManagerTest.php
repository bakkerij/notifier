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
namespace Bakkerij\Notifier\Test\TestCase\Utility;

use Bakkerij\Notifier\Utility\NotificationManager;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class NotificationManagerTest extends TestCase
{

    public $fixtures = [
        'plugin.bakkerij\Notifier.notifications'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Manager = NotificationManager::instance();

        $this->Model = TableRegistry::get('Bakkerij/Notifier.Notifications');
    }

    public function tearDown()
    {
        unset($this->Manager);
        unset($this->Model);

        parent::tearDown();
    }

    public function testInstance()
    {
        $instance = NotificationManager::instance();
        $this->assertInstanceOf('Bakkerij\Notifier\Utility\NotificationManager', $instance);
    }

    public function testNotificationFailWithEmpty()
    {
        $this->assertEquals(1, $this->Model->find()->count());

        $notify = $this->Manager->notify([]);

        $this->assertNotNull($notify);

        $this->assertEquals(1, $this->Model->find()->count());
    }

    public function testNotificationPassWithSingleUser()
    {
        $this->assertEquals(1, $this->Model->find()->count());

        $notify = $this->Manager->notify([
            'users' => 1
        ]);

        $this->assertEquals(1, $this->Model->find()->where(['tracking_id' => $notify])->count());

        $entity = $this->Model->get(2);

        $this->assertEquals('default', $entity->template);
        $this->assertEquals(1, $entity->user_id);
        $this->assertEquals(1, $entity->state);
        $this->assertEquals($notify, $entity->tracking_id);

        $this->assertEquals(2, $this->Model->find()->count());
    }

    public function testNotificationPassWithMultipleUsers()
    {
        $this->assertEquals(1, $this->Model->find()->count());

        $notify = $this->Manager->notify([
            'users' => [1, 2, 3]
        ]);

        $this->assertEquals(3, $this->Model->find()->where(['tracking_id' => $notify])->count());

        $entity = $this->Model->get(2);

        $this->assertEquals('default', $entity->template);
        $this->assertEquals(1, $entity->user_id);
        $this->assertEquals(1, $entity->state);
        $this->assertEquals($notify, $entity->tracking_id);

        $entity = $this->Model->get(3);

        $this->assertEquals('default', $entity->template);
        $this->assertEquals(2, $entity->user_id);
        $this->assertEquals(1, $entity->state);
        $this->assertEquals($notify, $entity->tracking_id);

        $entity = $this->Model->get(4);

        $this->assertEquals('default', $entity->template);
        $this->assertEquals(3, $entity->user_id);
        $this->assertEquals(1, $entity->state);
        $this->assertEquals($notify, $entity->tracking_id);

        $this->assertEquals(4, $this->Model->find()->count());
    }

    public function testNotificationPassWithTitleAndBody()
    {
        $this->Manager->addTemplate('newNotification', [
            'title' => 'New Notification',
            'body' => ':from has sent :to a notification about :about'
        ]);

        $notify = $this->Manager->notify([
            'users' => 1,
            'template' => 'newNotification',
            'vars' => [
                'from' => 'Bob',
                'to' => 'Leonardo',
                'about' => 'Programming Stuff'
            ]
        ]);

        $entity = $this->Model->get(2);

        $this->assertEquals('newNotification', $entity->template);
        $this->assertEquals('New Notification', $entity->title);
        $this->assertEquals('Bob has sent Leonardo a notification about Programming Stuff', $entity->body);
    }

    public function testNotificationPassWithRecipientLists()
    {
        $this->Manager->addRecipientList('administrators', [1, 2, 3, 4, 5]);

        $this->assertEquals(1, $this->Model->find()->count());

        $notify = $this->Manager->notify([
            'recipientLists' => 'administrators'
        ]);

        $this->assertEquals(5, $this->Model->find()->where(['tracking_id' => $notify])->count());
        $this->assertEquals(6, $this->Model->find()->count());
    }

    public function testAddRecipientList()
    {
        $this->Manager->addRecipientList('administrators', [1, 2, 3, 4, 5]);

        $list = Configure::read('Notifier.recipientLists.administrators');

        $expected = [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5];

        $this->assertEquals($expected, $list);
    }

    public function testGetRecipientList()
    {
        Configure::write('Notifier.recipientLists.administrators', [1, 2, 3, 4, 5]);

        $expected = [0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5];

        $this->assertEquals($expected, $this->Manager->getRecipientList('administrators'));
    }

    public function testAddTemplate()
    {
        $this->Manager->addTemplate('newNotification', [
            'title' => 'New Notification',
            'body' => ':from has sent :to a notification about :about'
        ]);

        $result = Configure::read('Notifier.templates.newNotification');

        $expected = [
            'title' => 'New Notification',
            'body' => ':from has sent :to a notification about :about'
        ];

        $this->assertEquals($expected, $result);
    }

    public function testGetTemplate()
    {
        Configure::write('Notifier.templates.newNotification', [
            'title' => 'New Notification',
            'body' => ':from has sent :to a notification about :about'
        ]);

        $expected = [
            'title' => 'New Notification',
            'body' => ':from has sent :to a notification about :about'
        ];

        $this->assertEquals($expected, $this->Manager->getTemplate('newNotification'));
        $this->assertEquals($expected['title'], $this->Manager->getTemplate('newNotification', 'title'));
        $this->assertEquals($expected['body'], $this->Manager->getTemplate('newNotification', 'body'));
    }

    public function testGetTrackingId()
    {
        $this->assertEquals(10, strlen($this->Manager->getTrackingId()));
    }
}

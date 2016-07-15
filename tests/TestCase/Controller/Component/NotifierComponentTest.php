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
namespace Bakkerij\Notifier\Test\TestCase\Controller\Component;

use Bakkerij\Notifier\Controller\Component\NotifierComponent;
use Bakkerij\Notifier\Utility\NotificationManager;
use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * Notifier\Controller\Component\NotifierComponent Test Case
 */
class NotifierComponentTest extends TestCase
{

    public $fixtures = [
        'plugin.bakkerij\Notifier.notifications'
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Manager = NotificationManager::instance();
        $this->Model = TableRegistry::get('Bakkerij/Notifier.Notifications');

        // Setup our component and fake the controller
        $request = new Request();
        $response = new Response();

        $this->controller = $this->getMockBuilder('Cake\Controller\Controller')
            ->setConstructorArgs([$request, $response])
            ->setMethods(['redirect'])
            ->getMock();

        $this->controller->loadComponent('Auth');

        $this->controller->Auth->setUser([
            'id' => 1,
        ]);

        $registry = new ComponentRegistry($this->controller);
        $this->Notifier = new NotifierComponent($registry);
    }

    public function tearDown()
    {
        unset($this->Notifier);
        unset($this->Manager);
        unset($this->Model);

        parent::tearDown();
    }

    public function testCountNotifications()
    {
        $this->Manager->notify(['users' => [1, 1, 1, 2, 2]]);

        $this->assertEquals(3, $this->Notifier->countNotifications(1));
        $this->assertEquals(3, $this->Notifier->countNotifications(1, true));
        $this->assertEquals(0, $this->Notifier->countNotifications(1, false));
        $this->assertEquals(2, $this->Notifier->countNotifications(2));
        $this->assertEquals(2, $this->Notifier->countNotifications(2, true));
        $this->assertEquals(0, $this->Notifier->countNotifications(2, false));

        $this->Notifier->markAsRead(2, 1);
        $this->Notifier->markAsRead(5, 2);
        $this->Notifier->markAsRead(6, 2);

        $this->assertEquals(3, $this->Notifier->countNotifications(1));
        $this->assertEquals(2, $this->Notifier->countNotifications(1, true));
        $this->assertEquals(1, $this->Notifier->countNotifications(1, false));
        $this->assertEquals(2, $this->Notifier->countNotifications(2));
        $this->assertEquals(0, $this->Notifier->countNotifications(2, true));
        $this->assertEquals(2, $this->Notifier->countNotifications(2, false));
    }

    public function testGetNotifications()
    {
        $this->Manager->notify(['users' => [1, 1, 1, 2, 2]]);

        $this->assertEquals(3, count($this->Notifier->getNotifications(1)));
        $this->assertEquals(3, count($this->Notifier->getNotifications(1, true)));
        $this->assertEquals(0, count($this->Notifier->getNotifications(1, false)));
        $this->assertEquals(2, count($this->Notifier->getNotifications(2)));
        $this->assertEquals(2, count($this->Notifier->getNotifications(2, true)));
        $this->assertEquals(0, count($this->Notifier->getNotifications(2, false)));

        $this->Notifier->markAsRead(2, 1);
        $this->Notifier->markAsRead(5, 2);
        $this->Notifier->markAsRead(6, 2);

        $this->assertEquals(3, count($this->Notifier->getNotifications(1)));
        $this->assertEquals(2, count($this->Notifier->getNotifications(1, true)));
        $this->assertEquals(1, count($this->Notifier->getNotifications(1, false)));
        $this->assertEquals(2, count($this->Notifier->getNotifications(2)));
        $this->assertEquals(0, count($this->Notifier->getNotifications(2, true)));
        $this->assertEquals(2, count($this->Notifier->getNotifications(2, false)));
    }

    public function testMarkAsReadWithAuth()
    {
        $this->Manager->notify(['users' => [1, 1, 1, 1, 1]]);

        $this->assertEquals(5, $this->Notifier->countNotifications(null, true));

        $this->Notifier->markAsread(2);

        $this->assertEquals(4, $this->Notifier->countNotifications(null, true));

        $this->Notifier->markAsread();

        $this->assertEquals(0, $this->Notifier->countNotifications(null, true));
    }
}

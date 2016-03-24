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
namespace Notifier\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Network\Request;
use Cake\Network\Response;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Notifier\Controller\Component\NotifierComponent;
use Notifier\Utility\NotificationManager;

/**
 * Notifier\Controller\Component\NotifierComponent Test Case
 */
class NotifierComponentTest extends TestCase
{

    public $fixtures = [
        'plugin.notifier.notifications'
    ];

    public function setUp()
    {
        parent::setUp();

        $this->Manager = NotificationManager::instance();
        $this->Model = TableRegistry::get('Notifier.Notifications');

        // Setup our component and fake the controller
        $request = new Request();
        $response = new Response();

        $this->controller = $this->getMock('Cake\Controller\Controller', ['redirect'], [$request, $response]);
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
        $this->assertEquals(1, count($this->Notifier->getNotifications(1, true, ['limit' => 1])));
        $this->assertEquals(2, count($this->Notifier->getNotifications(2, false, ['limit' => 3])));
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

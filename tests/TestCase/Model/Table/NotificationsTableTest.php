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

namespace Notifier\Test\TestCase\Model\Table;

use Cake\I18n\I18n;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Notifier\Model\Table\NotificationsTable;
use Notifier\Utility\NotificationManager;

/**
 * Notifier\Model\Table\NotificationsTable Test Case
 */
class NotificationsTableTest extends TestCase
{

    public $fixtures = [
        'plugin.notifier.notifications',
        'core.translates'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->Notifications = TableRegistry::get('Notifier.Notifications');
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

    public function testI18nEntity()
    {
        NotificationManager::instance()->addI18nTemplate('newOrder', [
            'en' => [
                'title' => 'New order',
                'body' => ':username bought :product'
            ],
            'fr' => [
                'title' => 'Nouvelle commande',
                'body' => ':username a acheté :product'
            ]
        ]);

        $notify = NotificationManager::instance()->notifyI18n([
            'users' => 1,
            'template' => 'newOrder',
            'vars' => [
                'en' => [
                    'username' => 'Bob',
                    'product' => 'a car'
                ],
                'fr' => [
                    'username' => 'Bob',
                    'product' => 'une voiture'
                ]
            ]
        ]);

        I18n::locale('en');
        $entity = $this->Notifications->get(2);

        $this->assertEquals('newOrder', $entity->template);
        $this->assertEquals('New order', $entity->getI18n('title', 'en'));
        $this->assertEquals('Bob bought a car', $entity->getI18n('body', 'en'));

        I18n::locale('fr');
        $entity = $this->Notifications->get(2);

        $this->assertEquals('newOrder', $entity->template);
        $this->assertEquals('Nouvelle commande', $entity->getI18n('title', 'fr'));
        $this->assertEquals('Bob a acheté une voiture', $entity->getI18n('body', 'fr'));
    }

}

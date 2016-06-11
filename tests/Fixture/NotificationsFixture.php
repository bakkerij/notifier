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
namespace CakePlugins\Notifier\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * NotificationsFixture
 *
 */
class NotificationsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => [
            'type' => 'integer',
            'autoIncrement' => true
        ],
        'template' => [
            'type' => 'string'
        ],
        'vars' => [
            'type' => 'text'
        ],
        'user_id' => [
            'type' => 'integer'
        ],
        'state' => [
            'type' => 'integer'
        ],
        'tracking_id' => [
            'type' => 'text'
        ],
        'created' => [
            'type' => 'datetime',
            'default' => null
        ],
        'modified' => [
            'type' => 'datetime',
            'default' => null
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB', 'collation' => 'latin1_swedish_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'template' => 'newNotification',
            'vars' => '{"name":"Bob"}',
            'state' => 1,
            'tracking_id' => 'aaZ97ZA',
            'created' => '2015-05-11 19:48:21',
            'modified' => '2015-05-11 19:48:21'
        ],
    ];
}

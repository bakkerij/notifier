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
use Phinx\Migration\AbstractMigration;

class TrackingId extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('notifications');
        $table
            ->addColumn('tracking_id', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->dropTable('notifications');
    }
}

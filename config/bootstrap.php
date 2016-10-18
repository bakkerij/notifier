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

use Cake\Core\Configure;

Configure::write('Notifier.templates.default', [
    'title' => ':title',
    'body' => ':body'
]);

Configure::write('Notifier.recipientLists', []);


collection((array)Configure::read('Notifier.config'))->each(function ($file) {
    Configure::load($file);
});

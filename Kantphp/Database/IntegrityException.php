<?php

/**
 * @package KantPHP
 * @author  Zhenqiang Zhang <565364226@qq.com>
 * @copyright (c) 2011 KantPHP Studio, All rights reserved.
 * @license http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Kant\Database;

/**
 * Exception represents an exception that is caused by violation of DB constraints.
 *
 */
class IntegrityException extends \Exception {

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName() {
        return 'Integrity constraint violation';
    }

}

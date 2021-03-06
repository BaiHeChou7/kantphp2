<?php

/**
 * @package KantPHP
 * @author  Zhenqiang Zhang <565364226@qq.com>
 * @copyright (c) 2011 KantPHP Studio, All rights reserved.
 * @license http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Kant\Exception;

/**
 * InvalidParamException represents an exception caused by invalid parameters passed to a method.
 *
 */
class InvalidParamException extends \BadMethodCallException {

    /**
     * @return string the user-friendly name of this exception
     */
    public function getName() {
        return 'Invalid Parameter';
    }

}

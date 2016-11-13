<?php

/**
 * @package KantPHP
 * @author  Zhenqiang Zhang <565364226@qq.com>
 * @copyright (c) 2011 - 2015 KantPHP Studio, All rights reserved.
 * @license http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Kant\Database;

use Kant\Exception\KantException;
use Kant\Registry\KantRegistry;

/**
 * Database driver class
 * 
 * @access private
 * @final
 */
final class Driver {

    /**
     *
     * Static instance of factory mode
     *
     */
    private static $_database;

    /**
     *
     * Database config list
     *
     */
    private $_dbConfig = array();

    /**
     *
     * Database list
     *
     */
    private $_dbList = array();

    /**
     *
     * Construct
     *
     */
    public function __construct() {
        
    }

    /**
     *
     * Get instantce of the final object
     *
     * @param string $dbConfig
     * @return object on success
     */
    public static function getInstance($dbConfig = '') {
        if ($dbConfig == '') {
            $dbConfig = KantRegistry::get('config')->get('database');
        }
        if (self::$_database == '') {
            self::$_database = new self();
        }
        if ($dbConfig != '' && $dbConfig != self::$_database->_dbConfig) {
            self::$_database->_dbConfig = array_merge($dbConfig, self::$_database->_dbConfig);
        }
        return self::$_database;
    }

    /**
     *
     * Get instance of the _database config
     *
     * @param String db_name
     * @return resource a _database link identifier on success, or false on
     * failure.
     */
    public function getDatabase($db_name) {
        if (!isset($this->_dbList[$db_name]) || !is_object($this->_dbList[$db_name])) {
            $this->_dbList[$db_name] = $this->connect($db_name);
        }
        return $this->_dbList[$db_name];
    }

    /**
     *
     *  Load database driver
     *
     * @param db_name string
     * @return object on success
     */
    public function connect($db_name) {
        $dbType = $this->_dbConfig[$db_name]['type'];
        if (empty($dbType)) {
            throw new \InvalidArgumentException('Underfined db type');
        }
        $class = "Kant\\Database\\Driver\\" .  ucfirst($dbType);
        if (!class_exists($class)) {
            throw new KantException(sprintf('Unable to load Database Driver: %s', $this->_dbConfig[$db_name]['type']));
        }
        $object = new $class;
        $object->open($this->_dbConfig[$db_name]);
        $object->dbTablepre = $this->_dbConfig[$db_name]['tablepre'];
        return $object;
    }

    /**
     *
     * Close _database connection
     *
     */
    protected function close() {
        foreach ($this->_dbList as $db) {
            $db->close();
        }
    }

    /**
     *
     * Destruct
     */
    public function __destruct() {
        $this->close();
    }

}

<?php

/**
 * @package KantPHP
 * @author  Zhenqiang Zhang <565364226@qq.com>
 * @copyright (c) 2011 KantPHP Studio, All rights reserved.
 * @license http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 */

namespace Kant\Driver\Session;

/**
 * Session Memcache
 * 
 * @access private
 * @static
 * @version 1.1
 * @since version 1.1
 */
class Session {

    protected $sidpre = 'sess_';
    //Session setting: gc_maxlifetime, auth_key;
    private $_setting;
    //Session Model
    private $_sessM;

    public function __construct($setting) {
        $this->_setting = $setting;
        require_once KANT_PATH . 'Session/Sqlite/SessionSqliteModel.php';
        $this->_sessM = new SessionSqliteModel();
        self::_setSessionModule();
    }

    /**
     * Set Session Module
     */
    private function _setSessionModule() {
        session_module_name('user');
        session_set_save_handler(
                array($this, 'open'), array($this, 'close'), array($this, 'read'), array($this, 'write'), array($this, 'destroy'), array($this, 'gc')
        );
        register_shutdown_function('session_write_close');
        session_start();
    }

    public function open() {
        return true;
    }

    public function close() {
        $maxlifetime = $this->_setting['maxlifetime'] ? $this->_setting['maxlifetime'] : ini_get('session.gc_maxlifetime');
        return self::gc($maxlifetime);
    }

    /**
     * READ SESSION
     * 
     * @param string $sid
     * @return string
     */
    public function read($sid) {
        $sessionid = $this->sidpre . $sid;
        $row = $this->model->readSession($sessionid);
        if ($row) {
            $data = $row[0]['data'];
            return $data;
        }
    }

    /**
     * Write Session
     * 
     * @param string $sid
     * @param string $data
     * @return boolean
     */
    public function write($sid, $data) {
        $sessionid = $this->sidpre . $sid;
        $exist = $this->model->readSession($sessionid);
        if (!$exist) {
            $row = $this->model->saveSession(array(
                'sessionid' => $sessionid,
                'data' => $data,
                'lastvisit' => time(),
                'ip' => get_client_ip()
            ));
        } else {
            $row = $this->model->saveSession(array(
                'data' => $data,
                'lastvisit' => time(),
                'ip' => get_client_ip()
                    ), $sessionid);
        }
        return $row;
    }

    public function destroy($sid) {
        $this->_sessM->delete($sid);
        return true;
    }

    public function gc($maxlifetime) {
        $expiretime = time() - $maxlifetime;
        $this->_sessM->deleteExpire($expiretime);
        return true;
    }

}

?>

<?php

namespace Kant\Traits;
use Kant\KantFactory;

trait UrlTrait {

    public function url($url = '', $vars = '', $suffix = true) {
        $originalparams = array();
        $config = KantFactory::getConfig()->reference();
        if (strpos($url, $config['url_suffix']) !== false) {
            $url = rtrim($url, $config['url_suffix']);
        }
        $info = parse_url($url);
        if (isset($info['fragment'])) {
            $anchor = $info['fragment'];
            if (false !== strpos($anchor, '?')) {
                list($anchor, $info['query']) = explode('?', $anchor, 2);
            }
        }
        if (!empty($info['host'])) {
            return $url;
        }
        // 解析参数
        if (is_string($vars)) { // aaa=1&bbb=2 转换成数组
            parse_str($vars, $vars);
        } elseif (!is_array($vars)) {
            $vars = array();
        }
        if (isset($info['query'])) { // 解析地址里面参数 合并到vars
            parse_str($info['query'], $params);
            $vars = array_merge($params, $vars);
        }

        $depr = "/";
        $url = trim($url, $depr);
        $path = explode($depr, $url);
        $var['module'] = $path[0];
        $var['ctrl'] = !empty($path[1]) ? $path[1] : $config['route']['ctrl'];
        $var['act'] = !empty($path[2]) ? $path[2] : $config['route']['act'];
        if (!empty($path[3])) {
            $restpath = array_slice($path, 3);
            foreach ($restpath as $key => $val) {
                $arr = preg_split("/[,:=-]/", $val, 2);
                $originalparams[$arr[0]] = isset($arr[1]) ? $arr[1] : '';
            }
        }
        $url = APP_URL . implode($depr, ($var));
        $vars = array_merge($originalparams, $vars);
        if (!empty($vars)) { // 添加参数
            foreach ($vars as $var => $val) {
                if ('' !== trim($val)) {
//					$url .= $depr . $var . "," . urlencode($val);
                    $url .= $depr . $var . "," . $val;
                }
            }
        }
        //$url = rtrim($url, "/");
        if ($suffix) {
            $suffix = $suffix === true ? $config['url_suffix'] : $suffix;
            if ($pos = strpos($suffix, '|')) {
                $suffix = substr($suffix, 0, $pos);
            }
            //if ($suffix && '/' != substr($url, -1)) {
            if ($suffix) {
                $url .= $suffix;
            }
        }
        if (isset($anchor)) {
            $url .= '#' . $anchor;
        }
        return $url;
    }
    
    /**
     * 
     * Page redirection with message 
     * 
     * @param string $message
     * @param string $url
     * @param integer $second
     */
    public function redirect($message, $url = 'goback', $second = 3) {
        $redirectTpl = KantFactory::getConfig()->get('redirect_tpl');
        if ($redirectTpl) {
            include TPL_PATH . $redirectTpl . '.php';
        } else {
            include KANT_PATH . 'View' . DIRECTORY_SEPARATOR . 'system/redirect.php';
        }
        exit();
    }

}

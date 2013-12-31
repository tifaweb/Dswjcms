<?php
require_once './config.inc.php';
require_once './uc_client/client.php';
load('extend');

class UcApi{
    static protected $lastAction = '';
    static protected $lastErrorCode = '';
    static protected $authPre = 'bpi_';
    static protected $uid = '';
    static protected $username = '';
    static protected $password = '';
    static protected $email ='';
    static protected $errorCode = array(
        'reg' => array(
            '-1' => '用户名不合法' ,
            '-2' => '包含不允许注册的词语' ,
            '-3' => '用户名已经存在' ,
            '-4' => 'Email格式有误' ,
            '-5' => 'Email不允许注册' ,
            '-6' => '该Email已经注册' ,
        ) ,
        
        'login' => array(
            '-1' => '用户不存在' ,
            '-2' => '密码错误' ,
            '-3' => '安全提示问答错误' ,
        ),
        
        'checkemail' => array(
            '-4' => 'Email格式错误' ,
            '-5' => '该Email不允许注册' ,
            '-6' => '该Email已经被注册' ,
        ),
        
        'checkname' => array(
            '-1' => '用户名不合法' ,
            '-2' => '包含不允许注册的词语' ,
            '-3' => '用户名已存在' ,
        ),
        
        'addfeed' => array(
            '0' => '增加事件动态失败' ,
        ),
        
    );
    
    static function login($username, $password, $isuid = 0) {
       
       list($uid, $username, $password, $email) = uc_user_login($username, $password, $isuid);
       setcookie(self::$authPre . 'auth', '', -86400);
       if($uid > 0) {
           self::$uid = $uid;
           self::$username = $username;
           self::$password = md5($password);
           self::$email = $email;
           setcookie(self::$authPre . 'auth', uc_authcode($uid . "\t" . $username . "\t" . md5($password) . "\t" . $email, 'ENCODE'));
           return array(
               'uid' => $uid ,
               'username' => $username ,
               'password' => $password ,
               'email'  => $email ,
               'synlogin' => uc_user_synlogin($uid),
           );

           
       } else{
            self::$lastAction = 'login';
            self::$lastErrorCode = $uid;
            return FALSE;
       }
    }
    
    static function reg($username, $password, $email, $autologin = false) {
        $ip = get_client_ip();
        $zhuce = uc_user_register($username, $password, $email, '', '',$ip);
        if($zhuce > 0) {
            if($autologin){
                self::$uid = $uid;
                self::$username = $username;
                self::$password = md5($password);
                self::$email = $email;
                setcookie(self::$authPre . 'auth', uc_authcode($uid . "\t" . $username . "\t" . md5($password) . "\t" . $email, 'ENCODE'));
            }
            return $zhuce;   //返回UID
        } else {
            self::$lastAction = 'reg';
            self::$lastErrorCode = $zhuce;
            return FALSE;
        }
    }
    
    static function logout() {
        setcookie(self::$authPre . 'auth', '', -86400);
        return uc_user_synlogout();
    }
    
    static function addFeed($uid, $username, $url, $where, $action, $event, $desc, $images =array()) {
        $feed = array();
        $feed['icon'] = 'thread';
        $feed['title_template'] = '<b>{username} 在{where}{action}了{event}</b>';
        $feed['title_data'] = array(
            'username' => $username ,
            'where' => $where ,
            'action' => $action ,
            'event' => $event ,
            );
        $feed['body_template'] = '<br>{message}';
        $feed['body_data'] = array(
            'message' => cutstr(strip_tags(preg_replace("/\[.+?\]/is", '', $desc)), 150) ,
        );
        $feed['images'] = $images;
        
        $addfeed = uc_feed_add($feed['icon'], $uid, $username, $feed['title_template'], $feed['title_data'], $feed['body_template'], $feed['body_data'], '', '', $feed['images']);
        
        if($addfeed > 0) {
            return $addfeed;
        } else {
            self::$lastAction = 'addfeed';
            self::$lastErrorCode = $addfeed;
            return FALSE;
        }

    }
    
    static function checkEmail($email) {
        $checkemail = uc_user_checkemail();
        if($checkemail > 0) {
            return TRUE;
        }else{
            self::$lastAction = 'checkemail';
            self::$lastErrorCode = $checkemail;
            return FALSE;
        }
    }

    static function checkName($username) {
        $checkname = uc_user_checkname();
        if($checkname > 0) {
            return TRUE;
        }else{
            self::$lastAction = 'checkname';
            self::$lastErrorCode = $checkname;
            return FALSE;
        }
    }
    
    static function isLogin () {
       return self::getUserByCookie();
    }
    
    static function getUserByCookie() {
        if(!empty($_COOKIE[self::$authPre . 'auth'])) {
            list(self::$uid, self::$username, self::$password, self::$email) = explode("\t", uc_authcode($_COOKIE[self::$authPre . 'auth'], 'DECODE'));
            return array(
                'uid' => self::$uid,
                'username' => self::$username,
                'password' => self::$password,
                'email' => self::$email,
            );
        } else {
            return FALSE;
        }

    }
    
    static function getUid() {
        if(empty(self::$uid)) {
            self::getUserByCookie();
        }  
            return self::$uid;
    }
    
    static function getUserName() {
        if(empty(self::$username)) {
            self::getUserByCookie();
        }  
            return self::$username;
    }
    
    static function getPassWord() {
        if(empty(self::$password)) {
            self::getUserByCookie();
        }  
            return self::$password;
    }
    
    static function getEmail() {
        if(empty(self::$email)) {
            self::getUserByCookie();
        }  
            return self::$email;
    }
    
    static function getError() {
        //return self::$lastErrorCode = '' ? '' :  '错误代码: ' . self::$lastErrorCode . ' : ' . self::$errorCode[self::$lastAction][self::$lastErrorCode];
        return self::$lastErrorCode = '' ? '' :  '错误: ' . self::$errorCode[self::$lastAction][self::$lastErrorCode];
    }
}
?>
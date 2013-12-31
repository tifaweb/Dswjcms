<?php
class SMSxc{
    const USER='88331';
    CONST PWD='dy88331';
    CONST URL='http://211.147.244.114:9801/CASServer/SmsAPI/SendMessage.jsp?';
    
    public function send($mobile,$msg){
        $msg=  iconv("GBK", "UTF-8", $msg);
        $data=array(
            'userid'=>self::USER,
            'password'=>self::PWD,
            'destnumbers'=>$mobile,
            'msg'=>$msg
        );
        $query=http_build_query($data);
        $query=self::URL.$query;
//        die($query);
        $ret = @file_get_contents($query);
        //简单判断，应该使用XML来判断为佳
        if(stripos($ret, 'return="0"')){
            return true;
        }else{
            return false;
        }
    }
    
}
//$sms=new SMSxc();
//$sms->send('15867253361', 'i love 人民币');
?>

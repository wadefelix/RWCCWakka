<?php
// 摘录自 http://blog.ipodmp.com/archives/php-url-parameter-transfer-encoding-problem-with-chinese-characters/
function safeEncoding($string,$outEncoding = 'UTF-8')  
{  
    $encoding = 'UTF-8';
    for($i=0;$i<strlen($string);$i++)  
    {  
        if(ord($string{$i})<128)  
            continue;  
 
        if((ord($string{$i})&224)==224)  
        {  
            //第一个字节判断通过  
            $char = $string{++$i};  
            if((ord($char)&128)==128)  
            {  
                //第二个字节判断通过  
                $char = $string{++$i};  
                if((ord($char)&128)==128)  
                {  
                    $encoding = 'UTF-8';  
                    break;  
                }  
            }  
        }  
        if((ord($string{$i})&192)==192)  
        {  
            //第一个字节判断通过  
            $char = $string{++$i};  
            if((ord($char)&128)==128)  
            {  
                //第二个字节判断通过  
                $encoding = 'GB2312';  
                break;  
            }  
        }  
    }  
      
    if(strtoupper($encoding) == strtoupper($outEncoding))  
        return $string;  
    else 
        return iconv($encoding,$outEncoding,$string);  
}

?>

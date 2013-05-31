<?php
    //require_once("array_diff.php");
    if (!function_exists("TextDiff")) {
        /**
        *
        * This is the short Description for the Function
        *
        * This is the long description for the Class
        *
        * @return mixed  Description
        * @access public
        * @see  ??
        */
        function TextDiff($a, $b) {
            $bodyA = explode("\n", $a);
            $bodyB = explode("\n", $b);
             
            $result['added'] = array_diff($bodyA, $bodyB);
            $result['deleted'] = array_diff($bodyB, $bodyA);
	    
	    return $result;
        }
         
         
    }
     
     
?>

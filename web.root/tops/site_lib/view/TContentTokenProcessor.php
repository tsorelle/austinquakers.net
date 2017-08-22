<?php
/** Class: TContentTokenProcessor ***************************************/
/// process replacement tokens content text
/**
*****************************************************************/
class TContentTokenProcessor
{
    public function __construct() {
    }

    public function __toString() {
        return 'TContentTokenProcessor';
    }

    private function transformToken($token) {
        TTracer::Trace('transformToken');
        $parts = explode(':',$token);
        $name = $parts[1];
        $person = TPerson::FindByName($name);
        if ((!$person) || $person->getId() == 0) {
            drupal_set_message("Unable to find directory entry for '".$name);
            return $token.']';
        }

        $result = sprintf('<a href="http://%s/directory?cmd=showPerson&pid=%d">%s %s</a>',
            $_SERVER["SERVER_NAME"],
            $person->getId(),
            $person->getFirstName(),
            $person->getLastName());

        return $result;

    }

   private function doReplaceTokens($tokenName,$remainder) {
        TTracer::Trace('doReplaceTokens');
        $body = '';
        $searchValue = "[$tokenName:";
        $before = true;
        $found = true;
        while ($found) {
            $found = strpos($remainder,$searchValue);
            if ($found) {
                $before = substr($remainder,0,$found);
                $end = strpos($remainder,']',$found);
                if (!$end) {
                    drupal_set_message("Warning: Unterminated $tokenName token.");
                    return false;
                }

                $tag = substr($remainder,$found,$end - $found);
                $remainder = substr($remainder,$end + 1);
                $value = $this->transformToken($tag);
                $body .= $before.$value;
            }
        }
        return $body.$remainder;

   }


   private static $_instance;
   private function getInstance() {
       if (!isset(self::$_instance))
            self::$_instance = new TContentTokenProcessor();
       return self::$_instance;
   }

    public static function ReplaceTokens($tokenName,$text) {
        $instance = self::GetInstance();
        return $instance->doReplaceTokens($tokenName,$text);
    }
}
// end TContentTokenProcessor
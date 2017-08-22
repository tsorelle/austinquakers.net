<?php
/*****************************************************************
Class:  TTextChecker
Description:
*****************************************************************/
class TTextChecker
{
    public static function BadText($text) {
        $text = strtolower($text);
        $badWords = array(
              'adipex',
              'cialis',
              'c i a l i s',
              'viagra',
              'v i a g r a',
              'xenical',
              'casino',
              'xxx',
              'penis',
              'tits',
              'ringtones',
              'ring tones',
              'home loan',
              'home loans',
              'home-loan',
              'home-loans',
              'home equity',
              'home-equity',
              'debt consolidation',
              'payday loans',
              'payday loan',
              'payday-loans',
              'payday-loan',
              'porn',
              'adult-sex',
              'nude pic',
              'nude-pic',
              'sex clip',
              'sex-clip',
              'hentai'
              );
        foreach($badWords as $word)
            if (preg_match('/\b'.$word.'\b/',$text))
                return true;

        return false;

    }

    public static function IsSpam($name, $email, $subject, $body, $spamTrap = '')
    {
            if (!empty($spamTrap))
                return true;

            if (stristr($subject,'http:'))
                  return true;

            if (isset($_SERVER['HTTP_REFERER'])) {
                $url = strtolower($_SERVER['HTTP_HOST']);
                $url = ereg_replace("www.", "", $url);
                if (!ereg($url,strtolower($_SERVER['HTTP_REFERER'])))
                    //Security Violation: Unauthorized referer
                    return true;
            }

            if (
                    ereg("\n|\r", $name)||
                    ereg("\n|\r", $subject) ||
                    (strlen($name) > 100) ||
                     (strlen($subject) > 150)
                    )
                    return true;

            if (($email=="") || (!ereg("^([[:alnum:]\_\.\-]+)(\@[[:alnum:]\.\-]+\.+[[:alpha:]]+)$", $email)) || (strlen($email)>100))
                // Invalid recipient field
                return true;

            if ((ereg("\n|\r", $name)) || (ereg("\n|\r", $subject)) || (strlen($name)>100))
                // Invalid recipient name field
                return true;

            if (($email!="") && (!ereg("^([[:alnum:]\_\.\-]+)(\@[[:alnum:]\.\-]+\.+[[:alpha:]]+)$", $email)))
                // invalid email
                return true;;

            if ((ereg("\n|\r", $subject)) || (strlen($subject)>100))
                return true;

            if (  TTextChecker::BadText($name) ||
                  TTextChecker::BadText($email) ||
                  TTextChecker::BadText($subject) ||
                  TTextChecker::BadText($body))
                return true;

          return false;
    }  //  isSpam


}
// end TTextChecker




<?php
class TRequest {
    private  $requestVars;
    private static $_instance;
    private $cleanInput = false;

    public function __construct($clean = false)
    {
        $this->cleanInput = $clean;
        global $HTTP_SERVER_VARS;
        global $HTTP_GET_VARS;
        global $HTTP_POST_VARS;
        global $_SERVER;
        global $_GET;
        global $_POST;

        if (isset($_SERVER["REQUEST_METHOD"])) {
          if ($_SERVER["REQUEST_METHOD"] == 'GET')
            $this->requestVars = $_GET;
          else
            $this->requestVars = array_merge($_POST,$_GET);
        }
        else if (isset($HTTP_SERVER_VARS["REQUEST_METHOD"])) {
          if  ($HTTP_SERVER_VARS["REQUEST_METHOD"] == 'GET')
            $this->requestVars = $HTTP_GET_VARS;
          else
             $this->requestVars  = $HTTP_POST_VARS;
        }
        else
          exit('Cannot determine request method');
    }  //  TRequest

    public function getMultipleByPrefix($varName)
    {
        $result = array();
        foreach($this->requestVars as $name => $value) {
            if (strpos($name,$varName) === 0) {
                array_push(
                    $result,
                    $this->cleanInput ? self::clean($value) : $value);
            }
        }
        return $result;
    }  //  getMultipleByPrefix

    public function getMultiple($varName)
    {
        $result = array();
        foreach($this->requestVars as $name => $value) {
            if ($name == $varName) {
                array_push(
                    $result,
                    $this->cleanInput ? self::clean($value) : $value);
            }
        }
        return $result;

    }  //  getMultiple
    public function get($varName,$default=false)
    {
       // TTracer::Assert($this->cleanInput, 'clean');
      if (!empty($this->requestVars[$varName])) {
        $result = $this->requestVars[$varName];
        return $this->cleanInput ? self::clean($result) : $result;
      }
      else
        return $default;
    }  //  get

    public function getNoSlashes($varName,$default=false)
    {
      if (!empty($this->requestVars[$varName])) {
        $result = stripslashes($this->requestVars[$varName]);
        return $this->cleanInput ? self::clean($result) : $result;
      }
      else
        return $default;
    }  //  getNoSlashes


    public function getVar($varName)
    {
        return $this->get($varName,'');
    }  //  getVar

    public function set($varName, $value)
    {
      $this->requestVars[$varName] = $value;
    }  //  set

    public function isChecked($varName)
    {
        return (isset($this->requestVars[$varName] ));
    }  //  isChecked

    public function includes($varName)
    {
        return (isset($this->requestVars[$varName] ));
    }  //  includes

    public function getVars()
    {
        return $this->requestVars;
    }  //  getVars

    public function getSelectedValue($prefix,$default='')
    {
        $exp = "^$prefix";
        $keys = array_keys($this->requestVars);
        foreach($keys as $key )
        {
            if (ereg($exp,$key)) {
                $result = ereg_replace($exp,'',$key);
                if (!empty($result))
                    return $this->cleanInput ? self::clean($result) : $result;
            }
        }
        return $default;
    }  //  getSelectedValue

    public function getSelectedValues($prefix)
    {
        $exp = "^$prefix";
        $keys = array_keys($this->requestVars);
        $result = array();
        foreach($keys as $key )
        {
            if (ereg($exp,$key)) {
                $value = ereg_replace($exp,'',$key);
                if (!empty($value))
                    array_push(
                        $result,
                        $this->cleanInput ? self::clean($value) : $value);
            }
        }
        return $result;
    }  //  getSelectedValue

    public function findButtonCommand()
    {
        $keys = array_keys($this->requestVars);
        foreach($keys as $key )
        {

            if (ereg('Button$',$key)) {

                $prefix = ereg_replace('Button$','',$key);
                $result = $this->get('on'.ucfirst($prefix),false);
                if (!empty($result))
                    return $result;
            }
        }
        return false;
    }  //  findButtonCommand

    public function getCommand($default=NULL, $onCancel=NULL, $cancelButtonName='cancelButton',  $commandName = 'cmd')
    {
        $result = $this->findButtonCommand();
        if (!empty($result))
            return $result;

        if (isset($this->requestVars[$cancelButtonName]))
            return isset($onCancel)?$onCancel:$default;

        return ($this->get($commandName,$default));
    }

    /*
    public static function clean($text) {
      if (is_numeric($text) || strlen($text) == 0 )
        return $text;
      if (preg_match('/^./us', $text) != 1)
         return '';
      return str_replace('&', '&amp;', htmlspecialchars($text, ENT_NOQUOTES));
    }
    */

    function clean($text) {
        static $search;
        if (is_numeric($text) || strlen($text) == 0 )
        return $text;
        if (preg_match('/^./us', $text) != 1)
         return '';
        if (!isset($search))
            $search = array(
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
            );

        return preg_replace($search, '', $text);
    }



    public function getInput($varName,$default=false) {
        $state = $this->cleanInput;
        $this->cleanInput = true;
        $result = $this->get($varName,$default);
        $this->cleanInput = $state;
        return $result;

    }





    public function enableInputCleaning($value=true) {
        TTracer::Trace('enambleInputCleaning');
        $this->cleanInput = $value;
    }


    public function getRequestVars()
    {
        return $this->requestVars;
    }  //  getRequestVars

    public function dump() {
        echo '<div style="background-color:white; text-align:left"><pre>';
        echo 'Request arguments:'."\n";
        print_r($this->requestVars);
        echo '</pre></div>';
    }

    public static function GetInstance() {
        if (!isset(TRequest::$_instance))
            self::$_instance = new TRequest();
        return self::$_instance;
    }

    public static function GetValue($varName,$default=false) {
        return TRequest::GetInstance()->get($varName,$default);
    }

    public static function GetInputValue($varName,$default=false) {
        $request = TRequest::GetInstance();
        $request->enableInputCleaning();
        return $request->get($varName,$default);
    }


    public static function GetCommandId($default=NULL, $onCancel=NULL, $cancelButtonName='cancelButton',  $commandName = 'cmd')  {
        return TRequest::GetInstance()->getCommand($default, $onCancel, $cancelButtonName, $commandName);
    }

    public static function PrintArgs() {
        TRequest::GetInstance()->dump();
    }




} // end TRequest

  // reference for backward compatibility
  $request = TRequest::GetInstance();



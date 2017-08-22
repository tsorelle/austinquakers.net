<?php
/*****************************************************************
Used to disable error checking for functions which might run afowl
of base_dir restriction.
*****************************************************************/
function ignoreFileErrors(){}  //  ignoreFileErrors

/**************************************************************
Handles a variety of file and directory manipulation operations.
***************************************************************/
class TFilePath
{
    private $path;

    public function __construct($path) {
        $this->path = TFilePath::Expand($path);
        if ($this->path === false)
            throw new Exception("File path not found: $path");
    }

    public function __toString() {
        return $this->path;
    }

    public function getPath() {
        return $this->path;
    }

    public static function getDocumentRoot() {
        global $_SERVER;
        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $root = $_SERVER['DOCUMENT_ROOT'];
        }
        if (empty($root)) {
            // assumes that startup script has set CWD to document root
            // see austinquakers.org/drupaljobs.php
            $root = getcwd();
        }
        return $root;
    }

    public static function Exists($path) {
        set_error_handler('ignoreFileErrors');
        $result = file_exists($path);
        restore_error_handler();
        return $result;
    }

    public static function Expand($path='.') {
        if (empty($path))
            $path = '.';
        else {
            $root = self::getDocumentRoot();
            if (strpos($path, $root) === 0)
               return $path;
            // assume '/' at start refers to document root not machine root
            if (strpos($path,'/') === 0) {
                $path =  $root.$path;
            }
        }
        $result = realpath($path);
        if ($result === false)
            // throw new Exception("Path not found: ".$path);
            return $result;
        return str_replace("\\",'/',$result);
    }
}   // finish class TFilePath



<?php
/** Class: TSqlImporter ***************************************/
///  Reads and executes SQL backup files
/**
Assumes file in form created by SQL Yog or Backup/Restore module:
Insert and Drop statements must be on single line. Create statements
may span multiple lines.  Only these statements are executed.
*****************************************************************/
class TSqlImporter
{
    private $database;
    private $line = 0;
    private $fails = 0;
    public $log = array();

    public function __construct($dbId) {
        $this->database = TDatabase::GetDatabase($dbId);
    }

    public function __toString() {
        return 'TSqlScriptRunner';
    }

    private function execSql($s) {
        try {
            $this->database->ExecuteCommand($s);
            return 1;
        }
        catch(Exception $ex) {
            array_push($this->log, "Statement failed on line $this->line");;
            $this->fails++;
            return 0;
        }
    }

    public function Import($file) {
        TTracer::Trace("Import($file)");
        $this->log = array();
        array_push($this->log, date("D M j G:i:s T Y"));
        array_push($this->log, "Importing '$file'");
        $state = 0;
        $statement = '';
        $creates = 0;
        $inserts = 0;
        $drops = 0;
        $this->fails = 0;
        $this->line = 0;
        $handle = @fopen($file, "r");
        if (!$handle)
            throw new Exception("Cannot open file '$file'");

        while (!feof($handle)) {
           $this->line++;
           $s =  fgets($handle);
           switch ($state) {
                case 0 :
                    if ( strtoupper(substr($s,0,13)) == 'CREATE TABLE ') {
                        if (strrchr($s,';')) {
                            $creates += $this->execSql($s );
                        }
                        else {
                            $state = 1;
                            $statement = $s;
                        }
                    }
                    else if (strtoupper(substr($s,0,11)) == 'DROP TABLE ') {
                        $drops += $this->execSql($s);
                    }
                    else if (strtolower( substr($s,0,7) )== 'insert ') {
                        $inserts += $this->execSql($s );
                    }
                    break;
                case 1 :
                    $statement .= $s;
                    if (strrchr($s,';')) {
                        $creates += $this->execSql($statement);
                        $statement = '';
                        $state = 0;
                    }
                    break;
            }
        }
        fclose($handle);
        array_push($this->log, "Processed $this->line lines");
        array_push($this->log, "Created $creates tables.");
        array_push($this->log, "Dropped $drops tables.");
        array_push($this->log, "Inserted $inserts records.");
        array_push($this->log, "Failures: $this->fails");
        array_push($this->log, 'Completed: '.date("G:i:s T"));
    }
}
// end TSqlImporter

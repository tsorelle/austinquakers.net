<?php
/** Class: TFriendlyNotesManager ***************************************/
/// Mangage FN files
/**
*****************************************************************/
class TFriendlyNotesManager extends TDocManager
{
    public function __construct() {

    }

    public function __toString() {
        return 'TFriendlyNotesManager';
    }

    function getMonthName($mon) {
        switch ($mon)  {
            case 'Jan' : return "January";
            case 'Feb' : return "February";
            case 'Mar' : return 'March';
            case 'Apr' : return 'April';
            case 'May' : return 'May';
            case 'Jun' : return 'June';
            case 'Jul' : return 'July';
            case 'Aug' : return 'August';
            case 'Sep' : return 'September';
            case 'Oct' : return 'October';
            case 'Nov' : return 'November';
            case 'Dec' : return 'December';
            default: return 'error';
        }

    }

    private function getMonthNumber($mon) {
        switch ($mon)  {
            case 'Jan' : return '01';
            case 'Feb' : return '02';
            case 'Mar' : return '03';
            case 'Apr' : return '04';
            case 'May' : return '05';
            case 'Jun' : return '06';
            case 'Jul' : return '07';
            case 'Aug' : return '08';
            case 'Sep' : return '09';
            case 'Oct' : return '10';
            case 'Nov' : return '11';
            case 'Dec' : return '12';
            default: return '00';
        }
    }

    private function writeToLog($msg)
    {
       // print "$msg\n";
        watchdog('cron','FN Archive: '.$msg);
    }

    private function doArchiveFriendlyNotes() {
        TTracer::Trace('doArchiveFriendlyNotes');

        $filesAdded = 0;

        $files = $this->getFiles('../notes');

        if (!empty($files)) {
            $rootPath = $this->getDocumentRoot();
            $destPath = $this->getFilePath('docs/fn');
            $sourcePath = realpath($rootPath.'/../notes');
            if (empty($sourcePath) || (!file_exists($sourcePath))) {
                $cwd = getcwd();
                $rawPath = $rootPath.'/../notes';
                $this->writeToLog("Source path not found at $sourcePath.");
                $this->writeToLog("Doc root: $rootPath.");
                $this->writeToLog("Raw source path: $rawPath.");
                $this->writeToLog("CWD: $cwd.");
                return;
            }

            if (empty($destPath) || (!file_exists($destPath))) {
                $this->writeToLog("Dest path not found at $destPath");
                return;
            }

            foreach ($files as $file) {
                if ($file->filetype == 'pdf' && strlen($file->name) == 11 && substr($file->name, 0, 2) == 'FN') {
                    $month = substr($file->name, 2, 3);
                    $year = '20' . substr($file->name, 5, 2);
                    $monthNumber = $this->getMonthNumber($month);
                    $publicationDate = "$year-$monthNumber-01";
                    TTracer::Trace("Publication date: $publicationDate");
                    $monthName = $this->getMonthName($month);
                    $monthNumber = $this->getMonthNumber($month);
                    $title = "Friendly Notes #$year$monthNumber - $monthName $year";
                    $description = "Meeting newsletter, $monthName $year";
                    $sourceFilePath = $sourcePath . '/' . $file->name;
                    $targetFilePath = $destPath . '/' . $file->name;
                    // TTracer::Trace("Processing $year-$month: " . date("Y-m-d", $publicationDate));
                    // watchdog('cron', "FN: Processing $year-$month: " . date("Y-m-d", $publicationDate));
                    // watchdog('cron', "FN: Processing $year-$month: $publicationDate");
                    TTracer::Trace("Title = $title");
                    TTracer::Trace("Description = $description");
                    TTracer::Trace("source=$sourceFilePath; dest=$targetFilePath");
                    if (!file_exists($targetFilePath)) {
                        copy($sourceFilePath, $targetFilePath);
                    }
                    if ($this->addDocument('docs/fn', $file, 'fmanotes', null, $title, $description, $publicationDate)) {
                        TTracer::Trace('document added');
                        $this->writeToLog("Archived document $file->name");
                        // watchdog('cron', "FN Archive: Archived document $file->name"); // , $publicationDate");
                        $filesAdded++;
                    } else {
                        $this->writeToLog("Document skipped $file->name");
                        // watchdog('cron', "FN Archive: Document skipped $file->name"); // , $publicationDate");
                    }
                }
            }
        }
        if  ($filesAdded > 0) {
            watchdog('cron', "Archived $filesAdded Friendly Notes issues. From: $sourcePath; To: $destPath");
        }
        else {
            watchdog('cron', "No Friendly Notes to archive in $sourcePath"); // ; destination: $destPath");
        }
        //watchdog($type, $message, $variables = array(), $severity = WATCHDOG_NOTICE, $link = NULL)
    }

    private static $instance;

    private static function getInstance() {
        if (!isset(self::$instance))
            self::$instance = new TFriendlyNotesManager();
        return self::$instance;
    }

    public static function ShowArchivingMessages() {
        $instance = self::getInstance();
        return $instance->showMessages();

    }

    public static function ArchiveFriendlyNotes() {
        TTracer::Trace('ArchiveFriendlyNotes');
        $instance = self::getInstance();
        return $instance->doArchiveFriendlyNotes();
    }


    /**
     * @param $publicationDate
     * 
     * $publicationDate must be a vailid date string
     */
    public static function GetIssueInformation($publicationDate) {
        $result = new stdClass();
        $result->errorMessage = '';
        $pubdate = strtotime($publicationDate);
        if ($pubdate === FALSE || $pubdate === -1) {
            return FALSE;
        }
        $result->filename = 'FN'.date('My',$pubdate).'.pdf';
        $result->uploadDir = TFilePath::Expand('../notes');
        $result->filePath = $result->uploadDir.'/'.$result->filename;
        $result->isUploaded = is_file($result->filePath);
        $result->publicationDate = $pubdate;
        $result->issueDate = date('F Y',$pubdate);
        $result->messageText =  "The $result->issueDate issue of Friendly Notes is ready.  Click the link below to read the on-line PDF file:\n".
        "\nhttp://www.austinquakers.org/notes/$result->filename\n\n".
        "Adobe Acrobat Reader is required to read a PDF file. If you don't have it, get it here: https://get.adobe.com/reader\n";
        return $result;
    }
}

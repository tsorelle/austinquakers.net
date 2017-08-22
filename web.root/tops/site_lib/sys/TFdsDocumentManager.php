<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/28/14
 * Time: 5:21 PM


 Updated 8/28/14 8:28

 */

class TFdsDocumentManager extends TDocManager
{


    public function __construct() {

    }

    public function __toString() {
        return 'TFdsDocumentManager';
    }

    public function IndexDocuments() {
        TTracer::Trace('doIndexDocuments');
        $files = $this->getFiles('sites/default/files/docs/fds');

        // TTracer::ShowArray($files);

        $filesAdded = 0;

        foreach($files as $file) {
            $publicationDate =  date('Y-m-d', $file->filedate);
            $title = $file->name;
            $description = 'First day school document: '.$title;

//            TTracer::Trace("document to process: $file->name, date $publicationDate, description $description ");


            if ($this->addDocument('docs/fds',$file,'teaching',null,$title,$description,$publicationDate)) {
                TTracer::Trace('document added');
                watchdog('cron', "Archived document $file->name");
                $filesAdded++;
            }
            else
                TTracer::Trace("document skipped: $file->name");

        }
        watchdog('cron', "Archived $filesAdded First Day School Files.");

    }


} 
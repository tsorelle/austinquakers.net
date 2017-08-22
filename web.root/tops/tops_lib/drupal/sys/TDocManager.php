<?php
/** Class: TDocManager ***************************************/
/// Manages document uploading and indexing
/**
*****************************************************************/
class TDocManager
{

    private $importLog;

    protected function logMessage($message)
    {
        if ($this->importLog)
            array_push($this->importLog, $message);
    }

    public function showMessages()
    {
        if ($$this->importLog) {
            echo '<h3>Results</h3><h4>Messages:</h4><p>';
            foreach ($this->importLog as $message)
                echo $message . '<br/>';
            echo '</p>';
        }
    }

    public function __construct()
    {
        $this->importLog = array();
    }

    public function __toString()
    {
        return 'TDocManager';
    }

    private function getMimeType($ext)
    {
        switch ($ext) {
            case 'pdf' :
                return 'application/pdf';
            case 'html' :
                return 'text/html';
            case 'doc' :
                return 'application/msword';
            case 'docx' :
                return 'application/msword';
            case 'txt' :
                return 'text/plain';
            case 'htm' :
                return 'text/html';
            case 'zip' :
                return 'application/zip';
            case 'jpeg' :
                return 'image/jpeg';
            case 'jpg' :
                return 'image/jpeg';
            case 'gif' :
                return 'image/gif';
            case 'bmp' :
                return 'image/bmp';

            case 'csv' :
                return 'text/comma-separated-values';
            case 'xls' :
                return 'application/vnd.ms-excel';
            case 'xlsx' :
                return 'application/vnd.ms-excel';
            case 'ppt' :
                return 'application/vnd.ms-powerpoint';
            case 'pptx' :
                return ' application/vnd.ms-powerpoint';
            case 'tsv' :
                return 'text/tab-separated-values';

            case 'gz' :
                return 'application/x-gzip';
            case 'mid' :
                return 'audio/mid';
            case 'mov' :
                return 'video/quicktime';
            case 'movie' :
                return 'video/x-sgi-movie';
            case 'mp2' :
                return 'video/mpeg';
            case 'mp3' :
                return 'audio/mpeg';
            case 'mpa' :
                return 'video/mpeg';
            case 'mpe' :
                return 'video/mpeg';
            case 'mpeg' :
                return 'video/mpeg';
            case 'mpg' :
                return 'video/mpeg';

            case 'pub' :
                return 'application/x-mspublisher';
            case 'tif' :
                return 'image/tiff';
            case 'tiff' :
                return 'image/tiff';

            case 'ra' :
                return 'audio/x-pn-realaudio';
            case 'ram' :
                return 'audio/x-pn-realaudio';

            case 'wav' :
                return 'audio/x-wav';
            case 'wks' :
                return 'application/vnd.ms-works';
            case 'wps' :
                return 'application/vnd.ms-works';
            case 'wri' :
                return 'application/x-mswrite';

            default:
                return false;   // type not supported
        }


    }

    protected function importDocument($relativePath, $filename, $category, $title, $description, $pubdate = '')
    {
        $docPath = $this->getFilePath($relativePath);
        $fileInfo = $this->getFile($docPath, $filename);
        if (!$fileInfo)
            return false;
        $this->addDocument($relativePath, $fileInfo, $category, $title, $description, $pubdate);
    }

    private function getDocumentPath($relativePath)
    {
        $result = file_directory_path();
        if ($relativePath)
            return $result . '/' . $relativePath;
        return $result;
    }

    public function addDocuments($sourcePath, $relativePath, $defaultCategory = 'other', $defaultTaxonomy = '')
    {
        TTracer::Trace("Sourcepath in: $sourcePath");

        $root = $this->getDocumentRoot();
        $sourcePath = realpath($root . '/../' . $sourcePath);
        TTracer::Trace("Source path: $sourcePath");
        $destPath = $this->getFilePath($relativePath);
        $docs = $this->getDocumentList($sourcePath); //$this->getFiles($relativePath);
        if (count($docs) == 0) {
            TTracer::Trace('No upload list');
            $this->logMessage("No upload list at $sourcePath");
        } else {
            $this->logMessage("Upload list found at $sourcePath");
        }
        foreach ($docs as $doc) {
            $count = count($doc);

            $fileName = $doc[0];
            $title = ($count > 1) ? $doc[1] : $fileName;
            $category = ($count > 2) ? $doc[2] : $defaultCategory;
            $taxonomy = ($count > 3) ? $doc[3] : $defaultTaxonomy;
            $description = ($count > 4) ? $doc[4] : $title;
            if (!file_exists($destPath . '/' . $fileName))
                rename($sourcePath . '/' . $fileName, $destPath . '/' . $fileName);
            $fileInfo = $this->getFile($destPath, $fileName);
            if ($fileInfo) {
                $this->logMessage("Adding $fileName to $relativePath");
                $result = $this->addDocument($relativePath, $fileInfo, $category, $taxonomy, $title, $description);
            } else {
                TTracer::Assert($fileInfo === false, "File found at $sourcePath");
                $this->logMessage("$fileName not found in doc directory.");
            }
        }
    }

    protected function addDocument($relativePath, $fileInfo, $category, $termName, $title, $description, $pubdate = '')
    {
        $documentPath = $this->getDocumentPath($relativePath);
        if (!fileInfo) {
            $this->logMessage("File not found in $relativePath");
        }
        if (empty($pubdate))
            $pubdate = date('Y-m-d', $fileInfo->filedate);
        // TTracer::Trace("pubdate= $pubdate");
        $fid = $this->getFileId($documentPath, $fileInfo);
        if (empty($fid)) {
            $fid = $this->addFileRecord($documentPath, $fileInfo);
        } else if ($this->documentUploadRecordExists($fid)) {
            $this->logMessage("File $fileInfo->name already indexed.");
            return false;
        }

        $node = $this->addDocNode($title, $description);
        $nid = $node->nid;
        $vid = $node->vid;
        TTracer::Trace("Node created: $nid");
        $this->updateDocumentUploadRecord($nid, $fid, $category, $pubdate);

        if ($termName) {
            $terms = taxonomy_get_term_by_name($termName);
            if ($terms)
                taxonomy_node_save($node, $terms);
        }
        return true;
    }

    private function getFileId($documentPath, $fileInfo)
    {
        $filePath = $documentPath . '/' . $fileInfo->name;
        $fid = db_result(db_query("SELECT fid FROM {files} WHERE filepath = '%s'", $filePath));
        return $fid;
    }

    private function addFileRecord($documentPath, $fileInfo)
    {
        // needs path from sites down.
        $filePath = $documentPath . '/' . $fileInfo->name;
        TTracer::Trace("adding $filePath");

        $fileRecord = new stdclass();
        $fileRecord->uid = 1;
        $fileRecord->filename = $fileInfo->name;
        $fileRecord->filepath = $filePath;
        $fileRecord->filemime = 'application/pdf';
        $fileRecord->filesize = $fileInfo->size;
        $fileRecord->status = 1;
        $fileRecord->timestamp = $fileInfo->filedate;
        $writeResult = drupal_write_record('files', $fileRecord);
        TTracer::Assert($writeResult !== false, "Added $fileRecord->fid", "Failed to add file record for $fileInfo->name");
        TTracer::Trace("Result = $writeResult <br>");
        $fid = $fileRecord->fid;
        return $fid;
    }

    function addDocNode($title, $body)
    {
        $node = new stdClass();
        $node->title = $title;
        $node->created = time();
        $node->status = 1; //published
        $node->promote = 0;
        $node->sticky = 0;
        $node->body = $body;
        $node->type = 'document_upload';
        $node->language = 'en';

        node_save($node);
        return $node;
    }

    private function documentUploadRecordExists($fid)
    {
        $count = db_result(
            db_query("SELECT count(*) FROM content_type_document_upload WHERE field_document_file_fid = %d", $fid));
        return ($count > 0);
    }

    public function updateDocumentUploadRecord($nid, $fid, $category, $pubDate = null, $listed = true)
    {
        TTracer::Trace("updateDocumentUploadRecord($nid)");
        $count = db_result(
            db_query("SELECT count(*) FROM {content_type_document_upload} WHERE nid=%d", $nid));
        TTracer::Trace("Count=$count");
        if ($count > 0) {
            TTracer::Trace("Adding document record for $nid");
            $data = 'a:1:{s:11:"description";s:0:"";}';

            $sql = "UPDATE content_type_document_upload " .
                "set field_document_category_value = '$category'," .
                "field_document_file_fid = $fid," .
                "field_publication_date_value = '" . $pubDate . "T00:00:00'," .
                "field_document_file_list = 1," .
                "field_document_islisted_value = 'yes'," .
                "field_document_file_data = '$data'" .
                " where nid=$nid";

            TSqlStatement::ExecuteNonQueryToDrupal($sql);
        } else {
            TTracer::Trace("Document record for $nid exists");
            $this->logMessage("Document record for $nid exists");
        }
        return ($count > 0);
    }

    public function getFile($path, $filename)
    {
        $item = null;
        $filePath = $path . '/' . $filename;
        if (!file_exists($filePath)) {
            $this->addMessage("File not found at $filePath");
            return false;
        }
        $type = filetype($filePath);
        if ($type == 'file') {
            $item = new stdclass();
            $item->name = $filename;
            $item->size = filesize($filePath);
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $item->filetype = $ext;
            $item->filedate = filemtime($filePath);
            $item->mime = $this->getMimeType($ext);
        }
        return $item;

    }

    public function getDocumentRoot() {
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
    
    protected function getFilePath($relativePath)
    {
        $root = $this->getDocumentRoot();
        $result = $root . '/' .  file_directory_path();
        if ($relativePath) {
            return $result . '/' . $relativePath;
        }
        return $result;
    }

    public function getDocumentList($sourcePath)
    {
        TTracer::Trace("Path: $sourcePath" . "/uploads.csv");
        $result = array();
        if (($handle = fopen($sourcePath . "/uploads.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                array_push($result, $data);
            }
            fclose($handle);
        }
        return $result;
    }

    public function getFiles($relativePath)
    {
        // $path = $this->getFilePath($relativePath);
        $rawPath = $this->getDocumentRoot() . '/' . $relativePath;
        $path = realpath($rawPath);
        // TTracer::Trace("Path = $path");
        $dir = @ dir($path);
        if (!$dir) {
            watchdog('cron', "TDocManager::getFiles(): Path not found: $rawPath");
            return false;
        }
        $result = array();
        while (($filename = $dir->read()) !== false) {
            $item = $this->getFile($path, $filename);
            if ($item) {
                array_push($result, $item);
            }
        }

        $dir->close();
        return $result;
    }
}
// end TDocManager
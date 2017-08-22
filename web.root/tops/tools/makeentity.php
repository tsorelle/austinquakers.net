<?php
$enabled = true;

$_SERVER['DOCUMENT_ROOT'] = realpath('.');
$docRoot = $_SERVER['DOCUMENT_ROOT'];
require($docRoot.'/tops/startup.php');

require_once("tops_lib/db/TDBTableInfo.php");

function getStampFieldType($fname, $defaultType) {
    $fname = strtolower($fname);
    if ($fname == 'dateadded' || $fname == 'createdon' || $fname=='addedon')
        return 'CREATEDATE_FIELD';
    if ($fname == 'dateupdated' || $fname == 'updatedon' || $fname == 'changedon')
        return 'DATESTAMP_FIELD';
    if ($fname == 'addedby' || $fname == 'createdby')
        return 'CREATEUSER_FIELD';
    if ($fname == 'updatedby' || $fname == 'changedby' )
        return 'USERSTAMP_FIELD';
    return $defaultType;

}

function writeClassFile(
        $entityTableName,
        $entityClassName,
        $idFieldName,
        $overwrite = true) {

$SITE_LIB = $_SERVER['DOCUMENT_ROOT'].'/tops/site_lib';

// $entityOutputPath = $SITE_LIB.'/model';
$entityOutputPath = __DIR__.'/model';
echo "Writing $entityClassName to $entityOutputPath<br>";
    $info = new TDBTableInfo();

    if (!$info->setTable($entityTableName))
        return false;

    $columns = $info->getfieldCount();
    $idFieldName = $info->getFieldName(0);

    if ((!$overwrite) && file_exists("$entityOutputPath/$entityClassName.php"))
        exit('File exists. Set $overwrite=true');
    $fp = @fopen("$entityOutputPath/$entityClassName.php","w");
    if ($fp === false) {
        trigger_error('Cannot open output file '."$entityOutputPath/$entityClassName.php",E_USER_WARNING);
        return false;
    }
    fwrite($fp, '<?php'."\n");


    fwrite($fp, 'require_once("tops_lib/model/TEntityObject.php");'."\n");
    fwrite($fp, "\n");
    fwrite($fp, "class $entityClassName extends TEntityObject { \n");
    fwrite($fp, "    public function  __construct()\n");
    fwrite($fp, "    {\n");

    fwrite($fp, '        $this->tableName = '."'$entityTableName';\n");
    fwrite($fp, '        $this->idFieldName = '."'$idFieldName';\n");


    for ($i = 0; $i < $columns; $i++) {
        $fieldName = $info->getFieldName($i);
        $fieldType = $info->getFieldType($i);

//echo "Field: $fieldName; Type: $fieldType<br>";
            if ($fieldType == 'int')
                $fieldType = 'INT_FIELD';
             else if ($fieldType == 'string')
                $fieldType = getStampFieldType($fieldName,'STRING_FIELD');
             else if ($fieldType == 'real')
                $fieldType = 'DOUBLE_FIELD';
             else if ($fieldType == 'date')
                $fieldType = getStampFieldType($fieldName,'DATE_FIELD');
             else if ($fieldType == 'datetime')
                $fieldType = getStampFieldType($fieldName,'DATETIME_FIELD');
             else
                $fieldType = 'STRING_FIELD';
       // echo('        $this->addField('."'".$info->getFieldName($i)."',$fieldType);<br>");

        fwrite($fp, '        $this->addField('."'".$info->getFieldName($i)."',$fieldType);\n");
    }

    fwrite($fp, '    }  //  '."$entityClassName\n");
    fwrite($fp, "\n");

    for ($i = 1; $i < $columns; $i++) {
        $fieldName = $info->getFieldName($i);

        fwrite($fp, "    function get".ucfirst($fieldName)."() {\n");
        fwrite($fp, "        return ".'$'."this->get('$fieldName');\n");
        fwrite($fp, '    }'."\n");

        if (!(strtolower($fieldName) == 'dateadded' || strtolower($fieldName) == 'dateupdated' ||
             strtolower($fieldName) == 'addedby' || strtolower($fieldName) == 'updatedby')) {

            fwrite($fp, '    function set'.ucfirst($fieldName).'($value) {'."\n");
            fwrite($fp, '        $this->setFieldValue('."'$fieldName'".',$value);'."\n");
            fwrite($fp, '    }'."\n");
        }
        fwrite($fp, "\n");
    }


    fwrite($fp, '} // end class'."\n\n");
    fclose($fp);
  }

//  if (!$enabled)
//    exit("Code generation disabled.");
//    writeClassFile('mailboxes','TMailbox','mailboxId',true);
//    writeClassFile('memberships','TMembership','membershipId',true);
//    writeClassFile('committeemembers','TCommitteeMember','committeeMemberId');
// writeClassFile('wmschedule','TWmSchedule','wmscheduleid');
writeClassFile('fdsclasses','TFdsClass','id');
    echo '<br>Done.<br>';





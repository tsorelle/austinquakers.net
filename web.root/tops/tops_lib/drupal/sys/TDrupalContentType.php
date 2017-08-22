<?php
/*****************************************************************
Class:  TDrupalContentType
Description:
*****************************************************************/
abstract class TDrupalContentType
{
    protected   $typeId;
    protected   $entityCode;
    private     $teaserImage;
    private     $bodyImage;
    private     $webSite;
    private     $showSubmittedTeaser;
    private     $showSubmittedBody;
    private     $showLinksTeaser;
    private     $showLinksBody;
    protected   $node;

    public function getTypeId() {
        return $this->typeId;
    }

    public function setTypeId($value) {
        $this->typeId = $value;
    }

    public function setNode($node) {
        $this->node = $node;
    }

    public function getNode() {
        return $this->node;
    }

    public function __construct() {
    }

    public function __toString() {
        return "Tops drupal content type '$this->typeId'";
    }

    public static function Create($typeId) {
        $result = TClassFactory::MakeObject('T'.ucfirst($typeId).'Node');
        if (!$result) {
            throw new Exception("No content type found for ID '$typeId'");
        }
        $result->setTypeId($typeId);
        return $result;
    }

    public static function CreateForNode($node) {
        if (empty($node) || empty($node->tops_content_type))
            return null;
        $result = TDrupalContentType::Create($node->tops_content_type);
        $result->setNode($node);
    }
}
// end TDrupalContentType




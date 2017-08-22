<?php

/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/14/2015
 * Time: 8:13 PM
 */
class TTaxonomy
{
    private $taxonomyData;

    public function __construct($data)
    {
        $this->taxonomyData = $data;
    }

    public function getTerm($vocabularyName, $termName)
    {
        foreach ($this->taxonomyData as $taxonomyTerm) {
            if ($taxonomyTerm->name == $termName && $taxonomyTerm->vocabulary == $vocabularyName) {
                return $taxonomyTerm;
            }
        }
        return null;
    }
    public function getVocabularies() {
        $result = array();
        foreach ($this->taxonomyData as $taxonomyTerm) {
            $array[$taxonomyTerm->vocabulary] = 1;
        }
        return array_keys($result);
    }

    public function vocabularyAsString($vocabularyName) {
        $result = '';
        foreach ($this->taxonomyData as $taxonomyTerm) {
            if ($taxonomyTerm->vocabulary = $vocabularyName) {
                if ($result) {
                    $result .= ', ';
                }
                $result .= $taxonomyTerm->name;
            }
        }
        return $result;
    }
}
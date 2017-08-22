<?php
/** Class: TContentFormatter ***************************************/
/// Use to format content in a pre-process node routine
/**
*****************************************************************/
class TContentFormatter
{
    private $vocabularies;
    public function __construct() {

    }

    public function __toString() {
        return 'TContentFormatter';
    }

    private function getVocabularies() {
        if (!isset($this->vocabularies)) {
            $vocabularies = taxonomy_get_vocabularies();
            $this->vocabularies = array();
            foreach ($vocabularies as $vocabulary) {
                $this->vocabularies[$vocabulary->vid] = $vocabulary->name;
            }
        }
        return $this->vocabularies;
    }

    private function getVocabularyTree($vocabularyName) {
        $sql = 'select v.vid from vocabulary v '.
                "where v.name = '$vocabularyName'";
        $vid = db_result(db_query($sql));
        TTracer::Trace("Vocab id = $vid");
        TTracer::Trace("Vocab sql = $sql");

        return taxonomy_get_tree($vid);
    }


    function doFormatTaxonomy($taxonomy) {
        if ($taxonomy) {
            $vocabularies = $this->getVocabularies();
            // TTracer::ShowArray($vocabularies);
            $keys = array_keys($taxonomy);
            $results = array();
            foreach ($keys as $key) {
                $termId= str_replace('taxonomy_term_','',$key);
                if (is_numeric($termId)) {
                    $term = taxonomy_get_term($termId);
                    // krumo($term);
                    $vid = $term->vid;
                    $vocabularyName = $vocabularies[$vid];
                    $results[$vid]['heading'] = $vocabularyName;
                    $results[$vid]['titles'][$termId] = $term->name;

                }
            }
            foreach($results as $item) {
                $heading = $item['heading'];
                    TTracer::Trace("Heading = $heading");
                $result .= '<p><strong>'.$heading.'</strong>:&nbsp;';
                $isFirst = true;
                foreach ($item['titles'] as $termTitle) {
                    $result .= $isFirst ?  $termTitle : ', '.$termTitle;
                    $isFirst = false;
                    TTracer::Trace("TermTitle = $termTitle");
                }

                $result .= '<p>';
            }

            return $result;
        }
        return '';
    }

    /*
    private function extractMenuItem($menu) {
        $result = array();
        $i = 0;
        foreach($menu as $item) {
            $result[$i]
        }

    }
    public function extractSubmenu($menuName, $subMenuName = null) {
        TTracer::Trace('extractSubmenu');

        $tree =  menu_tree_all_data($menuName);
        krumo($tree);
        // TTracer::ShowArray($tree);

        if (empty($subMenuName) || $subMenuName == $menuName)
            return $tree;


        return $tree;

    }


    private function doFormatMenuAsList($menuName, $subMenuName) {
        TTracer::Trace("doFormatMenuAsList($menuName,$subMenuName)");

        $result = '';
        $items = $this->extractSubMenu($menuName, $submenuName);

        return $result;

    }
    */

   public function getVocabularyInfo($vocabularyName) {
       TTracer::Trace("getVocabularyInfo($vocabularyName)");
        $sql = 'select v.vid, v.name, v.description '.
                'from vocabulary v '.
                "where v.name = '%s'";
        $queryResult = db_query($sql,$vocabularyName);
        while ($data = db_fetch_object($queryResult)) {
            return $data;
        }
        return null;

   }

   public function getVocabularyTermsInfo($vid) {
        $sql = 'select t.tid, t.name, t.description '.
                'from term_data t '.
                "where vid = %d ";
        $queryResult = db_query($sql, $vid);
        $result = array();
        $i = 0;
        while ($data = db_fetch_object($queryResult)) {
            $result[$i++] = $data;
        }

        return $result;

   }


    public function doFormatVocabularyList($vocabularyName,$url = null) {
        $vocabulary = $this->getVocabularyInfo($vocabularyName);
        // krumo($vocabulary);
        TTracer::Trace("doFormatVocabularyList($vocabularyName)");
        $terms = $this->getVocabularyTermsInfo(($vocabulary->vid));
        // krumo($terms);

        $result = TDiv::Create('vocabulary-term-list');
        // $result->add (THtml::Header(2,$vocabulary->name));
        $list = new TBulletList('pageMenu');
        foreach($terms as $term) {
           // TTracer::Trace("Adding term ".$term->name);
            $line = '<li>';
            $line .= $url ?
                '<a href="'.$url.'/'.$term->name.'">'.$term->name.'</a>'
                : "<strong>$term->name</strong>";
            if ($term->description)
                $line .= "<p>$term->description</p>";
            $line .= '</li>';
            $list->add($line);
        }

        $result->add($list);

        return $result;


    }



    private static $instance;

    private static function getInstance() {
        if (!isset(self::$instance))
            self::$instance = new TContentFormatter();
        return self::$instance;
    }

    public static function FormatTaxonomy($taxonomy) {
        $instance = self::getInstance();
        return $instance->doFormatTaxonomy($taxonomy);
    }

    /*
    public static function FormatMenuAsList($menuName, $subMenuName = null) {
        $instance = self::getInstance();
        return $instance->doFormatMenuAsList($menuName, $subMenuName);
    }
    */

    public static function FormatVocabularyList($vocabularyName,$url) {
        $instance = self::getInstance();
        return $instance->doFormatVocabularyList($vocabularyName,$url);
    }

    public static function GetTeaser($body) {
        TTracer::Trace('GetTeaser');
            $p = strpos($body,'<!--break-->');
        if ($p)
            return substr($body,0,$p-1);
        return $body;
    }



}
// end TContentFormatter
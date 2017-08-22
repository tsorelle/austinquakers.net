<?php
/*****************************************************************
Class:  TDrupalSnippet
Description:
*****************************************************************/
class TDrupalSnippet
{
    private $values = array();
    private $text = '';

    public function __construct($text) {
        $this->text = $text;
    }

    public function setValue($name,$value) {
        $this->values[$name] = $value;
    }

    public function render() {
// TTracer::Trace("render snippet:"); // $this->text");
        $result = $this->text;
        foreach($this->values as $name => $value) {
//            TTracer::Trace("adding value: $name = $value");
            $token = sprintf('[value:%s]',$name);
            $result = str_replace($token,$value,$result);
        }
        return $result;
    }

    public function __toString() {
        return $this->render();
    }


    public static function Get($title, $default='') {
//        TTracer::Trace('get snippet: '.$title);
        $sql = "select r.body from node n join node_revisions r on n.nid = r.nid where type = 'tops_snippet' and r.title='$title' limit 1";
        $result = db_fetch_object(db_query($sql));
//        TTracer::ShowArray($result);
//        TTracer::Assert(isset($result->body),'has body.');
        return new TDrupalSnippet(isset($result->body) ? $result->body : $default);
    }



}
// end TDrupalSnippet




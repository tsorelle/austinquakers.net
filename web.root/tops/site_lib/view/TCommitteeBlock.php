<?
/** Class: TCommitteeBlock ***************************************/
/// controller for committee list block
/**
*****************************************************************/
class TCommitteeBlock
{
    public function __construct() {
        $this->nodeId = TDrupalPage::GetPageNodeId();
    }

    public function __toString() {
        return 'TCommitteeBlock';
    }

   private static $_instance;
   private static function GetInstance() {
       if (!isset(self::$_instance))
            self::$_instance = new TCommitteeBlock();
       return self::$_instance;
   }

   private $committeeId;
   private $nodeId;
   private $committee;

   private function getCommitteeId() {
       if (!isset($this->committeeId)) {
            $sql = 'select field_committee_id_value from content_type_committee_post where nid = %d';
            $this->committeeId = db_result(db_query($sql,$this->nodeId));
        }
        return $this->committeeId;
    }

    private function getCommittee() {
        if (!isset($this->committee)) {
            $committeeId = $this->getCommitteeId();
            $this->committee = TCommittee::GetCommittee($committeeId);

        }
        return $this->committee;
    }


    private function setHomePageLink() {
        $committeeId = $this->getCommitteeId();
        $result = array();
        $sql =
        'select cp.nid '.
        'from content_type_committee_post cp '.
        'where field_committee_id_value = %d and cp.field_committee_homepage_value = 1 ';
        $nid = db_result( db_query($sql,$committeeId));
        if (!$nid)
            return '';

        $committee = $this->getCommittee();
        $name = $committee->getName();
        $breadcrumb = array();
        $breadcrumb[] = l('Home', '<front>');
        $breadcrumb[] = l($name.' Home', 'node/'.$nid);
        drupal_set_breadcrumb($breadcrumb);


        // return '<a href="/?q=node/'.$nid.'">'.$name.' home</a>';

    }


    private function getPagesForCommittee() {
        $committeeId = $this->getCommitteeId();
        $result = array();
        $sql =
        'select cp.nid, n.title, cp.field_committee_homepage_value as IsHomePage '.
        'from content_type_committee_post cp '.
        'join node n on cp.nid = n.nid '.
        'where field_committee_id_value = %d and cp.field_list_order_value > 0 '.
        'order by field_committee_homepage_value desc, field_list_order_value, n.title';
        $nodes = db_query($sql,$committeeId);
        while($data = db_fetch_object($nodes)) {
            array_push($result,$data);
        }
        return $result;
    }

    private function getCommitteeLinksCount() {
        $committeeId = $this->getCommitteeId();
        $sql = 'select count(*) from content_type_committee_post cp '.
                'where field_committee_homepage_value = 0 and field_committee_id_value = %d';
        return db_result( db_query($sql,$committeeId));
    }



    private function getCommitteePageList() {
        $result = array();
        $sql = 'select nid, field_committee_id_value from content_type_committee_post cp '.
                'where field_committee_homepage_value = 1';
        $nodes = db_query($sql);
        while($data = db_fetch_object($nodes)) {
            $committeeId = $data->field_committee_id_value;
            $committee = TCommittee::GetCommittee($committeeId);
            if (!empty($committee)) {
                $c = new stdClass();
                $c->nid = $data->nid;
                $c->committeeId = $committeeId;
                $c->committeeName = $committee->getName();
                $result[$c->committeeName] = $c;
                // array_push($result,$c);
            }
        }
        ksort($result);
        return  array_values($result);

    }

    private function buildDocumentsBlock() {
        $committee = $this->getCommittee();
        if (empty($committee))
            return null;

        $committeeName = $committee->getName();

        $href = '/committees/docs/'.$committeeName;

        $menu = new THtmlMenu('committee-documents-menu','menu');
        $menu->setSelectedClass('active');
        $menu->addItem('Documents list',$href,'Documents for '.$committeeName,'',false,'leaf');


        if (TUser::Authorized('create document_upload content'))
            $menu->addItem('New document','/node/add/document-upload','Upload new document.','',false,'leaf');
        return TCollapsible::Create('committee-documents-block','Documents',false,$menu);

    }
    private function buildMemberList(){
        $committeeId = $this->getCommitteeId();
        $list = TCommitteeMember::GetCurrentMemberList($committeeId);
        $menu = new THtmlMenu('committee_links','menu');
        $menu->setSelectedClass('active');
        $mailtoList = '';
        $mailCount = 0;
        foreach($list as $item) {
            $href = '/directory?cmd=showPerson&pid='.$item->pid;
            $text = $item->name;
            if ($item->roleId != 1)
                $text .= ' ('.$item->roleName.')';
            $menu->addItem($text,$href,'Member: '.$item->name,'',false,'leaf');
            if (!empty($item->email)) {
                if ($mailCount > 0) {
                    $mailtoList .= ';';
                }
                $mailCount++;
                $mailtoList .= $item->name.'<'.$item->email.'>';
            }

        }

        if (TUser::Authenticated() && $mailtoList) {
            $menu->addItem('[Send email message]',"mailto:$mailtoList",'','',false,'leaf');
        }

        if (TUser::Authorized('update fma committee directory'))
            $menu->addItem('[Update committee]','/committees?cid='.$committeeId,'','',false,'leaf');

        $result = TCollapsible::Create('committee-members-blockblock','Members',true,$menu);
//        $result->addText('Not Implemented yet');
        return $result;
    }

    private function buildPageList() {
        $list = $this->getPagesForCommittee();
        $listCount = count($list);
        $addCreateLink = TUser::Authorized('create committee_post content');
        if ($addCreateLink)
            $listCount++;

        if ($listCount == 0)
            return '';

        $menu = new THtmlMenu('committee_links','menu');
        $menu->setSelectedClass('active');
        foreach($list as $item) {
            $href = '/?q=node/'.$item->nid;
            $title = $item->IsHomePage ? 'Home' : $item->title;
                            //$text,$href,$hint='',$subtext='',$selected=true, $liClass=''
            $menu->addItem($title,$href,$item->title,'',($item->nid == $this->nodeId),'leaf');
        }

        if ($addCreateLink) {
            $menu->addItem('New page...','?q=/node/add/committee-post','Create new committee page','',false,'leaf');
        }
        return TCollapsible::Create('committee_links_block','Pages',
            ($listCount > 6),$menu);

    }

    private function buildCommitteeLinks() {
          $result = TDiv::Create('committee-block-content','clear-block');
          $result->add($this->buildPageList());
          $result->add($this->buildMemberList());
          $result->add($this->buildDocumentsBlock());


          return $result;
    }

    private function buildCommitteeMenu() {
        // $div = new TDiv('committee_menu_block');
        $list = $this->GetCommitteePageList();
        $menu = new THtmlMenu('committee_menu','menu');
        $menu->setSelectedClass('active');
        foreach($list as $item) {
            $href = '/?q=node/'.$item->nid;
            $menu->addItem($item->committeeName,$href,$item->committeeName,'',($item->nid == $this->nodeId),'leaf');
        }
        // $div->add($menu);

        // return $div;
        return TCollapsible::Create('committee_menu_block','Committee Pages...',true,$menu);
    }

    private function getDescription() {
        $committee = $this->getCommittee();
        if (empty($committee))
            return '';

        $result = new stdclass();
        $result->id = $committee->getId();
        $result->description = $committee->getDescription();
        $result->notes = $committee->getNotes();

        if (TUser::Authorized('update fma committee directory')) {
            $result->updateLink = 'http://www.austinquakers.net/committees?cid='.$result->id;
        }

        return $result;
    }

    private function committeeHasSubmenu() {
        $committee = $this->getCommittee();
        if ($committee->name == 'First Day School') {
            return true;
        }
        return false;
    }


    public static function GetCommitteeDescription() {
        $instance = self::GetInstance();
        return $instance->getDescription();

    }

    public static function GetCommitteeLinks() {
        $instance = self::GetInstance();
        return $instance->buildCommitteeLinks();
    }

    public static function GetCommitteeMenu() {
        $instance = self::GetInstance();
        return $instance->buildCommitteeMenu();

    }

    public static function SetCommitteeHomePageLink() {
        $instance = self::GetInstance();
        return $instance->setHomePageLink();

    }

    public static function PageIsForCommittee($committeeName) {
        if (TDrupalPage::GetPageNodeType() == 'committee_post') {
            $instance = self::GetInstance();
            $committee = $instance->getCommittee();
            if ($committee)
              return $committee->getName() == $committeeName;
        }
        return false;
    }

    public static function GetPageCommittee() {
        $instance = self::GetInstance();
        return $instance->getCommittee();
    }

    public static function GetPageCommitteeName() {
        $instance = self::GetInstance();
        $committee = $instance->getCommittee();
        if ($committee)
            return $committee->getName();
        return '';
    }


    public static function ShowCommitteePageLinks() {
        return TDrupalPage::GetPageNodeType() == 'committee_post';
        /*
       if (TDrupalPage::GetPageNodeType() == 'committee_post') {
            $instance = self::GetInstance();
            return ($instance->getCommitteeLinksCount() > 0);
       }
       return false;
       */
    }


    public static function Test() {
        $instance = self::GetInstance();
        return $instance->getPagesForCommittee();
    }
}


// end TCommitteeBlock
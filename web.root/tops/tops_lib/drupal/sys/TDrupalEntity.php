<?php
/*****************************************************************
Class:  TDrupalEntity
Description:
*****************************************************************/
class TDrupalEntity
{
    protected $contentType;
    protected $title;
    protected $nid = -1;
    protected $webSite;
    protected $teaserImage = '';
    protected $bodyImage = '';
    protected $moreContent = false;

    public function __construct($contentType = 'unknown') {
        $this->contentType = $contentType;
    }

    public function __toString() {
        if (isset($this->title))
            return $this->title;
        if (isset($this->contentType))
            return $this->contentType;
        return "TDrupalEntity";
    }

    public function loadByTitle($title) {
        $this->title = $title;
        $query =
            db_query("SELECT nid from {node} where type = '%s' and title = '%s'",
                $this->contentType, $title);
        $nid = db_result($query);

        if (empty($nid))
            $this->nid = 0;
        $this->nid = $nid;
    }

    public function loadFromNode($node) {
        $this->contentType = $node->type;
        $this->title = $node->title;
        $this->nid = $node->nid;
        if (isset($node->field_website))
            $this->webSite = $node->field_website[0]['value'];

//TTracer::On();

        if (isset($node->field_body_image))
            $this->bodyImage = $node->field_body_image[0]['filepath'];
        else
            $this->bodyImage =  '';

        $this->teaserImage = '';
        $option = empty($node->field_imageoption[0]['value']) ? 'teaser' : $node->field_imageoption[0]['value'];
//TTracer::Trace("option: $option ");
        switch($option) {
            case 'teaser' :
                if (isset($node->field_teaser_image))
                    $this->teaserImage = $node->field_teaser_image[0]['filepath'];
                break;
            case 'body' :
                $this->teaserImage = $node->field_body_image[0]['filepath'];
                break;
            case 'thumbnail' :
                $this->teaserImage = $node->field_body_image[0]['filepath'].'.thumb.jpg';
                break;
        }

        $this->moreContent = $node->readmore;
        /*
        TTracer::On();
        TTracer::Trace("teaser image = $this->teaserImage");
        TTracer::Trace("body image = $this->bodyImage");
        */
    }


    private function getWebsiteFromData() {
        // TODO: Query against content website table
        //       might not need this.
        $this->webSite = '';

    }

    public function getWebsite() {
        if (!isset($this->webSite)) {
            if ($nid == 0)
                $this->webSite = '';
            if ($nid > 0)
                $this->getWebsiteFromData();
        }
        return $this->webSite;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getImageTag($image, $align='') {
        if (empty($image))
            return '';
        $alignment = empty($align) ? '' : sprintf('align="%s" ',$align);

        // TODO: Image location in config
        return sprintf('<img class="entityPicture entityPicture-%s" src="/%s" alt="Picture of %s" %s />',
            $align, $image, $this->title, $alignment);
    }



    public function getTeaserImageTag($align='') {

        return $this->getImageTag($this->teaserImage,$align);
    }

    public function getBodyImageTag($align='') {
        return $this->getImageTag($this->bodyImage,$align);
    }

    public function getNodeLink() {
        if ($this->nid < 1)
            return $this->title;
        return sprintf('<a href="/?q=node/%d">%s</a>',$this->nid, $this->title);
    }

    public function getWebSiteLink() {
        if (empty($this->webSite))
            return '';
        return sprintf(
            '<a href="%s" title="Web Site">%s Web Site</a>',
                $this->webSite, $this->title);
    }

    public function getReadMoreLink() {
        if ($this->moreContent) {
            return sprintf(
                '<a href="/?q=node/%d" title="Read more about %s">Read more...</a>',
                $this->nid,
                $this->title);
        }
        return '';
    }

    public function getLinks() {
        $website = $this->getWebSiteLink();
        $more = $this->getReadMoreLink();
        $result = '';
        if ($webSite)
            $result .= sprintf('<li>%s</li>',$webSite);
        if ($more)
            $result .=  sprintf('<li>%s</li>',$more);
        if ($result)
            return sprintf('<div class="entityLinks"><ul>%s</ul></div>',$result);
        return '';
    }

    public static function FindByTitle($contentType, $title) {
        $result = new TDrupalEntity($contentType);
        $result->loadByTitle($title);
        return $result;
    }

    public static function Create($node) {
        $result = new TDrupalEntity($node->type);
        $result->loadFromNode($node);
        return $result;
    }
}
// end TDrupalEntity




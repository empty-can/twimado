<?php
require_once ("init.php");

class Pawoo extends Toot {

    public $providerIcon = 'https://pawoo.net/favicon.ico';
    public $mutterBase = "https://pawoo.net/web/statuses/";
    public $domain = "pawoo";

    public function __construct($toot)
    {
        if (is_array($toot))
            $toot = (object) $toot;

        parent::__construct($toot);
        
        $this->cutMediaURL();
        $this->lineTagLinks();
        $this->rewriteTagLinks();
        $this->trimText();

        // pixivカードがあれば取得
        if (isset($toot->pixiv_cards)) {
            foreach ($toot->pixiv_cards as $card) {
                $pixivCard = new PixivCards($card);
                //                 myVarDump($card)
                if ($pixivCard->hasMedia()) {
                    $this->media[] = $pixivCard;
                    $this->isObject = true;
                }
//                 var_dump($this->thumbnailURLs);
            }
        }
    }
    
//     protected function extractGoods() {
//         $this->goods[] = $this->text;
//     }
    
    protected function cutMediaURL() {
        $match = array();
        $pattern = '(https?://pawoo.net/media/[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)';
        preg_match_all($pattern, $this->text, $match, PREG_SET_ORDER);
        foreach ($match as $token) {
            $this->text = mb_ereg_replace('<a href="'.preg_quote($token[0]).'".*?</a>', "", $this->text);
        }
    }
    
    protected function lineTagLinks() {
        $match = array();
        $pattern = '(<a href="http[s]?://pawoo.net/tags/.*?<br />)';
        preg_match_all($pattern, $this->text, $match, PREG_SET_ORDER);
        foreach ($match as $token) {
            $this->text = mb_ereg_replace(preg_quote($token[0]), mb_ereg_replace("<br />", " ", $token[0]), $this->text);
        }
    }
    
    protected function rewriteTagLinks() {
        $pattern = '|<a href="http[s]?://pawoo.net/tags/|';
        $replacement = '<a href="https://www.suki.pics/timeline/search.php?q=';
        $this->text = preg_replace($pattern, $replacement, $this->text);
        
        $pattern = '|rel="tag"|';
        $replacement = 'rel="tag" target="_blank"';
        $this->text = preg_replace($pattern, $replacement, $this->text);
    }
}
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
}
<?php
require_once ("init.php");

abstract class StandardMutter implements Mutter {

    public $id = "-1";
    public $isRe = false;
    public $date = "0";
    public $time = "0";
    public $text = "初期値";

    public $account = null;
    public $retweeter = null;

    public $originalId = -1;
    public $originalTime = "0";
    public $originalDate = "0";

    public $comCount = "-1";
    public $favCount = "-1";
    public $reCount = "-1";

    public $sensitive = false;

    public $isImg = false;
    public $isVideo = false;
    public $isObject = false;
    public $media = array();

    public $providerIcon = "初期値";
    public $domain = "初期値";
    public $mutterBase = "初期値";
    public $mutterURL = "初期値";

    public $isReply = false;
    public $selfReply = false;
    public $selfTweet = false;
    public function isOriginal() {
        return $this->isOriginal;
    }

    public $favorited = false;
    public $retweeted = false;

    public $goods = array();

    public $sortValue = 0;


    public function domain() {
        return $this->domain;
    }

    public function isFavorited() {
        return $this->favorited;
    }

    public function isRetweeted() {
        return $this->retweeted;
    }

    public function hasMedia() {
        return !empty($this->media);
    }

    public function isRe() {
        return $this->isRe;
    }

    public function isImg() {
        return $this->isImg;
    }

    public function isVideo() {
        return $this->isVideo;
    }

    public function isObject() {
        return $this->isObject;
    }

    public function comCount() {
        return $this->comCount;
    }

    public function favCount() {
        return $this->favCount;
    }

    public function reCount() {
        return $this->reCount;
    }

    public function time() {
        return $this->time;
    }

    public function date() {
        return $this->time;
    }

    public function originalDate() {
        return $this->originalDate;
    }

    public function id() {
        return $this->id;
    }

    public function account() {
        return $this->account;
    }

    public function text() {
        return $this->text;
    }

    public function providerIcon() {
        return $this->providerIcon;
    }

    public function sensitive() {
        return $this->sensitive;
    }

    public function originalId() {
        return $this->originalId;
    }

    public function originalTime() {
        return $this->originalTime;
    }

    public function retweeter() {
        return $this->retweeter;
    }

    public function isReply() {
        return $this->isReply;
    }

    public function selfReply() {
        return $this->selfReply;
    }

    public function selfTweet() {
        return $this->selfTweet;
    }

    public function mutterURL() {
        return $this->mutterBase.$this->id;
    }

    public function convertArray() {
        return obj_to_array($this);
    }

    public function toArray() {
        return obj_to_array($this);
    }

    public function extractGoods() {
    }

    public function getSortValue() {
        return $this->sortValue;
    }

    public function getGoods() {
        return $this->goods;
    }

    protected function trimText() {
        $this->text = trim($this->text);
    }

    public function getThumbnail() {
        return $this->media[0]->getUrl();
    }

    public function getRawURL() {
        return $this->media[0]->getRaw();
    }
}
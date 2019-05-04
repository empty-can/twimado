<?php
require_once ("init.php");

class Toot extends StandardMutter implements Mutter {
    
    public function __construct($toot) {
        if(is_array($toot))
            $toot = (object)$toot;

        $this->id = $toot->id;
        $this->time = strtotime($toot->created_at);
        $this->date = $this->date();
        $this->sortValue = $toot->created_at;

        // リツイートだった場合、ツイートID以外の情報をリツイート元に差し替える処理を入れる
        if (isset($toot->reblog)) {
            $this->retweeter = new MastodonAccount($toot->account);
            $this->isRe = true;
            $toot = $toot->reblog;
            
            if (is_array($toot))
                $toot = (object) $toot;
        }
        
        $this->originalId = $toot->id;
        $this->originalTime = strtotime($toot->created_at);
        $this->originalDate = strtotime($toot->created_at);
        
        $this->text = $toot->content;
        
        $this->mutterURL = $this->mutterBase.$this->id;
        
        $this->account = new MastodonAccount($toot->account);
        
        $this->comCount = "";
        $this->favCount = $toot->favourites_count;
        $this->reCount = $toot->reblogs_count;
        
        if($toot->favourited) {
            $this->favorited = $toot->favourited;
            $this->favCount++;
        }
        if($toot->reblogged) {
            $this->retweeted = true;
            $this->reCount++;
        }
        
        $this->sensitive = $toot->sensitive;
        
        // メディアURLを取得
        if(isset($toot->media_attachments)) {
            $this->media = array();
            
            foreach($toot->media_attachments as $media) {
                $mediaURL = (is_array($media)) ? $media["url"] : $media->url;
                $thumbnailURL = (is_array($media)) ? $media["url"] : $media->url;
                $this->media[] = new Media($mediaURL, $thumbnailURL);
                
                if (isImg($mediaURL))
                    $this->isImg = true;
                else if (isVideo($mediaURL))
                    $this->isVideo = true;
                
            }
        }
    }
}
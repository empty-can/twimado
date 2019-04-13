<?php
require_once ("init.php");

class Toot extends StandardMutter implements Mutter {
    
    public function __construct($toot) {
        if(is_array($toot))
            $toot = (object)$toot;

        $this->id = $toot->id;
        $this->time = strtotime($toot->created_at);
        $this->date = $this->date();

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
        $this->originalDate = $this->originalDate();
        
        $this->text = $toot->content;
        
        $this->mutterURL = $this->mutterBase.$this->id;
        
        $this->account = new MastodonAccount($toot->account);
        
        $this->comCount = "";
        $this->favCount = $toot->favourites_count;
        $this->reCount = $toot->reblogs_count;
        
        $this->sensitive = $toot->sensitive;
        
        // メディアURLを取得
        if(isset($toot->media_attachments)) {
            $this->media = array();
            
            foreach($toot->media_attachments as $media) {
                $mediaURL = (is_array($media)) ? ((object)$media)->url : $media->url;
                $thumbnailURL = (is_array($media)) ? ((object)$media)->url : $media->url;
                $this->media[] = new Media($mediaURL, $thumbnailURL);
                
                if (isImg($mediaURL))
                    $this->isImg = true;
                else if (isVideo($mediaURL))
                    $this->isVideo = true;
                
            }
        }
    }
}
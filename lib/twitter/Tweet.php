<?php
require_once ("init.php");

class Tweet extends StandardMutter implements Mutter {
    
    public $providerIcon = 'https://abs.twimg.com/responsive-web/web/icon-default.604e2486a34a2f6e1.png';
    public $mutterBase = "https://twitter.com/tyanmiaisiteru/status/";
    public $domain = "twitter";
    
    public function __construct($tweet) {
        if(is_array($tweet))
            $tweet = (object)$tweet;
            
        $this->id = $tweet->id;
        $this->time = strtotime($tweet->created_at);
        $this->date = $this->date();
        
        // リツイートだった場合、ツイートID以外の情報をリツイート元に差し替える
        if(isset($tweet->retweeted_status)) {
            $this->retweeter = new TwitterAccount($tweet->user);
            $tweet = $tweet->retweeted_status;
            $this->isRe = true;
            
            if(is_array($tweet))
                $tweet = (object)$tweet;
        }
        
        $this->originalId = $tweet->id;
        $this->originalTime = strtotime($tweet->created_at);
        $this->originalDate = $this->originalDate();
        
        $this->text = nl2br(searchTag(decorateLinkTag(mb_ereg_replace("http[s]?://t\.co/[a-zA-Z0-9]+", "", $tweet->text))));
        $this->mutterURL = $this->mutterBase.$this->id;
        
        $this->account = new TwitterAccount($tweet->user);
        
        $this->comCount = "";
        $this->favCount = $tweet->favorite_count;
        $this->reCount = $tweet->retweet_count;
        
        $this->sensitive = (isset($tweet->possibly_sensitive)) ? $tweet->possibly_sensitive : true;
        
        // メディアURLを取得
        if(isset($tweet->extended_entities) && isset($tweet->extended_entities->media)) {
            $this->mediaURLs = array();
            foreach($tweet->extended_entities->media as $media) {
                if(isset($media->type) && (($media->type=='video') || ($media->type=='animated_gif'))) {
                    //                     var_dump($media);
                    $tmp = null;
                    foreach($media->video_info->variants as $video) {
                        if($video->content_type == "video/mp4") {
                            $tmp = new Media($video->url, $media->media_url);
                            $this->isVideo = true;
                        }
                    }
                    
                    if($this->isVideo)
                        $this->media[] = $tmp;
                } else {
                    $suffix = get_suffix($media->media_url);
                    $thumbnail = str_replace('.'.$suffix, "?format=$suffix&name=small", $media->media_url);
                    
                    $this->media[] = new Media($media->media_url, $thumbnail);
                    $this->isImg = true;
                }
            }
        }
    }
}
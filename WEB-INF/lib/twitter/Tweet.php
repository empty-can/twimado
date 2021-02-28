<?php
require_once ("init.php");

use Jenssegers\ImageHash\ImageHash;
use Jenssegers\ImageHash\Implementations\DifferenceHash;

class Tweet extends StandardMutter implements Mutter {

    public $providerIcon = 'https://abs.twimg.com/responsive-web/web/icon-default.604e2486a34a2f6e1.png';
    public $mutterBase = "https://twitter.com/tyanmiaisiteru/status/";
    public $domain = "twitter";

    public function __construct($tweet) {
        if(is_array($tweet))
            $tweet = (object)$tweet;

        $this->id = $tweet->id;
        $this->time = strtotime($tweet->created_at);
        $this->sortValue = $this->time;
        $this->date = $this->date();
        $this->originalId = $tweet->id;

        // リツイートだった場合、ツイートID以外の情報をリツイート元に差し替える
        if(isset($tweet->retweeted_status)) {
            $this->retweeter = new TwitterAccount($tweet->user);
            $tweet = $tweet->retweeted_status;
            $this->isRe = true;
            $this->isRetweeted = true;

            if(is_array($tweet))
                $tweet = (object)$tweet;
        } else {
            $this->selfTweet = true;
        }

        $this->originalId = $tweet->id;
        $this->originalTime = strtotime($tweet->created_at);
        $this->originalDate = strtotime($tweet->created_at);

        $this->text = nl2br(searchTag(decorateLinkTag(mb_ereg_replace("http[s]?://t\.co/[a-zA-Z0-9]+", "", $tweet->text))));
        // $this->text = nl2br(searchTag(decorateLinkTag($tweet->text)));
        $this->mutterURL = $this->mutterBase.$this->id;

        $this->account = new TwitterAccount($tweet->user);

        if($this->account->id()===$tweet->in_reply_to_user_id_str)
            $this->selfReply = true;

        $this->comCount = "";
        $this->favCount = ceilNum($tweet->favorite_count);
        $this->reCount = ceilNum($tweet->retweet_count);

        $this->favorited = $tweet->favorited;
        $this->retweeted = $tweet->retweeted;

        $this->isReply = $tweet->in_reply_to_status_id;

        $this->sensitive = (isset($tweet->possibly_sensitive)) ? $tweet->possibly_sensitive : false;
        // メディアURLを取得
        if(isset($tweet->extended_entities) && isset($tweet->extended_entities->media)) {
            $this->mediaURLs = array();
            foreach($tweet->extended_entities->media as $media) {
                if(isset($media->type) && (($media->type=='video') || ($media->type=='animated_gif'))) {
                    //                     var_dump($media);
                    $tmp = null;
                    foreach($media->video_info->variants as $video) {
                        if($video->content_type == "video/mp4") {
                            $tmp = new Media($video->url, $media->media_url_https);
                            $this->isVideo = true;
                        }
                    }

                    if($this->isVideo)
                        $this->media[] = $tmp;
                } else {
                    $suffix = get_suffix($media->media_url);
                    $rootPath = str_replace('.'.$suffix, "", $media->media_url_https);
                    $thumbnail = $rootPath."?format=$suffix&name=small";
                    $origninal = $rootPath."?format=$suffix&name=orig";

                    $this->media[] = new Media($origninal, $thumbnail, $media->media_url_https);
                    $this->isImg = true;
                }
            }
        }
//         if ((10000 < $tweet->favorite_count) && ($tweet->favorite_count < 200000) && ($this->isImg || $this->isVideo) && ! contains($this->text, "拡散")) {
//             $isReply = (empty($tweet->in_reply_to_status_id)) ? 0 : 1;
//             addMatomeTimeline($tweet->id, 'twitter', $this->account->id, strtotime($tweet->created_at), $tweet->favorite_count, $tweet->retweet_count, 1, $isReply);
//         }
        // myVarDump("id:".$tweet->id.", accountId:".$this->account->id.", time:".$this->time.", favorite_count:".$tweet->favorite_count.", retweet_count:".$tweet->retweet_count.", isReply:".$isReply);
        updateMutter($tweet->id, "twitter", $tweet->favorite_count, $tweet->retweet_count);
    }

    public function isNotMediaInfo() {
        $result = false;

        if(!$this->isImg)
            return false;

        foreach ($this->media as $medium) {
            $url = $medium->thumb;
            $result |= (isMediaInfo($url, $this->originalId, $this->domain)==0);
        }

        return $result;
    }

    public static function insertMediaTable(array $tweets) {
        $mydb = new MyDB();
        $hasher = new ImageHash(new DifferenceHash());
        $sql = "";
        $results = array();
        foreach ($tweets as $tweet) {
            $mutterId = $tweet->originalId;
            $media = $tweet->media;

            foreach ($media as $medium) {
                $url = $mydb->escape($medium->thumb);
                $hash = $hasher->hash($url)->toHex();
                $sql .= "INSERT INTO media VALUES ('$url', $mutterId, 'twitter', '$hash');";
                $results = $mydb->insert("INSERT INTO media VALUES ('$url', $mutterId, 'twitter', '$hash');");
            }
        }

        $mydb->close();

        return $results;
    }
}
<?php
/**
 * DBインタフェースの隠蔽化
 *
 *
 */
class MyDB {
    private $hostname = DbHost;
    private $user = DbAccount;
    private $password = DbPassword;
    private $dbname = DbName;

    private $dbo;


    function insert($query) {
        $result = $this->dbo->query($query);

        if (!$result) {
            echo $query;
            printf("Errormessage: %s\n", $this->dbo->error);
            exit();
        }

        return $result;
    }


    function select($query) {
        $array = array();

        $result = $this->dbo->query($query);

        if (!$result) {
            //       echo $query;
            //       printf("Errormessage: %s\n", $this->dbo->error);
            //       exit();
        }

        if(!is_object($result))
            return null;

            for ($i=0; $row = $result->fetch_row(); $i++)
                $array[$i] = $row;

                return $array;
    }

    /**
     * 文字列をエスケープ
     * 文字列以外はスルー
     *
     */
    public function escape($param) {
        if(is_string($param))
            return $this->dbo->real_escape_string($param);
            else
                return $param;
    }

    /**
     * クエリ実行
     *
     */
    function query($query) {
        $result = $this->dbo->query($query);

        if (!$result) {
            //       echo $query;
            //       printf("Errormessage: %s\n", $this->dbo->error);
            //       exit();
        }

        return $result;
    }

    /**
     *
     *
     */
    function close() {
        // DB接続を閉じる
        $this->dbo->close();
    }

    /**
     * コンストラクタ
     *
     */
    function __construct($db_type = 'MySQL') {

        // DB接続
        $this->dbo = new mysqli($this->hostname, $this->user, $this->password, $this->dbname);
        $this->dbo->set_charset("utf8");

        if ($this->dbo->connect_error) {
            //      echo $this->dbo->connect_error;
            //      exit();
        } else {
            $this->dbo->set_charset("utf8");
        }
    }

    /**
     * デストラクタ
     *
     */
    function __destruct()
    {
    }
}
<?php
date_default_timezone_set('Asia/Tokyo');

require_once dirname(__FILE__).'/function/phpQuery-onefile.php';
require_once dirname(__FILE__).'/function/put_file.php';
require_once dirname(__FILE__).'/function/put_img.php';

try{

    $art_data = 'https://www.sanrio.co.jp/news/mx-event-miracle-sunshinecity-20220617/';

    // csvファイル書き出し
    putFile($art_data);

    // 画像ダウンロード
    putImg($art_data);

} catch(Exception $e) {
    echo $e->getMessage();
}

?>
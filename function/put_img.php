<?php 

function putImg($data) {
    try {

        // 出力先ディレクトリ生成・上書き
        $dl_path = './output/img/';
        if(!(file_exists($dl_path))){
            mkdir($dl_path, 0777);
        }else{
            array_map('unlink', glob($dl_path.'*.*'));
        }

        $html = file_get_contents($data);
        $html_dom = phpQuery::newDocument($html);
        $scraped_str = $html_dom[".main_container"];
        mb_convert_variables('SJIS', 'UTF-8', $scraped_str);
        $img_src = $scraped_str->find('img');

        // 関連キャラ抜き出し
        $scraped_relate = $html_dom['.relation_box'];
        $relate_img_src = $scraped_relate->find('img');

        // html画像ダウンロード
        $img_length = $img_src->length();
        $relate_img_length = $relate_img_src->length();
        $all_img_src = $img_length - $relate_img_length;
        for ($j = 0; $j < $all_img_src; $j++) {
            $src_url = $html_dom[".main_container"]->find("img:eq($j)")->attr('src');
            $img_data = file_get_contents($src_url);
            // 拡張子取得
            $ext = pathinfo($src_url, PATHINFO_EXTENSION);
            // 画像ダウンロードファイル名
            $dl_name = 'image_'.($j + 1).'.'.$ext;
            file_put_contents($dl_path.$dl_name, $img_data);
        }

        // css(bg)画像
        $bg_img = [
            'https://www.sanrio.co.jp/common_v2/img/icon_arr_right_pink.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_print.png'
        ];
        
        $count_bg = count($bg_img);

        // css画像ダウンロード
        for ($p = 0; $p < $count_bg; $p++) {
            $bg_data = file_get_contents($bg_img[$p]);
            // 元ファイル名取得
            $dl_css_name = basename($bg_img[$p]);
            file_put_contents($dl_path.$dl_css_name, $bg_data);
        }
    } catch(Exception $e) {
        echo $e->getMessage();
    }
}

?>
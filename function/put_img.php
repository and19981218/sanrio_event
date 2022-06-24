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
        $scraped_src = $html_dom[".main_container"]->find("img");
        mb_convert_variables('SJIS', 'UTF-8', $scraped_src);

        // html画像ダウンロード
        for ($j = 0; $j < $scraped_src->length(); $j++) {
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
            'https://www.sanrio.co.jp/common_v2/img/bg_cont_part.gif',
            'https://www.sanrio.co.jp/common_v2/img/icon_arr_right_large.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_ext_link_white.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_ext_link.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_carousel_prev.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_carousel_next.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_indicater.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_indicater_active.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_accordion_on.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_accordion_off.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_oshop.png',
            'https://www.sanrio.co.jp/common_v2/img/icon_cart.png'
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
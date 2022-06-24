<?php 

function putFile($data) {
    try {

        // 出力先ディレクトリ生成
        $output_path = './output/';
        if(!(file_exists($output_path))){
            mkdir($output_path, 0777);
        }

        // 出力設定
        $output_name = $output_path . 'index.html';
        $file_name = 'index.html';
        $file_open = fopen($output_name, 'w');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_name); 
        header('Content-Transfer-Encoding: binary');

        // コンテンツ抽出
        $html = file_get_contents($data);
        $html_dom = phpQuery::newDocument($html);
        $scraped_str = $html_dom['.main_container'];
        mb_convert_variables('SJIS', 'UTF-8', $scraped_str);
        $scraped_ttl = $scraped_str->find('h2:eq(0)')->text();
        $img_src = $scraped_str->find('img');

        // id付与
        $scraped_str = preg_replace('/<div class="main_container">/', '<div id="old_event" class="main_container">', $scraped_str);

        // html画像パス書き換え
        for ($j = 0; $j < $img_src->length(); $j++) {
            $src_url = $html_dom[".main_container"]->find("img:eq($j)")->attr('src');
            // 拡張子取得
            $ext = pathinfo($src_url, PATHINFO_EXTENSION);
            // 画像パス
            $replace_path = './img/image_'.($j + 1).'.'.$ext;
            $scraped_str = str_replace($src_url, $replace_path, $scraped_str);
        }

        // css読み込み
        $css_src = [ 
            'https://www.sanrio.co.jp/common_v2/css/common.css?202007', 
            'https://www.sanrio.co.jp/rs_v2/news/event/css/NW2b.css', 
            'https://www.sanrio.co.jp/common_v2/css/reaction.css',
            'https://www.sanrio.co.jp/common_v2/css/rwd.css'
        ];
        $scraped_css_all = "";
        foreach($css_src as $org_css){
            $scraped_css = file_get_contents($org_css);
            $scraped_css_all .= $scraped_css;
        }

        // css画像パス書き換え
        $bg_img = [
            '/common_v2/img/bg_cont_part.gif',
            '/common_v2/img/icon_arr_right_large.png',
            '/common_v2/img/icon_ext_link_white.png',
            '/common_v2/img/icon_ext_link.png',
            '/common_v2/img/icon_carousel_prev.png',
            '/common_v2/img/icon_carousel_next.png',
            '/common_v2/img/icon_indicater.png',
            '/common_v2/img/icon_indicater_active.png',
            '/common_v2/img/icon_accordion_on.png',
            '/common_v2/img/icon_accordion_off.png',
            '/common_v2/img/icon_oshop.png',
            '/common_v2/img/icon_cart.png'
        ];
        $bg_img_length = count($bg_img);
        for ($p = 0; $p < $bg_img_length; $p++) {
            // ファイル名取得
            $bg_img_name = basename($bg_img[$p]);
            $scraped_css_all = str_replace($bg_img[$p], './img/'.$bg_img_name, $scraped_css_all);
        }
        $scraped_css_all = '<style>'.$scraped_css_all.'</style>';

        // ヘッダタグ抽出
        $scraped_head = $html_dom['head'];
        mb_convert_variables('SJIS', 'UTF-8', $scraped_head);

        // HTML・ヘッダ・bodyタグ追記、css合体
        $output_data = '<html>'.$scraped_head.'<body>'.$scraped_str.$scraped_css_all.'</body>'.'</html>';

        // 改行削除
        $output_data = preg_replace("/\n|\r|\r\n/", "", $output_data);

        // ファイルを出力
        fputs($file_open, $output_data);
        fclose($file_open);

    } catch(Exception $e) {
        echo $e->getMessage();
    }
}

?>
<?php
/*
 * SBI証券 出来高ランキング 買い・売り 抽出
 *
 * @param: string argv[1] 入力 htmlファイルパス
 * @param: string argv[2] 出力 買いcsvファイルパス
 * @param: string argv[3] 出力 売りcsvファイルパス
 *
 * https://www.sbisec.co.jp/ETGate/?_ControlID=WPLETmgR001Control&_PageID=WPLETmgR001Mdtl20&_DataStoreID=DSWPLETmgR001Control&_ActionID=DefaultAID&burl=iris_ranking&cat1=market&cat2=ranking&dir=tl1-rnk%7Ctl2-stock%7Ctl3-thisrank%7Ctl4-turnover&file=index.html&getFlg=on
 *
 * $ php TRADERANKING.php /dat/trade_ranking_20231201.html /csv/buy_20231201.csv /csv/sell_20231201.csv
 */
// ToDo  HTMLURL違う？よく確認. データチェック
// ToDo  シェルにphp組み込み。引数渡すようにする
try {
        $binFile = __FILE__;
        $binDir = dirname($binFile);
        $homeDir = dirname($binDir);

        // 引数チェック
        if (count($argv) != 4) {
                throw new Exception('ERROR 引数が異なります', 200);
        }
        var_dump($argv);

        $htmlfilepath =  $homeDir.$argv[1];
        $buyfilepath =   $homeDir.$argv[2];
        $sellfilepath = $homeDir.$argv[3];

var_dump($htmlfilepath); var_dump($buyfilepath); var_dump($sellfilepath); //exit();

        // htmlファイルパース
        if (file_exists($htmlfilepath) == false) {
                throw new Exception('ERROR 入力ファイルがありません', 200);
        }

        $contents = file_get_contents($htmlfilepath);
        $html = mb_convert_encoding($contents, 'HTML-ENTITIES', 'UTF-8');

        $dom = new DOMDocument('1.0', 'UTF-8');
        @$dom->loadHTML($html);

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace("php", "http://php.net/xpath");
        $xpath->registerPHPFunctions();

        // 買い 抽出・出力
        $tds = $xpath->query('//div[@class="md-col2-l"]/table/tbody/tr/td');
        $hrefs = $xpath->query('//div[@class="md-col2-l"]/table/tbody/tr/td/a[@class="rankProdLink"]');

        $nRow = $hrefs->length; // 30
        $i = 0;
        $lines = [];
        for ( $row = 0 ; $row < $nRow ; $row++ ) {
                $line = [];
                $line[] = $tds->item($i++)->nodeValue;

                $href = $hrefs->item($row)->getAttribute('href');
                $queries = [];
                parse_str(parse_url($href, PHP_URL_QUERY), $queries);
                $line[] = $queries['stock_sec_code_mul'];
                $line[] = $tds->item($i++)->nodeValue;
                $line[] = $tds->item($i++)->nodeValue;
                $line[] = $tds->item($i++)->nodeValue;
                $lines[] = $line;
        }
        if (file_exists($buyfilepath) || ($fp = fopen($buyfilepath, 'w+')) == false) {
                throw new Exception('ERROR 出力ファイルを作成できませんでした', 200);
        }
        foreach($lines as $line) {
                fputcsv($fp, $line);
        }
        fclose($fp);

        // 売り 抽出・出力
        $tds = $xpath->query('//div[@class="md-col2-r"]/table/tbody/tr/td');
        $hrefs = $xpath->query('//div[@class="md-col2-r"]/table/tbody/tr/td/a[@class="rankProdLink"]');

        $nRow = $hrefs->length; // 30
        $i = 0;
        $lines = [];
        for ( $row = 0 ; $row < $nRow ; $row++ ) {
                $line = [];
                $line[] = $tds->item($i++)->nodeValue;

                $href = $hrefs->item($row)->getAttribute('href');
                $queries = [];
                parse_str(parse_url($href, PHP_URL_QUERY), $queries);
                $line[] = $queries['stock_sec_code_mul'];
                $line[] = $tds->item($i++)->nodeValue;
                $line[] = $tds->item($i++)->nodeValue;
                $line[] = $tds->item($i++)->nodeValue;
                $lines[] = $line;
        }
        if (file_exists($sellfilepath) || ($fp = fopen($sellfilepath, 'w+')) == false) {
                throw new Exception('ERROR 出力ファイルを作成できませんでした', 200);
        }
        foreach($lines as $line) {
                fputcsv($fp, $line);
        }
        fclose($fp);
}
catch (Exception $e) {
        fputs(STDERR, $e->getMessage());
}

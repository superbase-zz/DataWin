<?php

    $host="56a3768226622.sh.cdb.myqcloud.com";
    $name="root";
    $password="ctfoxno1";
    $dbname="TV";
    $con=mysqli_connect($host,$name,$password,$dbname,4892) or die("Can't connect mysql!".mysqli_connect_error() );
    mysqli_query($con,"set names utf8");
    date_default_timezone_set("Asia/Shanghai");
    $date = date("Y-m-d");

    //判断tv_name和douban_tv表里面是否有重复的数据，如果有，在tv_name里删除
    $tv_name_name = mysqli_query($con, "select name from tv_name;");
    $tv_name_name = mysqli_fetch_all($tv_name_name);
    foreach ($tv_name_name as $key => $value) {
        $isExite = mysqli_query($con,"select count(*) from douban_tv where name=\"{$value[0]}\";");
        $isExite = mysqli_fetch_all($isExite);
        if($isExite[0][0] == '1'){
            mysqli_query($con,"delete from tv_name where name = \"{$value[0]}\"");
        }
    }
    //判断tv_name里面的电影在douban网中是否有完整的信息，如果有加入douban_tv并且从tv_name里删除
    $tv_name_href = mysqli_query($con, "select name,href from tv_name;");
    $tv_name_href = mysqli_fetch_all($tv_name_href);
    foreach ($tv_name_href as $key1 => $value) {
        $url = $value[1];
        //$url = "https://movie.douban.com/subject/26765222/";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1664.3 Safari/537.36");
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOSIGNAL, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        //提取数据
        preg_match_all('/<strong class="ll rating_num" property="v:average">(.*)<\/strong>/', $result, $rate);
        preg_match_all('/rel="v:directedBy">(.{0,10})<\/a>/', $result,$daoyan);
        preg_match_all('/<span ><span class=\'pl\'>编剧<\/span>: <span class=\'attrs\'>(.*)<\/span><\/span><br\/>/', $result, $bianju);
        if ($bianju[1] == NULL){
            $bianju[1] == NULL;
        }
        else {
            preg_match_all('/<a href=".*?">(.{0,10})<\/a>/', $bianju[1][0], $bianju);
        }
        preg_match_all('/rel="v:starring">(.{0,10})<\/a>/', $result, $zhuyan);
        preg_match_all('/<span property="v:genre">(.{0,10})<\/span>/', $result, $type);
        preg_match_all('/<span class="pl">制片国家\/地区:<\/span>(.*)<br\/>/', $result, $region);
        preg_match_all('/<span class="pl">语言:<\/span>(.*)<br\/>/', $result, $language);
        preg_match_all('/<span class="pl">首播:<\/span> <span property="v:initialReleaseDate" content=".*">(.*)<\/span><br\/>/',$result, $firstShow);
        preg_match_all('/<span class="pl">集数:<\/span>(.*)<br\/>/', $result, $jishu);
        preg_match_all('/<span class="pl">单集片长:<\/span>(.*)<br\/>/', $result, $danjipianchang);
        preg_match_all('/<span class="pl">又名:<\/span>(.*)<br\/>/', $result, $youming);
        preg_match_all('/<a class="playBtn" data-cn=".*" data-source=".*"  href="javascript: void 0;">\s*(.*)\s*<\/a>/', $result, $playBtn);
        preg_match_all('/<span class="buylink-price"><span>\s*(.*)\s*<\/span><\/span>/', $result, $buylink);
        preg_match_all('/<a href="\/tag\/.*" class="">(.*)<\/a>/', $result, $tag);
        //整合数据
        //通过判断首播是否存在，来判断信息是否全面
        if (count($firstShow[1]) == 0 || $rate[1][0] == "") {
            sleep(1);
            continue;
        }
        //daoyan
        if (count($daoyan[1]) != 1) {
            $temp = NULL;
            foreach ($daoyan[1] as $key1 => $value1) {
                $temp .= $value1.";";
            }
            $daoyan = $temp;
        }
        elseif (count($daoyan[1]) == 1) {
            $daoyan = $daoyan[1][0];
        }
        else {
            $daoyan = NULL;
        }
        //bianju
        if (count($bianju[1]) != 1) {
            $temp = NULL;
            foreach ($bianju[1] as $key1 => $value1) {
                $temp .= $value1.";";
            }
            $bianju = $temp;
        }
        elseif (count($bianju[1]) == 1) {
            $bianju = $bianju[1][0];
        }
        else {
            $bianju = NULL;
        }
        //zhuyan
        if (count($zhuyan[1]) != 1) {
            $temp = NULL;
            foreach ($zhuyan[1] as $key1 => $value1) {
                $temp .= $value1.";";
            }
            $zhuyan = $temp;
        }
        elseif (count($zhuyan[1]) == 1) {
            $zhuyan = $zhuyan[1][0];
        }
        else {
            $zhuyan = NULL;
        }
        //type
        if ($type[1] == NULL){
            $type = "NULL";
        }
        else {
            $type = $type[1][0];
        }
        //$region
        if ($region == NULL){
            $region = "NULL";
        }
        else {
            $region = $region[1][0];
        }
        //language
        if ($language[1] == NULL){
            $language = "NULL";
        }
        else {
            $language = $language[1][0];
        }
        //firstShow
        if ($firstShow[1] == NULL){
            $firstShow = "NULL";
            $zzsy = "0";
        }
        else {
            $firstShow = $firstShow[1][0];
            preg_match('/(.{0,10})/',$firstShow,$firstShowEx);
            if (strlen($firstShowEx[1]) < 10 || strpos($firstShowEx[1], '(') !=false ) {
                sleep(1);
                continue;
            }
            $diff = date_diff(date_create($date),date_create($firstShowEx[1]));
            if ($diff->format("%a") <= 90) {
                $zzsy = "1";
            }
            else {
                $zzsy = "0";
            }

        }
        //jishu
        if ($jishu[1] == NULL){
            $jishu = "NULL";
        }
        else {
            $jishu = $jishu[1][0];
        }
        //danjipianchang
        if ($danjipianchang[1] == NULL){
            $danjipianchang = "NULL";
        }
        else {
            $danjipianchang = $danjipianchang[1][0];
        }
        //youming
        if ($youming[1] == NULL){
            $youming = "NULL";
        }
        else {
            $youming = $youming[1][0];
        }

        //playBtn
        if (count($playBtn[1]) != 1) {
            $temp = NULL;
            foreach ($playBtn[1] as $key1 => $value1) {
                $temp .= $value1."/".$buylink[1][$key1].";";
            }
            $playBtn = $temp;
        }
        elseif (count($playBtn[1]) == 1) {
            $playBtn = $playBtn[1][0].$buylink[1][0];
        }
        else {
            $playBtn = NULL;
        }

        //tag
        if (count($tag[1]) != 1) {
            $temp = NULL;
            foreach ($tag[1] as $key1 => $value1) {
                $temp .= $value1.";";
            }
            $tag = $temp;
        }
        elseif (count($tag[1]) == 1) {
            $tag = $tag[1][0];
        }
        else {
            $tag = NULL;
        }

        var_dump($value[0]);
        mysqli_query($con, "insert into douban_tv values(\"{$value[0]}\", \"{$rate[1][0]}\", \"网络剧\", \"NULL\",\"{$daoyan}\", \"{$bianju}\", \"{$zhuyan}\", \"{$type}\"
                                                        , \"{$region}\", \"{$language}\", \"{$firstShow}\", \"{$jishu}\", \"{$danjipianchang}\"
                                                        , \"{$youming}\", \"{$playBtn}\", \"{$tag}\",\"{$zzsy}\",\"1\",\"{$url}\");");
        mysqli_query($con, "delete from tv_name where name = \"{$value[0]}\"");

        sleep(1);

    }


 ?>

<?php
//获取yiren名字
    $host="56a3768226622.sh.cdb.myqcloud.com";
    $name="root";
    $password="ctfoxno1";
    $dbname="filmdaily";
    $con=mysqli_connect($host,$name,$password,$dbname,4892) or die("Can't connect mysql!".mysqli_connect_error() );
    mysqli_query($con,"set names utf8");
    date_default_timezone_set("Asia/Shanghai");
    $date = date("Y-m-d");
    $filmname = mysqli_query($con,"select mainname from filmname where zzsy=1;");
    $filmname = mysqli_fetch_all($filmname);
    mysqli_close($con);

//连接yingxiang数据库
    $host="56a3768226622.sh.cdb.myqcloud.com";
    $name="root";
    $password="ctfoxno1";
    $dbname="yingxiang";
    $con=mysqli_connect($host,$name,$password,$dbname,4892) or die("Can't connect mysql!".mysqli_connect_error() );
    mysqli_query($con,"set names utf8");
    mysqli_query($con ,"delete from yingxiang_film_lastmonth;");

    foreach ($filmname as $key => $value) {
        $nameurl = urlencode($value[0]);
        $url = "http://s.weibo.com/impress?cate=ajax&key=".$nameurl."&page=-1&refer=tag&cuid=2883234484";
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
        if (json_decode($result)->state == 0) {
            continue;
        }
        $data = json_decode($result)->html;
        $data = explode("</section></article></div>", $data);
        array_pop($data);
        preg_match_all('/<a href=".*" class=".*"><span class="size(.*)">(.*)<\/span><\/a>/',$data[0], $temp);
        $size = $temp[1];
        $tag = $temp[2];
        $date = date_create();
        $date = date_modify($date, "-1 month");
        $date = date_format($date, "Y-m-d");

        foreach ($tag as $key1 => $value1) {
            if ($tag[$key1] == "") {
                continue;
            }
            $tag[$key1] = str_ireplace("<br>","",$tag[$key1]);
            mysqli_query($con, "insert into yingxiang_film_lastmonth values('{$value[0]}','{$tag[$key1]}','{$size[$key1]}','{$date}');");
            mysqli_query($con, "insert into yingxiang_film_history values('{$value[0]}','{$tag[$key1]}','{$size[$key1]}','{$date}');");
        }
        var_dump($value[0]);
    }
    mysqli_close($con);
 ?>

<?php
//调用python爬取数据
    date_default_timezone_set("Asia/Shanghai");
    $date = date("Y-m-d");
	$sdate = date_create($date);
	date_modify($sdate,"-28 days");
	$sdate = date_format($sdate,"Y-m-d");
        $edate = date_create($date);
        date_modify($edate,"-1 days");
        $edate = date_format($edate,"Y-m-d");
    //提取艺人名单
    $host="56a3768226622.sh.cdb.myqcloud.com";
    $name="root";
    $password="ctfoxno1";
    $dbname="yiren";
    $con=mysqli_connect($host,$name,$password,$dbname,4892) or die("Can't connect mysql!".mysqli_connect_error() );
    mysqli_query($con,"set names utf8");
    $yiren = mysqli_query($con, "select me from actname");
    $yiren = mysqli_fetch_all($yiren);
    mysqli_close($con);
    //入库
    $host="56a3768226622.sh.cdb.myqcloud.com";
    $name="root";
    $password="ctfoxno1";
    $dbname="zhishu";
    $con=mysqli_connect($host,$name,$password,$dbname,4892) or die("Can't connect mysql!".mysqli_connect_error() );
    mysqli_query($con,"set names utf8");
    foreach ($yiren as $key => $value) {
        $yirenname = $value[0];
        exec("python /var/www/html/zhishu/weibo_zhishu/yiren_qushi/getId.py $yirenname", $id);
        sleep(1);
        if ($id[1] == 'error') {
            $id = NULL;
            continue;
        }
        exec("python /var/www/html/zhishu/weibo_zhishu/yiren_qushi/getData.py $id[1] $sdate $edate", $json);
        sleep(1);
        $zt = json_decode($json[0])->zt;
        array_pop($zt);
        $zt_last = array_pop($zt);
        $yd = json_decode($json[0])->yd;
        $yd_last = array_pop($yd);
        //入库
        mysqli_query($con, "insert into wzs_yiren_qushi values('{$yirenname}', '{$zt_last->value}', '{$yd_last->pc}', '{$yd_last->mobile}', '{$zt_last->day_key}', '{$date}');");
        var_dump($yirenname);
        $id = NULL;
        $json = NULL;
    }

    mysqli_close($con);

 ?>

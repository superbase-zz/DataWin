<?php
/**
 * descript:用于检测数据库的状态
 * @date 2016/4/11
 * @author  XuJun
 * @version 1.0
 * @package
 */
date_default_timezone_set("Asia/Shanghai");
	header("Content-Type: text/html;charset=utf-8");

    //由于数据量很大，所以设置永不超时。
    set_time_limit(0);

	$result=array();



	//百度媒体指数查询
//	$baidumeiti=mysql_query("select * from baidumeiti",$con);
//	while($row=mysql_fetch_row($baidumeiti))
//	{
//		$baidumeit_arr=array();
//		$today=date("Y-m-d");
//		if($row[2]==$today)
//		{
//			$baidumeit_arr['name']="百度媒体指数";
//			$baidumeit_arr['time']=$today;
//			$baidumeit_arr['status']="入库成功";
//			$baidumeit_arr['systime']="23:00:00";
//			$result['baidumeiti']=$baidumeit_arr;
//			break;
//		}else{
//			$baidumeit_arr['name']="百度媒体指数";
//			$baidumeit_arr['time']=$today;
//			$baidumeit_arr['status']="尚未入库";
//			$baidumeit_arr['systime']="23:00:00";
//			$result['baidumeiti']=$baidumeit_arr;
//			break;
//		}
//
//	}


	//调用检测入库的状态
	//checkStatus("youyiba","友谊吧","19:00:00",2,"filmdaily");
	checkStatusForUpdate("baidu_news_yiren","百度新闻","12:10:00","acquitime",6,"baidu");
	checkStatusForUpdate("sogou_news_other","搜狗新闻","16:10:00","acquitime",6,"baidu");
	/**
	 * descript: 检测入库的状态
	 * @param $tabname:数据库表名 $strname:数据库的中文名  $systime:系统指定的入库时间 $i:表示采集时间是在第几列
	 * @return
	 * @date 2016/4/11
	 */
	function checkStatus($tabname,$strname,$systime,$i,$dbname)
	{
		$host="56a3768226622.sh.cdb.myqcloud.com:4892";
		$name="root";
		$password="ctfoxno1";
		//$dbname="filmdaily";
		global $result;

		//连接数据库
		$con=mysql_connect($host,$name,$password) or die("Can't connect mysql!");

		//选择数据库
		mysql_select_db($dbname,$con);

		//设置数据库表格编码


		mysql_query("set names utf8");

	    $sqlresult=mysql_query("select * from {$tabname}",$con);
		$arr=array();
		$today=date("Y-m-d");
		if($row=mysql_fetch_row($sqlresult))
		{
			if(strtotime($row[$i])>=strtotime("{$today}"))
			{
				$arr['name']=$strname;
				$arr['time']=$today;
				$arr['status']="<font color='#6B8E23'>入库成功</font>";
				$arr['systime']=$systime;
				$result["{$tabname}"]=$arr;

			}else{
				$arr['name']=$strname;
				$arr['time']=$today;
				$arr['status']="<font color='#CD0000'>尚未入库</font>";
				$arr['systime']=$systime;
				$result["{$tabname}"]=$arr;

			}

		}else{

				$arr['name']=$strname;
				$arr['time']=$today;
				$arr['status']="<font color='#CD0000'>尚未入库</font>";
				$arr['systime']=$systime;
				$result["{$tabname}"]=$arr;

		}
		mysql_close();
	} // end func

/////fuc of checkStatus for turing_zzb_*
        function checkStatusForUpdate($tabname,$strname,$systime,$time,$i,$dbname)
        {
                $host="56a3768226622.sh.cdb.myqcloud.com:4892";
                $name="root";
                $password="ctfoxno1";
                //$dbname="filmdaily";
                global $result;

                //连接数据库
                $con=mysql_connect($host,$name,$password) or die("Can't connect mysql!");

                //选择数据库
                mysql_select_db($dbname,$con);

                //设置数据库表格编码


                mysql_query("set names utf8");
            $sqlresult=mysql_query("select * from {$tabname} order by {$time} desc limit 1",$con);
                $arr=array();
                $today=date("Y-m-d");
                if($row=mysql_fetch_row($sqlresult))
                {
                        if(strtotime($row[$i])>=strtotime("{$today}"))
                        {
                                $arr['name']=$strname;
                                $arr['time']=$today;
                                $arr['status']="<font color='#6B8E23'>入库成功</font>";
                                $arr['systime']=$systime;
                                $result["{$tabname}"]=$arr;

                        }else{
                                $arr['name']=$strname;
                                $arr['time']=$today;
                                $arr['status']="<font color='#CD0000'>尚未入库</font>";
                                $arr['systime']=$systime;
                                $result["{$tabname}"]=$arr;

                        }

                }else{

                                $arr['name']=$strname;
                                $arr['time']=$today;
                                $arr['status']="<font color='#CD0000'>尚未入库</font>";
                                $arr['systime']=$systime;
                                $result["{$tabname}"]=$arr;

                }
                mysql_close();
        } 
	echo json_encode($result,JSON_UNESCAPED_UNICODE);
?>

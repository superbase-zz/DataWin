<?php
	/**
	*@手工数据插入数据库
	*@author: xujun
	*@date 2016/4/9
	*/

   header("Content-Type: text/html;charset=utf-8");

   //由于数据量很大，所以设置永不超时。
   set_time_limit(0);

   //计算百度搜索指数的行数
   $line = count(file('/var/www/html/uploads/maoyanfen.txt'));

   //打开百度搜索指数文件
   $maoyanfen=file("/var/www/html/uploads/maoyanfen.txt");

   //数据库的基本信息
   $host="56a3768226622.sh.cdb.myqcloud.com:4892";
   $name="root";
   $password="ctfoxno1";
   $dbname="filmdaily";

   //连接数据库
   $con=mysql_connect($host,$name,$password) or die("Can't connect mysql!");

   //选择数据库
   mysql_select_db($dbname,$con);

   //设置数据库表格编码
   mysql_query("set names utf8");

   //用于判断是否插入成功
   $flag=0;
   //用于显示在哪一行执行失败
   $num=0;

   //由于昨天的数据不需要，所以先删除数据再建立表格，如果是先清空数据也可以
    mysql_query("drop table if exists maoyanfen",$con);
    mysql_query("create table maoyanfen(mname varchar(30),mfen decimal(2,1),mfennum int(8),mwantnum int(8),macquitime date,primary key(mname,macquitime));",$con);

   //读取电影吧中的数据
	for($j=0;$j<$line;$j++)
	{
		//读取一行中的指定列的数据
      if($j>0)
      { //先分割一行的字符串
        $arr=explode("\t",$maoyanfen[$j]);
	    //获得字符串中艺人名称
        $mname=trim($arr[0]);
		$mfen=trim($arr[1]);
		$mfennum=trim($arr[2]);
		$mwantnum=trim($arr[3]);
		$macquitime=trim($arr[4]);

		$sqlinsert="insert into maoyanfen(mname,mfen,mfennum,mwantnum,macquitime) values('{$mname}','{$mfen}','{$mfennum}','{$mwantnum}','{$macquitime}')";

		echo $sqlinsert;

	    $result=mysql_query($sqlinsert,$con);
	    if(!$result)
		{
			$flag=1;
			$num=$j;
			break;
	    }

      }

	}

	if($flag==1)
	{
		//用于判断在哪一行执行失败
		echo "excute Failed, check line {$num}";

	}else{

		echo "excute OK";
	}
	mysql_close($con);

?>
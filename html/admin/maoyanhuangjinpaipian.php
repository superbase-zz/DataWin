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
   $line = count(file('/var/www/html/uploads/maoyanhuangjinpaipian.txt'));

   //打开百度搜索指数文件
   $maoyanhuangjinpaipian=file("/var/www/html/uploads/maoyanhuangjinpaipian.txt");

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
    mysql_query("drop table if exists maoyanhuangjinpaipian;",$con);
    mysql_query("create table maoyanhuangjinpaipian(m_gold_name varchar(30),m_gold_rowpiecerate decimal(4,2),m_gold_session int(5),m_gold_acquitime date,m_gold_type int(2) ,primary key(m_gold_name,m_gold_type,m_gold_acquitime));",$con);

   //读取电影吧中的数据
	for($j=0;$j<$line;$j++)
	{
		//读取一行中的指定列的数据
      if($j>0)
      { //先分割一行的字符串
        $arr=explode("\t",$maoyanhuangjinpaipian[$j]);
	    //获得字符串中艺人名称
        $name=trim($arr[0]);
		$rate=trim($arr[1]);
		$times=trim($arr[2]);
		$acquitime=trim($arr[3]);
		$type=trim($arr[4]);

		$sqlinsert="insert into maoyanhuangjinpaipian(m_gold_name,m_gold_rowpiecerate,m_gold_session,m_gold_acquitime,m_gold_type) values('{$name}','{$rate}','{$times}','{$acquitime}','{$type}')";

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
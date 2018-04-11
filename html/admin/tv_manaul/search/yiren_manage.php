<?php
    header("Content-type:text/html;charset=utf-8");
    $json = $_POST["json"];
    $option = $_POST["opt"];
    $json = json_decode($json,true);
    $host="56a3768226622.sh.cdb.myqcloud.com";
    $name="root";
    $password="ctfoxno1";
    $dbname="yiren";
    $con=mysqli_connect($host,$name,$password,$dbname,4892) or die("Can't connect mysql!".mysqli_connect_error());

/*返回值
error0: 删除和修改时未提供ｉｄ
error1: 插入数据未提供name
error2: 插入数据时不能提供ｉｄ
success_delete: 删除数据成功
success_insert: 插入数据成功
success_modify: 修改数据成功
*/

    if ($option == "delete") {
        $id = $json["id"];
        if ($id == "") {
            echo json_encode(array("result"=>"error0"));
        }else {
            $sql = "delete from yiren_info where id={$id}";
            $result = mysqli_query($con,$sql);
            echo json_encode(array("result"=>"success_delete"));
        }
    }
    elseif ($option == "insert") {
        $id = $json["id"];
        if ($id != "") {
            echo json_encode(array("result"=>"error2"));
        }else {
            $name = $json["name"];
            if ($name == "") {
                echo json_encode(array("result"=>"error1"));
            }else {
                foreach ($json as $key => $value) {
                    //去除非法字符
                    $json[$key] = str_replace("'","",$value);
                }
                $sql = "insert into yiren_info values(null,
                '{$json["name"]}','{$json["guoji"]}','{$json["mingzu"]}','{$json["xingzuo"]}','{$json["xuexing"]}','{$json["shengao"]}',
                '{$json["tizhong"]}','{$json["chushengdi"]}','{$json["chushengriqi"]}','{$json["zhiye"]}','{$json["biyexuexiao"]}',
                '{$json["jinjigongsi"]}','{$json["daibiaozuoping"]}','{$json["bieming"]}','{$json["zhuyaochengjiu"]}','{$json["changpiangongsi"]}',
                '{$json["peiou"]}','{$json["nver"]}','{$json["erzi"]}','{$json["gender"]}','{$json["introduction"]}'
                )";
		mysqli_query($con,$sql);
		$sql = "insert into actname values('{$json["name"]}',null,null,null,null,null,null,null,null,null,'0',null)";
                mysqli_query($con,$sql);
                echo json_encode(array("result"=>"success_insert"));
            }
        }
    }
    elseif ($option == "modify") {
        $id = $json["id"];
        if ($id == "") {
            echo json_encode(array("result"=>"error0"));
        }else {
            $str = "";
            foreach ($json as $key => $value) {
                //echo $key;
                if ($key == "id") {
                    continue;
                }
                if ($key == "name") {
                    continue;
                }
                if ($value != "") {
                        //去除非法字符
                    $value = str_replace("'","",$value);
                    $str .= $key."="."'$value',";
                }
            }
            if ($str == "") {
                echo json_encode(array("result"=>"error"));
            }
            else{
                $str = substr($str,0,strlen($str)-1);
                $sql = "update yiren_info set ".$str."where id={$id}";
                mysqli_query($con,$sql);
                echo json_encode(array("result"=>"success_modify"));
            }
        }
    }
    else {
        echo json_encode(array("result"=>"error"));
    }

?>

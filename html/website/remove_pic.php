<?php
/**
 *descript:	定期删除图片文件
 * @date 2016/6/22
 * @author  XuJun
 * @version 1.0
 * @package
 */

    $dir = './images';
    rmdirs($dir);

    //php删除指定目录下的的文件-用PHP怎么删除某目录下指定的一个文件？
    function rmdirs($dir){
        //error_reporting(0);    函数会返回一个状态,我用error_reporting(0)屏蔽掉输出
        //rmdir函数会返回一个状态,我用@屏蔽掉输出
        $dir_arr = scandir($dir);
        foreach($dir_arr as $key=>$val){
            if($val == '.' || $val == '..'){}
            else {
                if(is_dir($dir.'/'.$val))
                {
                    if(@rmdir($dir.'/'.$val) == 'true'){}    //去掉@您看看
                    else
                    rmdirs($dir.'/'.$val);
                }
                else
                unlink($dir.'/'.$val);
            }
        }
    }


?>
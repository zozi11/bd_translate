<?php
	//屏蔽notice错误
	@session_start();
	include('pdoclass.php');
	date_default_timezone_set('PRC');
	header("Content-type: text/html; charset=utf-8"); 
	error_reporting(E_ALL^E_NOTICE);
	$params = $_POST;
	unset($_POST);
	$funname = $params['funname']?$params['funname']:false;
	if (function_exists($funname)){
		$funname($params,$pdo);
	}else{
		$data['status'] = false;
		$data['msg'] = '无效请求!'.$funname;
		echo json_encode($data);die;
	}
	
	function do_translate($params){
	    
		$htmltag=$params['htmltag'];
		$wait_arr=$params['wait_arr'];
		$from=$params['from'];
		$to=$params['to'];
		
		//循环数组中的每个成员，进行翻译
		
		for($i=0;$i<count($wait_arr);$i++){
		    if(!$wait_arr[$i]){continue;}
		    $result_arr[$i]=language($wait_arr[$i],$from,$to);
		}
		//输出结果
		$data['status'] = true;
		$data['result_arr'] = $result_arr;
		$data['old_arr'] = $wait_arr;
		$data['htmltag'] = $htmltag;
		echo json_encode($data);die;
		
	}
	
	
	
	function language($value,$from="auto",$to="en")
{
    //首先检查数据库中是否存在，如果存在，直接返回数据库中对应的内容
    global $pdo;
    $old_str=urlencode($value);
    $strSql="select * from translate_list where old_str='{$old_str}' and new_to='{$to}'";
	
    $tmp=$pdo -> query($strSql, $queryMode = 'Row', $debug = false);
    if($tmp['id']){
        return urldecode($tmp['new_str']);die;
    }
    
    //否则，没有命中记录，则进行翻译，然后将记录写入数据库
    
  $appid="百度翻译API ID";
  $appkey="百度翻译API 秘钥";  //注册地址 https://fanyi-api.baidu.com/manage/developer
  $salt=mt_rand(11111111,99999999);
  $sign= md5($appid.$value.$salt.$appkey);
  
  $languageurl = "https://fanyi-api.baidu.com/api/trans/vip/translate?q={$old_str}&from={$from}&to={$to}&appid={$appid}&salt={$salt}&sign={$sign}";
  #生成翻译API的URL GET地址
  $re=language_text($languageurl);

  $text=json_decode($re);
  $text = $text->trans_result;
  
  $new_str=$text[0]->dst;
  if($new_str){
  
    $new_str=urlencode($new_str);
    $strSql="insert into translate_list set old_str='{$old_str}',new_to='{$to}',new_str='{$new_str}'";
    $pdo -> query($strSql, $queryMode = 'Row', $debug = false);
  }
  
  return $new_str;
}
function language_text($url)  #获取目标URL所打印的内容
{
  if(!function_exists('file_get_contents')) {
   $file_contents = file_get_contents($url);
  } else {
  $ch = curl_init();
  $timeout = 5;
  curl_setopt ($ch, CURLOPT_URL, $url);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  $file_contents = curl_exec($ch);
  curl_close($ch);
  }
   return $file_contents;
}


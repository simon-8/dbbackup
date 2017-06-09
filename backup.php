<?php
require __DIR__ . '/config.php';
require __DIR__ . '/vendor/autoload.php';
use Qiniu\Storage\UploadManager;
use Qiniu\Auth;

$sqlFile = __DIR__ . '/database.sql';
$keyName = $webSite . '/' . date('Ymd') . '.sql';
$command = '"mysqldump" --opt -u %s --password=%s %s > "%s"';


$command = sprintf($command, $username, $password, $database, str_replace('/','\\',$sqlFile));

if(function_exists('popen')){
	$handle = popen($command,'r');
	if(!$handle){
		WriteLog('备份失败[popen]');
		exit();
	}
	pclose($handle);
}else if(function_exists('exec')){
	exec($command . ' 2>&1', $output, $return);
	if($return == 1){
		WriteLog('备份失败，exec : ' . var_export($output , true));
		exit();
	}
}else{
	WriteLog('备份失败，没有可用函数');
	exit();
}

if(file_exists($sqlFile)){
	$filesize = filesize($sqlFile);
	if($filesize < 1024){
		WriteLog('SQL文件异常，文件过小，请检查');
		exit();
	}
	try{
		$result = uploadToQiniu($keyName , $sqlFile);
		WriteLog('上传成功，Result: ' . var_export($result , true));
		unlink($sqlFile);
	}catch(Exception $exception){
		WriteLog('上传失败，七牛报错: ' . $exception->getMessage());
	}
}else{
	WriteLog('上传失败，sql文件未生成');
}

exit(0);

function uploadToQiniu($keyName , $filename){
	global $qiniuAK,$qiniuSK,$qiniuBucket;
	$upManager = new UploadManager();
	$auth = new Auth($qiniuAK, $qiniuSK);
	$token = $auth->uploadToken($qiniuBucket);
	list($ret, $error) = $upManager->putFile($token, $keyName, $filename);
    if ($error !== null) {
        throw new Exception($error);
    } else {
        return $ret;
    }
}

function WriteLog($logStr){
    $filename = __DIR__ . "/log.txt";
    $fh = fopen($filename,"a");
    fwrite($fh,"Log created at: " . date("Y-m-d H:i:s",time())."\r\n");
    fwrite($fh, $logStr . "\r\n");
    fclose($fh);
}
?>
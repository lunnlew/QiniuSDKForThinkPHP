QiniuSDKForThinkPHP
===================

使用ThinkPHP封装的七牛云存储API

七牛文档参考 Docs:<a href='http://docs.qiniu.com'>七牛开发平台文档</a>

如何在ThinkPHP中使用
-------------------
### 放置位置
    将Cloud文件夹放在ThinkPHP\Extend\Library\ORG目录下，你也可以按需要放在其他地方。
### 增加配置
	请在框架配置文件中加入下面配置项
	'THINK_SDK_QINIU'=>array(
		'APP_KEY'=>'<你的access key>',
		'APP_SECRET'=>'<你的secret key',
		'DOWN_DOMAIN'=>'<你的资源域>'
		),
### 使用DEMO
##### 资源管理接口
	1、查看单个文件属性信息
	import('ORG.Cloud.QiniuSDK');
	$Instance = QiniuSDK::getInstance('QiniuRS');
	list($ret, $err) = $Instance->Stat('cdnimg','test.png');
	if ($err !== null) {
    	var_dump($err);
	} else {
    	var_dump($ret);
	}
	2、复制单个文件
	import('ORG.Cloud.QiniuSDK');
	$Instance = QiniuSDK::getInstance('QiniuRS');
	list($ret, $err) = $Instance->Copy('cdnimg','test.png','cdnimg','test1.png');
	if ($err !== null) {
    	var_dump($err);
	} else {
    	var_dump($ret);
	}
	3、移动单个文件
	import('ORG.Cloud.QiniuSDK');
	$Instance = QiniuSDK::getInstance('QiniuRS');
	list($ret, $err) = $Instance->Move('cdnimg','test.png','cdnimg','test1.png');
	if ($err !== null) {
    	var_dump($err);
	} else {
    	var_dump($ret);
	}
	4、删除单个文件
	import('ORG.Cloud.QiniuSDK');
	$Instance = QiniuSDK::getInstance('QiniuRS');
	list($ret, $err) = $Instance->Delete('cdnimg','test.png');
	if ($err !== null) {
    	var_dump($err);
	} else {
    	var_dump($ret);
	}
##### 上传下载接口
	1、上传字符串
	import('ORG.Cloud.QiniuSDK');
	$key1 = "test.png";
    $Instance = QiniuSDK::getInstance('QiniuRSTransfer');
	$Instance->setScope('cdnimg');
	$upToken=$Instance->Token();
	list($ret, $err) = $Instance->Put($upToken, $key1, "Qiniu Storage!");
	if ($err !== null) {
    	var_dump($err);
	} else {
    	var_dump($ret);
	}
	2、上传本地文件
	import('ORG.Cloud.QiniuSDK');
	$key1 = "test.txt";
    $Instance = QiniuSDK::getInstance('QiniuRSTransfer');
	$upToken=$Instance->Token();
	list($ret, $err) = $Instance->PutFile($upToken, $key1, __file__, 1);
	if ($err !== null) {
    	var_dump($err);
	} else {
    	var_dump($ret);
	}
	3、 公有资源下载
	import('ORG.Cloud.QiniuSDK');
	$key = "test.png";
    $Instance = QiniuSDK::getInstance('QiniuRSTransfer');
	$baseurl=$Instance->MakeBaseUrl($key);
	4、私有资源下载
	import('ORG.Cloud.QiniuSDK');
	$key = "test.png";
	$Instance = QiniuSDK::getInstance('QiniuRSTransfer');
	$baseurl=$Instance->MakeBaseUrl($key);
	$privateurl=$Instance->MakePrivateUrl($baseurl);

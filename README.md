QiniuSDKForThinkPHP
===================

使用ThinkPHP封装的七牛云存储API
如何在ThinkPHP中使用
-------------------
### 放置位置
    将Cloud文件夹放在ThinkPHP\Extend\Library\ORG目录下，你也可以按需要放在其他地方。
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


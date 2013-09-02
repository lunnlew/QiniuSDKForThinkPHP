QiniuSDKForThinkPHP
===================

使用ThinkPHP封装的七牛云存储API
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
##### 图像处理接口
	1、生成缩略图
	import('ORG.Cloud.QiniuSDK');
	$key = "test.png";
	$Instance = QiniuSDK::getInstance('QiniuRSTransfer');
	$baseurl = $Instance->MakeBaseUrl($key);
	$Instance = QiniuSDK::getInstance('QiniuImageView');
	$params = array(
		'Mode'=>2,
		'Width'=>50,
		'Height'=>50,
		'Quality'=>70,
		'Format'=>'jpg'
	);
	echo $Instance->MakeRequest($baseurl,'imageView',$params);
	2、文字水印
	import('ORG.Cloud.QiniuSDK');
	$key = "test.png";
	$Instance = QiniuSDK::getInstance('QiniuRSTransfer');
	$baseurl = $Instance->MakeBaseUrl($key);
	$Instance = QiniuSDK::getInstance('QiniuImageView');
	$params = array(
		'WaterMode'=>2,
		'Text'=>'文字水印',
		'Font'=>'宋体',
		'Fill'=>'white',
		'Fontsize'=>1000,
		'Dissolve'=>85,
		'Gravity'=>'SouthEast',
		'Dx'=>10,
		'Dy'=>10
		);
	echo $Instance->MakeRequest($baseurl,'watermark',$params);
	3、图片水印
	import('ORG.Cloud.QiniuSDK');
	$key = "test.png";
	$Instance = QiniuSDK::getInstance('QiniuRSTransfer');
	$baseurl = $Instance->MakeBaseUrl($key);
	$Instance = QiniuSDK::getInstance('QiniuImageView');
	$params = array(
		'WaterMode'=>1,
		'Waterimageurl'=>'http://www.domain.com/water.png',
		'Dissolve'=>50,
		'Gravity'=>'SouthEast',
		'Dx'=>10,
		'Dy'=>10
		);
	echo $Instance->MakeRequest($baseurl,'watermark',$params);
#### 媒体资源接口
	1、音频转换
	import('ORG.Cloud.QiniuSDK');
	$key = "music.mp3";
	$Instance = QiniuSDK::getInstance('QiniuRSTransfer','cdnimg');
	$params = array(
		'Format'=>'wav',
		'AudioQuality'=>'3',
		'SamplingRate'=>'44100'
		);
	$baseurl = $Instance->MakeBaseUrl($key);
	$Instance = QiniuSDK::getInstance('QiniuAudioVisual');
	echo $Instance->MakeRequest($baseurl,'avthumb',$params);
	2、视频转换
	import('ORG.Cloud.QiniuSDK');
	$key = "test.mp4";
	$Instance = QiniuSDK::getInstance('QiniuRSTransfer','cdnimg');
	$params = array(
		'Format'=>'flv',
		'FrameRate'=>'30',
		'VideoBitRate'=>'256k',
		'Vcodec'=>'ibx264',
		'SamplingRate'=>'22050',
		'BitRate'=>'64k',
		'Acodec'=>'libmp3lame',
		);
	$baseurl = $Instance->MakeBaseUrl($key);
	$Instance = QiniuSDK::getInstance('QiniuAudioVisual');
	echo $Instance->MakeRequest($baseurl,'avthumb',$params);
	2、视频缩略图
	import('ORG.Cloud.QiniuSDK');
	$key = "test.mp4";
	$Instance = QiniuSDK::getInstance('QiniuRSTransfer','cdnimg');
	$params = array(
		'Format'=>'jpg',
		'Offset'=>'7',
		'Width'=>'480',
		'Height'=>'360',
		);
	$baseurl = $Instance->MakeBaseUrl($key);
	$Instance = QiniuSDK::getInstance('QiniuAudioVisual');
	echo $Instance->MakeRequest($baseurl,'vframe',$params);

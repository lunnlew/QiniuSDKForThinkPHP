<?php
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: LunnLew <lunnlew@gmail.com> 20130801
// +----------------------------------------------------------------------
// | Docs:http://docs.qiniu.com/php-sdk/v6/index.html#rs-api

/**
 * 资源管理类
 */
abstract class QiniuRSSDK extends QiniuSDK{
	/**
	 * 申请应用时分配的app_key
	 * @var string
	 */
	protected $AppKey = '';
	
	/**
	 * 申请应用时分配的 app_secret
	 * @var string
	 */
	protected $AppSecret = '';
	/**
	 * API根路径
	 * @var string
	 */
	protected $ApiBase = 'http://rs.qbox.me';
	public function __construct($param){
		parent::__construct();
	}
	/**
     * 取得API实例
     * @static
     * @return mixed 返回API
     */
    public static function getInstance($type, $param = null) {
    	$name = ucfirst(strtolower($type)) . 'SDK';
    	require_once "sdk/{$name}.class.php";
    	if (class_exists($name)) {
    		return new $name($param);
    	} else {
    		halt(L('_CLASS_NOT_EXIST_') . ':' . $name);
    	}
    }
	/**
	 * 资源状态
	 * @param string $bucket 空间名
	 * @param string $key    文件名
	 */
	public function Stat($bucket, $key){
		$uri = self::get_RS_URIStat($bucket, $key);
		return parent::call($this->ApiBase . $uri);
	}
	/**
	 * 资源移动
	 * @param string $bucket  空间桶名
	 * @param string $key     文件名
	 * @param string $bucket1 空间桶名
	 * @param string $key1    文件名
	 */
	public function Move($bucket, $key, $bucket1, $key1){
		$uri = self::get_RS_URIMove($bucket, $key, $bucket1, $key1);
		parent::callNoRet($this->ApiBase . $uri);
	}
	/**
	 * 资源复制
	 * @param string $bucket  空间桶名
	 * @param string $key     文件名
	 * @param string $bucket1 空间桶名
	 * @param string $key1    文件名
	 */
	public function Copy($bucket, $key, $bucket1, $key1){
		$uri = self::get_RS_URICopy($bucket, $key, $bucket1, $key1);
		parent::callNoRet($this->ApiBase . $uri);
	}
	/**
	 * 资源删除
	 * @param string $bucket 空间名
	 * @param string $key    文件名
	 */
	public function Delete($bucket, $key){
		$uri = self::get_RS_URIDelete($bucket, $key);
		parent::callNoRet($this->ApiBase . $uri);
	}
	/**
	 * 组合操作地址
	 * @param  string $bucket  空间桶名
	 * @param  string $key     文件名
	 * @param  string $bucket1 空间桶名
	 * @param  string $key1    文件名
	 * @return string          uri
	 */
	public function get_RS_URIMove($bucket, $key, $bucket1, $key1){
		return '/move/' . parent::SafeBaseEncode("$bucket:$key") . '/' . parent::SafeBaseEncode("$bucket1:$key1");
	}
	public function get_RS_URIStat($bucket, $key){
		return '/stat/' . parent::SafeBaseEncode("$bucket:$key");
	}

	public function get_RS_URIDelete($bucket, $key){
		return '/delete/' . parent::SafeBaseEncode("$bucket:$key");
	}

	public function get_RS_URICopy($bucketSrc, $keySrc, $bucketDest, $keyDest){
		return '/copy/' . parent::SafeBaseEncode("$bucketSrc:$keySrc") . '/' . parent::SafeBaseEncode("$bucketDest:$keyDest");
	}
}
?>
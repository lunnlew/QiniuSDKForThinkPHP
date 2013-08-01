<?php
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: LunnLew <lunnlew@gmail.com> 20130801
// +----------------------------------------------------------------------
// | Docs:http://docs.qiniu.com/php-sdk/v6/index.html#get-and-put-api

/**
 * 资源传输类
 */
class QiniuRSTransferSDK extends QiniuSDK{
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
	protected $ApiBase = 'http://up.qiniu.com';
	//上传策略参数
	public $Scope;//客户端的权限
	public $CallbackUrl;//回调地址
	public $CallbackBody;//回调信息
	public $ReturnUrl;//跳转地址
	public $ReturnBody;//跳转信息
	public $AsyncOps;//可指定上传完成后，需要自动执行哪些数据处理
	public $EndUser;
	public $Expires;//默认是 3600 秒
	//校验参数
	public $Params = null;
	public $MimeType = null;
	public $Crc32 = 0;
	public $CheckCrc = 0;
	//资源下载域名
	public $downDomain = '';
	public function __construct($scope){
		$this->Scope = $scope;
		parent::__construct();
	}
	public function setScope($scope){
		$this->Scope = $scope;
	}
	/**
	 * 授权凭证
	 */
	public function Token($mac=null){
		$deadline = $this->Expires;
		if ($deadline == 0) {
			$deadline = 3600;
		}
		$deadline += time();
		if(empty($this->Scope)){
			exit('error:scope is null');
		}
		$policy = array('scope' => $this->Scope, 'deadline' => $deadline);
		if (!empty($this->CallbackUrl)) {
			$policy['callbackUrl'] = $this->CallbackUrl;
		}
		if (!empty($this->CallbackBody)) {
			$policy['callbackBody'] = $this->CallbackBody;
		}
		if (!empty($this->ReturnUrl)) {
			$policy['returnUrl'] = $this->ReturnUrl;
		}
		if (!empty($this->ReturnBody)) {
			$policy['returnBody'] = $this->ReturnBody;
		}
		if (!empty($this->AsyncOps)) {
			$policy['asyncOps'] = $this->AsyncOps;
		}
		if (!empty($this->EndUser)) {
			$policy['endUser'] = $this->EndUser;
		}

		$b = json_encode($policy);
		return parent::SignWithData($b);
	}
	/**
	 * 传输字符文本
	 * @param string $upToken 凭证
	 * @param string $key     文件名
	 * @param string $body    文本
	 */
	public function Put($upToken, $key, $body){
		$fields = array('token' => $upToken);
		if ($key === null) {
			$fname = '?';
		} else {
			$fname = $key;
			$fields['key'] = $key;
		}
		if ($this->CheckCrc) {
			$fields['crc32'] = $this->Crc32;
		}
		$files = array(array('file', $fname, $body));
		return parent::CallWithMultipartForm($this->ApiBase, $fields, $files);
	}
	/**
	 * 文件上传
	 * @param string $upToken   凭证
	 * @param string $key       文件名
	 * @param string $localFile 文件名
	 * @param int $CheckCrc  是否计算crc
	 */
	public function PutFile($upToken, $key, $localFile, $CheckCrc){
		$this->CheckCrc = $CheckCrc;
		$fields = array('token' => $upToken, 'file' => '@' . $localFile);
		if ($key === null) {
			$fname = '?';
		} else {
			$fname = $key;
			$fields['key'] = $key;
		}
		if ($this->CheckCrc) {
			if ($this->CheckCrc === 1) {
				$hash = hash_file('crc32b', $localFile);
				$array = unpack('N', pack('H*', $hash));
				$this->Crc32 = $array[1];
			}
			$fields['crc32'] = sprintf('%u', $this->Crc32);
		}
		return parent::CallWithForm($this->ApiBase, $fields,'multipart/form-data');
	}
}
?>
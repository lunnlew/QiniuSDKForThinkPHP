<?php
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: LunnLew <lunnlew@gmail.com> 20130801
// +----------------------------------------------------------------------
// | Docs:http://docs.qiniu.com/php-sdk/v6/index.html

/**
 * 七牛API封装基类 <www.qiniu.com>
 */
abstract class QiniuSDK{
	/**
	 * SDK版本
	 */
	private $SDK_Version='1.0';
	private $SDK_Release='20130801';
	/**
	 * API版本
	 * @var string
	 */
	protected $Version = '6.1.1';
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
	protected $ApiBase = '';
	/**
	 * 调用接口类型
	 * @var string
	 */
	private $Type = '';
	//请求信息
	public $URL;
	public $rqHeader;
	public $rqBody;
	//错误信息
	public $Err;	 // string
	public $Reqid;	 // string
	public $Details; // []string
	public $Code;	 // int
	//响应信息
	public $StatusCode;
	public $rpHeader;
	public $ContentLength;
	public $rpBody;
	//上传下载域
	public $downDomain;
	/**
	 * 构造方法，配置应用信息
	 */
	public function __construct(){
		//设置SDK类型
		$class = get_class($this);
		$this->Type = strtoupper(substr($class, 0, strlen($class)-3));
		//获取应用配置
		$config = C("THINK_SDK_QINIU");
		if(empty($config['APP_KEY']) || empty($config['APP_SECRET'])){
			throw new Exception('请配置您申请的APP_KEY和APP_SECRET');
		} else {
			$this->AppKey    = $config['APP_KEY'];
			$this->AppSecret = $config['APP_SECRET'];
			$this->downDomain = $config['DOWN_DOMAIN'];
		}
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
     * URLSafeBase64Encode
     * @param string str 安全编码串
     */
    public function SafeBaseEncode($str){
	$find = array('+', '/');
	$replace = array('-', '_');
	return str_replace($find, $replace, base64_encode($str));
	}
	/**
	 * 请求执行
	 * @param  string $url 完整url
	 * @return object 错误信息对象
	 */
	public function callNoRet($url){
		$u = array('path' => $url);
		self::request($u, null);
		self::RoundTrip();
		self::setResponse();
	}
	public function call($url){
		$u = array('path' => $url);
		self::request($u, null);
		self::RoundTrip();
		return self::getReponseRet();
	}
	public function CallWithMultipartForm($url, $fields, $files){
		list($contentType, $body) = self::buildMultipartForm($fields, $files);
		return self::CallWithForm($url, $body, $contentType);
		}
	/**
	 * 组装请求参数
	 * @param  array $fields 字段数组
	 * @param  array $files  文件数组
	 * @return array         组装结果
	 */
	public function buildMultipartForm($fields, $files){
		$data = array();
		$mimeBoundary = md5(microtime());
		foreach ($fields as $name => $val) {
			array_push($data, '--' . $mimeBoundary);
			array_push($data, "Content-Disposition: form-data; name=\"$name\"");
			array_push($data, '');
			array_push($data, $val);
		}
		foreach ($files as $file) {
			array_push($data, '--' . $mimeBoundary);
			list($name, $fileName, $fileBody) = $file;
			$fileName = self::escapeQuotes($fileName);
			array_push($data, "Content-Disposition: form-data; name=\"$name\"; filename=\"$fileName\"");
			array_push($data, 'Content-Type: application/octet-stream');
			array_push($data, '');
			array_push($data, $fileBody);
		}

		array_push($data, '--' . $mimeBoundary . '--');
		array_push($data, '');
		$body = implode("\r\n", $data);
		$contentType = 'multipart/form-data; boundary=' . $mimeBoundary;
		return array($contentType, $body);
	}
	/**
	 * 数据提交过程
	 * @param string $url         操作路径
	 * @param array $params      数据参数
	 * @param string $contentType 文本内容
	 */
	function CallWithForm($url, $params, $contentType = 'application/x-www-form-urlencoded'){
		$u = array('path' => $url);
		if ($contentType === 'application/x-www-form-urlencoded') {
			if (is_array($params)) {
				$params = http_build_query($params);
			}
		}
		self::request($u, $params);
		if ($contentType !== 'multipart/form-data') {
			$this->rqHeader['Content-Type'] = $contentType;
		}
		self::RoundTrip();
		return self::getReponseRet();
	}
	/**
	 * 替换处理
	 * @param  string $str 需处理串
	 * @return string      处理结果
	 */
	function escapeQuotes($str){
		$find = array("\\", "\"");
		$replace = array("\\\\", "\\\"");
		return str_replace($find, $replace, $str);
	}
	/**
	 * 设置请求参数
	 * @param  string $url  URL
	 * @param  string $body 头body内容
	 */
	public function request($url, $body){
		$this->URL = $url;
		$this->rqHeader = array();
		$this->rqBody = $body;
	}
	/**
	 * 执行请求
	 */
	public  function doCall(){
		$ch = curl_init();
		$url = $this->URL;
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_URL => $url['path']
		);
		$httpHeader = $this->rqHeader;
		if (!empty($httpHeader))
		{
			$header = array();
			foreach($httpHeader as $key => $parsedUrlValue) {
				$header[] = "$key: $parsedUrlValue";
			}
			$options[CURLOPT_HTTPHEADER] = $header;
		}
		$body = $this->rqBody;
		if (!empty($body)) {
			$options[CURLOPT_POSTFIELDS] = $body;
		}
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		$ret = curl_errno($ch);
		if ($ret !== 0) {
			self::error(0, curl_error($ch));
			curl_close($ch);
		}
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);
		self::response($code, $result);
		$this->rpHeader['Content-Type'] = $contentType;
	}
	/**
	 * 设置错误信息
	 * @param  int $code 错误码
	 * @param  string $err  错误信息
	 */
	public function error($code, $err){
		$this->Code = $code;
		$this->Err = $err;
	}
	/**
	 * 设置响应信息
	 * @param  int $code 响应码
	 * @param  string $body 响应文本
	 */
	public function response($code,$body){
		$this->StatusCode = $code;
		$this->rpHeader = array();
		$this->rpBody = $body;
		$this->ContentLength = strlen($body);
	}
	/**
	 * 判断是否有body或者设置了application/x-www-form-urlencoded头
	 * @return bool
	 */
	public function incBody(){
		$body = $this->rpBody;
		if (!isset($body)) {
			return false;
		}

		$ct = self::getHeaderType($this->rpHeader, 'Content-Type');
		if ($ct === 'application/x-www-form-urlencoded') {
			return true;
		}
		return false;
	}
	/**
	 * 获得头类型值
	 * @param  array $header 
	 * @param  string $key 
	 */
	public function getHeaderType($header, $key){
		$val = @$header[$key];
		if (isset($val)) {
			if (is_array($val)) {
				return $val[0];
			}
			return $val;
		} else {
			return '';
		}
	}
	/**
	 * 请求过程
	 */
	public function RoundTrip(){
		$incbody = self::incBody();
		$token = self::SignRequest($incbody);
		$this->rqHeader['Authorization'] = "QBox $token";
		return self::doCall();
	}
	/**
	 * 请求签名
	 * @param int $incbody
	 */
	public function SignRequest($incbody)
	{
		$url = $this->URL;
		$url = parse_url($url['path']);
		$data = '';
		if (isset($url['path'])) {
			$data = $url['path'];
		}
		if (isset($url['query'])) {
			$data .= '?' . $url['query'];
		}
		$data .= "\n";

		if ($incbody) {
			$data .= $this->Body;
		}
		return $this->Sign($data);
	}
	/**
	 * 数据签名
	 * @param array $data 需签名数据
	 */
	public function Sign($data){
		$sign = hash_hmac('sha1', $data, $this->AppSecret, true);
		return $this->AppKey . ':' . self::SafeBaseEncode($sign);
	}
	public function SignWithData($data){
		$data = self::SafeBaseEncode($data);
		return $this->Sign($data) . ':' . $data;
	}
	/**
	 * 设置相应错误信息
	 */
	public function setResponse(){
		$header = $this->rpHeader;
		$details = self::getHeaderType($header, 'X-Log');
		$reqId = self::getHeaderType($header, 'X-Reqid');
		self::error($this->StatusCode, null);
		if ($this->Code > 299) {
			if ($this->ContentLength !== 0) {
				if (self::getHeaderType($header, 'Content-Type') === 'application/json') {
					$ret = json_decode($this->rpBody, true);
					$this->Err = $ret['error'];
				}
			}
		}
	}
	/**
	 * 获得响应结果信息
	 * @return object
	 */
	public function getReponseErr(){
		$data['Err']=$this->Err;	 // string
		$data['Reqid']=$this->Reqid;	 // string
		$data['Details']=$this->Details; // []string
		$data['Code']=$this->Code;	 // int
		return (object)($data);
	}
	public function getReponseRet(){
		$code = $this->StatusCode;
		$data = null;
		if ($code >= 200 && $code <= 299) {
			if ($this->ContentLength !== 0) {
				$data = json_decode($this->rpBody, true);
				if ($data === null) {
					self::error(0, 'json decode null');
				}
			}
			if ($code === 200) {
				return array($data, null);
			}
		}
		return array($data, self::getReponseErr());
	}

	/**
	 * 生成地址
	 * @param srting $key    文件名
	 * @param string $domain 下载域
	 */
	public function MakeBaseUrl($key,$domain=''){
		if($domain!=''){
			$this->downDomain = $domain;
		}
		$keyEsc = rawurlencode($key);
		return "http://$this->downDomain/$keyEsc";
	}
	/**
	 * 生成私有地址
	 * @param srting $baseUrl 基础URL
	 */
	public function MakePrivateUrl($baseUrl){
		$deadline = $this->Expires;
		if ($deadline == 0) {
			$deadline = 3600;
		}
		$deadline += time();

		$pos = strpos($baseUrl, '?');
		if ($pos !== false) {
			$baseUrl .= '&e=';
		} else {
			$baseUrl .= '?e=';
		}
		$baseUrl .= $deadline;
		$token = self::Sign($baseUrl);
		return "$baseUrl&token=$token";
	}

}
?>
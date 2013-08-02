<?php
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: LunnLew <lunnlew@gmail.com> 20130802
// +----------------------------------------------------------------------
// | Docs:http://docs.qiniu.com/api/v6/audio-video-hls-process.html

/**
 * 媒体资源类
 */
class QiniuAudioVisualSDK extends QiniuSDK{
	/**
	 * 申请应用时分配的app_key
	 * @param string
	 */
	protected $AppKey = '';
	
	/**
	 * 申请应用时分配的 app_secret
	 * @param string
	 */
	protected $AppSecret = '';
	/**
	 * API根路径
	 * @param string
	 */
	protected $ApiBase = '';
	//上传下载域
	public $DownDomain;
	public $Map;
	//应用参数
	public $HLS;
	//媒体格式
	public $Format;
	//静态码率
	public $BitRate;
	//动态码率
	public $AudioQuality;
	//音频采样频率
	public $SamplingRate;
	//视频帧率
	public $FrameRate;
	//视频比特率
	public $VideoBitRate;
	//视频编码方案
	public $Vcodec;
	//音频编码方案
	public $Acodec;
	//取视频的第几秒
	public $Offset;
	//缩略图宽度
	public $Width;
	//缩略图高度
	public $Height;
	//预设集
	public $Preset;
	public function __construct($params=''){
		parent::__construct();
	}
	/**
	 * 设置请求参数
	 * @param array $params 请求参数数组
	 */
	public function setRequestParams($params){
		foreach ($params as $key => $value) {
			if(array_key_exists($key, $this->Map)){
				$this->Map[$key] = $value;
			}else{
				$this->$key = $value;
			}
		}
	}
	/**
	 * 生成请求
	 * @param string $url  请求地址
	 * @param strng $type 请求类型
	 */
	public function MakeRequest($url,$type='avthumb',$params=''){
		if($params!=''){
			self::setRequestParams($params);
		}
		$func = '_make_'.$type.'_Request';
		return self::$func($url);
	}
	protected function _make_avthumb_Request($url){
    	if (!empty($this->Format)) {
    		$ops[] = $this->Format;
    	}
    	//HLS自定义
    	if (!empty($this->HLS)) {
    		$ops[] = 'm3u8/segtime';
    		//HLS自定义必须
    		if (!empty($this->SegSeconds)) {
    		$ops[] = $this->SegSeconds;
    		}
    		//HLS预设必须
    		if (!empty($this->Preset)) {
    		$ops[] = 'preset/'.$this->Preset;
    		}
    	}
    	//视频
    	if (!empty($this->FrameRate)) {
    		$ops[] = 'r/' . $this->FrameRate;
    	}
    	if (!empty($this->VideoBitRate)) {
    		$ops[] = 'vb/' . $this->VideoBitRate;
    	}
    	if (!empty($this->Vcodec)) {
    		$ops[] = 'vcodec/' . $this->Vcodec;
    	}
    	if (!empty($this->Acodec)) {
    		$ops[] = 'acodec/' . $this->Acodec;
    	}
    	///
    	if (!empty($this->BitRate)) {
    		$ops[] = 'ab/' . $this->BitRate;
    	}
    	if (!empty($this->AudioQuality)) {
    		$ops[] = 'aq/' . $this->AudioQuality;
    	}
    	if (!empty($this->AudioQuality)) {
    		$ops[] = 'ar/' . $this->SamplingRate;
    	}
		return $url . "?avthumb/". implode('/', $ops);
	}
	protected function _make_vframe_Request($url){
		if (!empty($this->Format)) {
    		$ops[] = $this->Format;
    	}
    	if (!empty($this->Offset)) {
    		$ops[] = 'offset/' . $this->Offsete;
    	}
    	if (!empty($this->Width)) {
    		$ops[] = 'w/' . $this->Width;
    	}
    	if (!empty($this->Height)) {
    		$ops[] = 'h/' . $this->Height;
    	}
    	return $url . "?vframe/". implode('/', $ops);
	}
}
?>
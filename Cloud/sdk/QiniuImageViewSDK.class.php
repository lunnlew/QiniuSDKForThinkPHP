<?php
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: LunnLew <lunnlew@gmail.com> 20130801
// +----------------------------------------------------------------------
// | Docs:http://docs.qiniu.com/api/v6/image-process.html

/**
 * 图片资源类
 */
class QiniuImageViewSDK extends QiniuSDK{
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
	//字段映射
	public $map = array();
	//应用参数
	//图像缩略处理的模式
	public $Mode;
	//指定目标缩略图的宽度，单位：像素
    public $Width;
    //指定目标缩略图的高度，单位：像素
    public $Height;
    //指定目标缩略图的图像质量
    public $Quality;
    //指定目标缩略图的输出格式
    public $Format;
    //自动旋正
    public $Autoorient = 1;
    //缩略图大小
    public $Thumbnail;
    //位置偏移，只会使裁剪偏移受到影响
    public $Gravity;
    //裁剪大小和偏移
    public $Crop;
    //旋转角度
    public $Rotate;
    //水印模式
    public $WaterMode=1;
    //水印图片url
    public $Waterimageurl;
    //透明度
    public $Dissolve=100;
    //横向边距
    public $Dx=10;
    //纵向边距
    public $Dy=10;
    //水印文本
    public $Text;
    //字体名
    public $Font;
    //字体大小
    public $Fontsize;
    //字体颜色
    public $Fill;
	public function __construct($params=''){
		parent::__construct();
	}
	/**
	 * 设置请求参数
	 * @param array $params 请求参数数组
	 */
	public function setRequestParams($params){
		foreach ($params as $key => $value) {
			if(array_key_exists($key, $map)){
				$this->$map[$key] = $value;
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
	public function MakeRequest($url,$type='imageInfo',$params=''){
		if($params!=''){
			self::setRequestParams($params);
		}
		$func = '_make_'.$type.'_Request';
		return self::$func($url);
	}
	//基础功能
	protected function _make_imageInfo_Request($url){
		return $url . "?imageInfo";
	}
	protected function _make_exif_Request($url){
		return $url . "?exif";
	}
	protected function _make_imageView_Request($url){
		$ops = array($this->Mode);
    	if (!empty($this->Width)) {
    		$ops[] = 'w/' . $this->Width;
    	}
    	if (!empty($this->Height)) {
    		$ops[] = 'h/' . $this->Height;
    	}
    	if (!empty($this->Quality)) {
    		$ops[] = 'q/' . $this->Quality;
    	}
    	if (!empty($this->Format)) {
    		$ops[] = 'format/' . $this->Format;
    	}
		return $url . "?imageView/" . implode('/', $ops);
	}
	//高级功能,暂未处理 ‘链式处理’
	protected function _make_imageMogr_Request($url){
		$ops = array();
		if ($this->Autoorient) {
    		$ops[] = 'auto-orient';
    	}
    	if (!empty($this->Thumbnail)) {
    		$this->Thumbnail = '!'.str_replace(array('%','^'), array('p','r'), $this->Thumbnail);
    		$ops[] = 'thumbnail/' . $this->Thumbnail;
    	}
    	if (!empty($this->Gravity)) {
    		$ops[] = 'gravity/' . $this->Gravity;
    	}
    	if (!empty($this->Crop)) {
    		$this->Crop = '!'.str_replace(array('+'), array('a'), $this->Crop);
    		$ops[] = 'crop/' . $this->Crop;
    	}
    	if (!empty($this->Quality)) {
    		$ops[] = 'quality/' . $this->Quality;
    	}
    	if (!empty($this->Rotate)) {
    		$ops[] = 'rotate/' . $this->Rotate;
    	}
    	if (!empty($this->Format)) {
    		$ops[] = 'format/' . $this->Format;
    	}
		return $url . "?imageMogr/v2/".implode('/', $ops);
	}
	//建议在上传图片后进行图片水印预转
	protected function _make_watermark_Request($url){
		$ops = array();
    	if (!empty($this->WaterMode)) {
    		$ops[] = $this->WaterMode;
    		if($this->WaterMode==1){
    			if (!empty($this->Waterimageurl)){
		    		$ops[] = 'image/' . parent::SafeBaseEncode($this->Waterimageurl);
		    	}
    		}
    		if($this->WaterMode==2){
    			if (!empty($this->Text)) {
		    		$ops[] = 'text/' . parent::SafeBaseEncode($this->Text);
		    	}
		    	if (!empty($this->Font)) {

		    		if(preg_match("/[^a-zA-Z]/", $this->Font)){
		    			$ops[] = 'font/' . parent::SafeBaseEncode($this->Font);
		    		}else{
		    			$ops[] = 'font/' . $this->Font;
		    		}
		    		
		    	}
		    	if (!empty($this->Fontsize)) {
		    		$ops[] = 'fontsize/' . $this->Fontsize;
		    	}
		    	if (!empty($this->Fill)) {
		    		$ops[] = 'fill/' . parent::SafeBaseEncode($this->Fill);
		    	}
    		}


    		if (!empty($this->Dissolv)) {
	    		$ops[] = 'dissolve/' . $this->Dissolve;
	    	}
	    	if (!empty($this->Gravity)) {
	    		$ops[] = 'gravity/' . $this->Gravity;
	    	}
	    	if (!empty($this->Dx)) {
	    		$ops[] = 'dx/' . $this->Dx;
	    	}
	    	if (!empty($this->Dy)) {
	    		$ops[] = 'dy/' . $this->Dy;
	    	}

    	}
		return $url . "?watermark/". implode('/', $ops);
	}

}
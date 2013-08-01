<?php
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: LunnLew <lunnlew@gmail.com> 20130801
// +----------------------------------------------------------------------
// | Docs:http://docs.qiniu.com/php-sdk/v6/index.html#fop-image

/**
 * 图片资源类
 */
class QiniuImageViewSDK extends QiniuSDK{
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

	//应用参数
	public $Mode;
    public $Width;
    public $Height;
    public $Quality;
    public $Format;
    //上传下载域
	public $downDomain;
	
	public function __construct($param){
		parent::__construct();
	}
	/**
	 * 生成请求
	 * @param string $url  请求地址
	 * @param strng $type 请求类型
	 */
	public function MakeRequest($url,$type='imageInfo'){
		$URL='';
		if($type=='imageInfo')
			$URL= $url . "?imageInfo";
		if($type=='exif')
			$URL= $url . "?exif";
		if($type=='imageView'){
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
	    	$URL=$url . "?imageView/" . implode('/', $ops);
		}
		return $URL;
	}

}
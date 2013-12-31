<?php
/**
 * @abstract 生成图片的缩略图，可以指定任意尺寸，生成的图片为png格式
 * @example
 * $file = 'test.png';
 * $th =new Thumbnail();
 * $th->GenerateThumbnail($file, 400, 500);
 *
 */
class Thumbnail{
     /**
     * @var string $from 源图片
     */
     private $from;
     /**
     * @var string $name 缩略图的文件名
     */
     private $name = '';
     /**
     * @var 原图宽
     */
     private $rWidth;
     /**
     * @var 原图高
     */
     private $rHeight;
     /**
     * @var 缩略图宽
     */
     private $tWidth;
     /**
     * @var 缩略图高
     */
     private $tHeight;
     /**
     * @var 实际缩放到的宽度
     */
     private $width;
     /**
     * @var 实际缩放到的高度
     */
     private $height;

     public function __construct(){
         try{
             if(!function_exists('gd_info')){
                 throw new Exception('Must GD extension is enabled');
             }
         }
         catch(Exception $e){
             $msg = 'class ' . __CLASS__ . ' Error:' . $e->getMessage();
             echo $msg;
             exit;
         }
     }
     /**
     * @var $from 原图像
     * @var $width 生成的缩略图的宽
     * @var $height 生成缩略图的高
     * @var $name 生成的缩略图的文件名，不带后缀
     * @return string 生成的缩略图
     */
     public function GenerateThumbnail($from, $width, $height, $name=''){
         try{
             if(!file_exists($from)){
                 throw new Exception('File does not exist');
             }
             if($width <= 0){
                 throw new Exception('The width is invalid');
             }
             if($height <= 0){
                 throw new Exception('The height is invalid');
             }
             $this->from = $from;
             $this->tWidth = $width;
             $this->tHeight = $height;
             if(!empty($name)){
                 $this->name = $name;
             }
             else{
                 $this->name = date('Ymd') . mt_rand(0, 9999);
             }
             $this->createThumbnail();
         }
         catch(Exception $e){
             $msg = 'class ' . __CLASS__ . ' Error:' . $e->getMessage();
             echo $msg;
             exit;
         }
     }

     public function getThumbnail(){
         return $this->name;
     }

     /**
     * 生成缩略图文件
     */
     private function createThumbnail(){
         try{
             //读取原始图像信息
             $sourceInfo = getimagesize($this->from);
             $this->rWidth = $sourceInfo[0];
             $this->rHeight = $sourceInfo[1];
             //创建缩略图图像资源句柄
             $new_pic = imagecreatetruecolor($this->tWidth, $this->tHeight);
             //原图绘制到缩略图的x、y坐标
             $x = 0;
             $y = 0;
             //创建原始图像资源句柄
             $source_pic = '';
             switch ($sourceInfo[2]){
                 case 1: $source_pic = imagecreatefromgif($this->from); //gif
                         break;
                 case 2: $source_pic = imagecreatefromjpeg($this->from); //jpg
                         break;
                 case 3: $source_pic = imagecreatefrompng($this->from); //png
                         break;
                 default: throw new Exception('Does not support this type of image');
             }
             //计算缩放后图像实际大小
             //原图宽高均比缩略图大
             if($this->rWidth > $this->tWidth && $this->rHeight > $this->tHeight){
                 $midw = ($this->rWidth - $this->tWidth) / $this->rWidth; //宽缩小的比例
                 $midh = ($this->rHeight - $this->tHeight) / $this->rHeight; //高缩小的比例
                 //那个缩小的比例大以那个为准
                 if($midw > $midh){
                     $this->width = $this->tWidth;
                     $this->height = $this->rHeight - floor($this->rHeight * $midw);
                     $y = ($this->tHeight - $this->height) / 2;
                 }
                 else{
                     $this->width = $this->rWidth - floor($this->rWidth * $midh);
                     $this->height = $this->tHeight;
                     $x = ($this->tWidth - $this->width) / 2;
                 }
             }
             //原图宽高均比缩略图小
             elseif($this->rWidth < $this->tWidth && $this->rHeight < $this->tHeight){
                 $midw = ($this->tWidth - $this->rWidth) / $this->rWidth; //宽放大的比例
                 $midh = ($this->tHeight - $this->rHeight) / $this->rHeight; //高放大的比例
                 //那个放大的比例小以那个为准
                 if($midw < $midh){
                     $this->width = $this->tWidth;
                     $this->height = $this->rHeight + floor($this->rHeight * $midw);
                     $y = ($this->tHeight - $this->height) / 2;
                 }
                 else{
                     $this->width = $this->rWidth + floor($this->rWidth * $midh);
                     $this->height = $this->tHeight;
                     $x = ($this->tWidth - $this->width) / 2;
                 }
             }
             //原图宽小于缩略图宽，原图高大于缩略图高
             elseif($this->rWidth < $this->tWidth && $this->rHeight > $this->tHeight){
                 $mid = ($this->rHeight - $this->tHeight) / $this->rHeight; //高缩小的比例
                 $this->width = $this->rWidth - floor($this->rWidth * $mid);
                 $this->height = $this->rHeight - floor($this->rHeight * $mid);
                 $x = ($this->tWidth - $this->width) / 2;
                 $y = ($this->tHeight - $this->height) / 2;
             }
             //原图宽大于缩略图宽，原图高小于缩略图高
             elseif($this->rWidth > $this->tWidth && $this->rHeight < $this->tHeight){
                 $mid = ($this->rWidth - $this->tWidth) / $this->rWidth; //宽缩小的比例
                 $this->width = $this->rWidth - floor($this->rWidth * $mid);
                 $this->height = $this->rHeight - floor($this->rHeight * $mid);
                 $x = ($this->tWidth - $this->width) / 2;
                 $y = ($this->tHeight - $this->height) / 2;
             }
             else{
                 throw new Exception('Resize error');
             }

             //给缩略图添加白色背景
             $bg = imagecolorallocate($new_pic, 255, 255, 255);
             imagefill($new_pic, 0, 0, $bg);
             //缩小原始图片到新建图片
             imagecopyresampled($new_pic, $source_pic, $x, $y, 0, 0, $this->width, $this->height, $this->rWidth, $this->rHeight);
             //输出缩略图到文件
             imagepng($new_pic, $this->name.'.png');
             imagedestroy($new_pic);
             imagedestroy($source_pic);
         }
         catch(Exception $e){
             $msg = 'class ' . __CLASS__ . ' Error:' . $e->getMessage();
             echo $msg;
             exit;
         }
     }
}

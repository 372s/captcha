<?php

namespace qous\captcha;

define('FONT_PATH', dirname(__FILE__). './../fonts/');

class Captcha
{
    protected $img;

    protected $width = 100;
    protected $height = 30;
    
    protected $bgcolor = array(255, 255, 255);
    protected $red = 255;
    protected $green = 255;
    protected $blue = 255;

    protected $codeLength = 4;
    protected $code;

    protected $fontColors = [];
    protected $fonts = [
        'ABeeZee_regular.ttf',
        'Asap_700.ttf',
        'Khand_500.ttf',
        'Open_Sans_regular.ttf',
        'Roboto_regular.ttf', 
        'Ubuntu_regular.ttf'
    ];
    protected $font;
    protected $fontSize = 15;
    protected $fontPath = FONT_PATH;

    protected $dotted = 50;
    protected $angle = 15;
    protected $lines = 3;

    protected $characters = '1234567890abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ';

    public function __construct($width = 0, $height = 0, $characters = '') {
        if ($width) $this->width = $width;
        if ($height) $this->height = $height;
        if ($characters) $this->characters = $characters;
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $font = array_rand($this->fonts, 1);
        $this->font = FONT_PATH . $this->fonts[$font];
    }

    /**
     * 图片背景
     */
    protected function createBgColor($color = array()) {
        // 图片背景颜色
        $white = imagecolorallocate($this->img, $this->bgcolor[0], $this->bgcolor[1], $this->bgcolor[2]);
        imagefill($this->img, 0, 0, $white);
    }

    /**
     * 设置图片背景
     */
    protected function setBgColor($color = array()) {
        if ($color && is_array($color)) {
            $this->bgcolor = $color;
        }
    }


    /**
     * 生成随机验证码
     */
    public function createCode() {
        $sub = str_shuffle($this->characters);
        $this->code = substr($sub, 0, $this->codeLength);
        return $this->code;
    }

    /**
     * 设置随机文字的位置和大小
     */
    public function setCodeAttributes($codeLength = 4, $fontSize = 20) {
        $this->codeLength = $codeLength;
        $this->fontSize = $fontSize;
    }

    /**
     * 生成图片文字
     */
    protected function createFont() {
        $size = $this->fontSize;
        $_x = ($this->width / $this->codeLength);
        $y = $this->height / 1.5;
        for ($i=0; $i < $this->codeLength; $i++) {
            $angle = mt_rand(-20, 20);
            $fontcolor = imagecolorallocate($this->img, mt_rand(0,156), mt_rand(0,156), mt_rand(0,156));
            imagettftext($this->img, $size, $angle, $_x*$i+5, $y, $fontcolor, $this->font, $this->code[$i]);
        }
        // $black = imagecolorallocate($this->img, 0, 0, 0);
        // imagestring($this->img, 5, mt_rand(1, ($this->width)/2), mt_rand(1, ($this->height)/2), $this->code, $black);
    }

    /**
     * 设置干扰雪花数量
     */
    public function setDottedNumber($dotted = 0) {
        if ($dotted) $this->dotted = $dotted;
    }

    /**
     * 干扰雪花
     */
    protected function createDotted() {
        $red = imagecolorallocate($this->img, 255, 0, 0);
        $green = imagecolorallocate($this->img, 0, 255, 0);
        
        for($i = 0; $i< $this->dotted; $i++) {
            // 画一个单一像素，语法: bool imagesetpixel ( resource $image , int $x , int $y , int $color )
            imagesetpixel($this->img, rand(0, $this->width) , rand(0, $this->height) , $red);
            imagesetpixel($this->img, rand(0, $this->width) , rand(0, $this->height) , $green);
        }
    }

    /**
     * 设置干扰线条数
     */
    public function setLinesNumber($lines = 0) {
        if ($lines) $this->lines = $lines;
    }

    /**
     * 干扰线
     */
    protected function createLines() {
        for ($i=0; $i < $this->lines; $i++) {   
            //干扰线的颜色  
            $linecolor=imagecolorallocate($this->img, rand(80, 220), rand(80, 220), rand(80, 220));  
            //画出每条干扰线  
            imageline($this->img, rand(1, 100), rand(1, 30), rand(1, 100), rand(1, 30), $linecolor);  
        }
    }

    /**
     * 生成图片
     */
    protected function outPut() {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     * 生成图片验证码
     * @param $createDotted 是否生成雪花干扰
     * @param $createLines 是否生成干扰线
     */
    public function create($createDotted = true, $createLines = true) {
        $this->createBgColor();  
        $this->createCode();
        $this->createFont();     
        if ($createDotted) $this->createDotted();
        if ($createLines) $this->createLines();
        $this->outPut();
    }

    public function getCode() {
        return $this->code;
    }
}
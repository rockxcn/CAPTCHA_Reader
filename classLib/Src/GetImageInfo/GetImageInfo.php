<?php
/**
 * Created by PhpStorm.
 * User: kurisu
 * Date: 2017/11/12
 * Time: 23:47
 */

namespace CAPTCHA_Reader\GetImageInfo;

class GetImageInfo implements GetImageInfoInterFace
{
    use GetImageInfoTrait;

    private $config;
    private $mode;
    private $path;
    private $localImgNumber;
    private $savePath;
    private $deleteImageFile;

    private $fetchImagePath;
    private $imageInfo;

    /**
     * GetImageInfo constructor.
     * @param array $config
     * @param string $url
     */
    public function __construct( array $config , $url = '' )
    {
        $this->config          = $config;
        $this->mode            = $this->getImageMode( $this->config );
        $this->localImgNumber  = $this->getLocalImgNumber( $this->config );
        $this->savePath        = $this->getSavePath( $this->config );
        $this->deleteImageFile = $this->getDeleteImageFile( $this->config );
    }

    /**
     * @return array
     */
    //TODO 用这里的path，构造函数里的要改
    public function getResult( $path = '' )
    {
        $this->path           = $path;
        $this->fetchImagePath = $this->getFetchImagePath( $this->config , $this->path );
        $image                = $this->setImageInfo( $this->fetchImagePath , $this->savePath , $this->mode , $this->localImgNumber , $this->config );
        $imageBinaryArr       = $this->binarization( $this->imageInfo['width'] , $this->imageInfo['height'] , $image );

        imagedestroy( $image );
        unset( $image );

        return [
            'imageInfo'      => $this->imageInfo ,
            'imageBinaryArr' => $imageBinaryArr ,
        ];
    }

    /**
     * @param $width
     * @param $height
     * @param $image
     * @return array
     * 二值化
     */
    public function binarization( $width , $height , $image )
    {
        $imageArr = [];
        for($y = 0; $y < $height; ++$y)
        {
            for($x = 0; $x < $width; ++$x)
            {
                $rgb      = imagecolorat( $image , $x , $y );
                $rgbArray = imagecolorsforindex( $image , $rgb );
                if ($rgbArray['red'] < 110 && $rgbArray['green'] < 110 && $rgbArray['blue'] > 100)
                {
                    $imageArr[$y][$x] = '1';
                }
                else
                {
                    $imageArr[$y][$x] = '0';
                }
            }
        }
        return $imageArr;
    }

    /**
     * @param array $config
     * @return mixed
     */
    protected function getImageMode( array $config )
    {
        return $config['VerifyImageMode'];
    }

    /**
     * @param array $config
     * @return mixed
     */
    protected function getSavePath( array $config )
    {
        return $config['ImagePath']['online']['savePath'];

    }

    /**
     * @param array $config
     * @return mixed
     */
    protected function getDeleteImageFile( array $config )
    {
        return $config['ImagePath']['online']['deleteImageFile'];
    }

    protected function getLocalImgNumber( array $config )
    {
        return $config['ImagePath']['local']['number'];

    }

    /**
     * @param array $config
     * @param $url
     * @return mixed
     */
    protected function getFetchImagePath( array $config , $url )
    {
        return $this->mode == 'online'
            ? $this->getUrl( $config , $url )
            : (empty( $url )
                ? $config['ImagePath']['local']['dir']
                : $url);

    }

    /**
     * @param $config
     * @param $url
     * @return mixed
     */
    protected function getUrl( $config , $url )
    {
        return empty( $url )
            ? $config['ImagePath']['online']['url']
            : $url;
    }

    /**
     * @param $path
     * @param $savePath
     * @param $mode
     * @return mixed
     */
    protected function setImageInfo( $path , $savePath , $mode , $localImgNumber , $config )
    {
        $image = $mode == 'local'
            ? $this->setImageInfoLocal( $path , $localImgNumber )
            : $this->setImageInfoOnline( $path , $savePath , $config , $mode );
        return $image;
    }


    /**
     * @return mixed
     */
    public function getImageInfo()
    {
        return $this->imageInfo;
    }

}
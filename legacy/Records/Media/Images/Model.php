<?php

namespace ByTIC\Common\Records\Media\Images;

use Nip\Records\Record as Record;
use Nip_File_Image;

/**
 * Class Model
 * @package ByTIC\Common\Records\Media\Images
 * @deprecated use media library repo
 */
class Model extends Nip_File_Image
{

/**
 * Class Model
 * @package ByTIC\Common\Records\Media\Images
 */
class Model extends Image
{
    use \ByTIC\Common\Records\Media\Traits\HasModels;
    use \ByTIC\Common\Records\Media\Traits\HydrateCollection;

    public $basePath;
    public $baseURL;
    public $cropWidth;
    public $cropHeight;

    protected $_type;
    protected $mediaCollection = 'images';

    /**
     * @return Model
     */
    public function getSmall()
    {
        return $this->getImageSize("small");
    }

    /**
     * @param $type
     * @return $this
     */
    public function getImageSize($type)
    {
        if ($type == $this->_type) {
            return $this;
        } else {
            $image = $this->_model->getNewImage($type);

            $image->setName($this->name);

            return $image;
        }
    }

    /**
     * @return Model
     */
    public function getMedium()
    {
        return $this->getImageSize("medium");
    }

    /**
     * @return Model
     */
    public function getLarge()
    {
        return $this->getImageSize("large");
    }

    /**
     * @return Model
     */
    public function getFull()
    {
        return $this->getImageSize("full");
    }

    /**
     * @param string $path
     * @return bool
     */
    public function setResourceFromFile($path)
    {
        parent::setResourceFromFile($path);
        $this->setName(pathinfo($path, PATHINFO_BASENAME));

        return $this;
    }

//    /**
//     * @param string $name
//     */
//    public function setName($name)
//    {
//        parent::setName($name);
//        $this->url = $this->getModel()->getImageURL($this->_type, $this->name);
//        $this->path = $this->getModel()->getImagePath($this->_type, $this->name);
//    }

    /**
     * @param Image $image
     * @return $this
     */
    public function copyResource($image)
    {
        parent::copyResource($image);
        $this->setName($image->name);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultWidth()
    {
        $option = "images_" . $this->getModel()->getManager()->getTable() . "_" . $this->_type . "_width";
        return Options::instance()->$option;
    }

    /**
     * @return mixed
     */
    public function getDefaultHeight()
    {
        $option = "images_" . $this->getManager()->getTable() . "_" . $this->_type . "_height";
        return Options::instance()->$option;
    }

    /**
     * @return mixed
     */
    public function getImageType()
    {
        return $this->_type;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $this->path = $this->path ? $this->path : $this->basePath . $this->name;
        parent::save();

        return $this;
    }

    /**
     * @param bool $bubble
     * @return $this
     */
    public function delete($bubble = false)
    {
        if ($bubble) {
            return parent::delete();
        } else {
            return $this->getModel()->deleteImage($this->name);
        }
    }
}

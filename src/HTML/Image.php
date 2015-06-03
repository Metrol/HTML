<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

namespace Metrol\HTML;

/**
 * Define the image tag
 *
 */
class Image extends Tag
{
    /**
     * The kinds of horizontal alignment options
     *
     * @var array
     */
    private static $horizAlignRef;

    /**
     * The kinds of vertical alignment options
     *
     * @var array
     */
    private static $vertAlignRef;

    /**
     * The URL object that will be put into the "src=" attribute
     *
     * @var URL
     */
    private $sourceURL;

    /**
     *
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        parent::__construct('img', self::CLOSE_SELF);

        self::initRefVars();

        $this->sourceURL = new URL;

        $this->setImage($fileName);
    }

    /**
     * Adds a few extra attributes on the way out the door.
     *
     * @return string
     */
    public function output()
    {
        if ( !$this->sourceURL->isEmpty() )
        {
            $this->addAttribute('src', $this->sourceURL->output());
        }

        return parent::output();
    }

    /**
     * Sets the image source URL
     *
     * @param string $fileName
     *
     * @return $this
     */
    public function setImage($fileName)
    {
        $this->sourceURL->setURL($fileName);

        return $this;
    }

    /**
     * Sets the alternate text for the image
     *
     * @param string $text
     *
     * @return $this
     */
    public function setAlt($text)
    {
        $this->addAttribute('alt', Text::htmlent($text));

        return $this;
    }

    /**
     * Sets the title text for the image
     *
     * @param string $text
     *
     * @return $this
     */
    public function setTitle($text)
    {
        $this->addAttribute('title', Text::htmlent($text));

        return $this;
    }

    /**
     * Specifies the border attribute
     *
     * @param integer $size
     *
     * @return $this
     */
    public function setBorder($size)
    {
        $this->addAttribute('border', intval($size));

        return $this;
    }

    /**
     * Sets the size of the image
     *
     * @param integer $height
     * @param integer $width
     *
     * @return $this
     */
    public function setSize($height, $width)
    {
        $this->addAttribute('height', intval($height))
             ->addAttribute('width',  intval($width));

        return $this;
    }

    /**
     * Specifies the alignment of an image according to surrounding elements
     *
     * @param string $alignment
     *
     * @return $this
     */
    public function setAlign($alignment)
    {
        $align = strtolower($alignment);

        if ( in_array($align, self::$horizAlignRef) )
        {
            $this->addAttribute('align', $align);
        }
        else
        {
            $this->getAttributeObj()->delete('align');
        }

        return $this;
    }

    /**
     * Specifies the vertical alignment of the image
     *
     * @param string $alignment
     *
     * @return $this
     */
    public function setVerticalAlign($alignment)
    {
        $align = strtolower($alignment);

        if ( strpos($alignment, "%") or in_array($align, self::$vertAlignRef) )
        {
            $this->addStyle("vertical-align", $align);
        }
        else
        {
            $this->getAttributeObj()->deleteStyle('vertical-align');
        }

        return $this;
    }

    /**
     * Provide the source URL object attached to this tag.
     *
     * @return URL
     */
    public function getImageURL()
    {
        return $this->sourceURL;
    }

    /**
     * Initializes the static reference vars if they haven't had values stuffed
     * in them yet.
     *
     */
    private static function initRefVars()
    {
        if ( !empty(self::$horizAlignRef) )
        {
            return;
        }

        self::$horizAlignRef = array('top', 'bottom', 'middle', 'left', 'right');

        self::$vertAlignRef  = array('baseline', 'sub', 'super', 'top',
                                     'text-top', 'middle', 'bottom',
                                     'text-bottom', 'length');
    }
}

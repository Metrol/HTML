<?php
/**
 * @author    Michael Collette <metrol@metrol.net>
 * @package   Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license   Included with package files
 */

namespace Metrol\HTML;

use Metrol\HTML\Tag\Attribute;

/**
 * Base class for all HTML tags defined by Metrol classes
 *
 */
class Tag
{
    /**
     * Closure type defined to have a closing tag at the end of some content.
     *
     * @const
     */
    const CLOSE_CONTENT = 0;

    /**
     * Closure type defined for tags that close within themselves.
     *
     * @const
     */
    const CLOSE_SELF = 1;

    /**
     * Closure type defined to not close the tag automatically.
     *
     * @const
     */
    const CLOSE_NONE = 2;

    /**
     * The name of the tag
     *
     * @var string
     */
    private $tagName;

    /**
     * List of tag attributes used in the anchor
     *
     * @var \Metrol\HTML\Tag\Attribute
     */
    protected $attribObj;

    /**
     * Which kind of tag closure this tag needs
     *
     * @var integer
     */
    private $closure;

    /**
     * Text that will appear before the opening tag.
     *
     * @var string
     */
    private $before;

    /**
     * For tags that will contain information within them, this will be the var
     * where that is stored.
     *
     * @var string
     */
    private $content;

    /**
     * Text that appears after the closing tag
     *
     * @var string
     */
    private $after;

    /**
     * Stores the name of the tag to later be used to assemble output from here.
     *
     * @param string  $tagName
     * @param integer $tagClose What kind of closure this tag requires
     */
    public function __construct($tagName, $tagClose)
    {
        $this->content    = '';
        $this->before     = '';
        $this->after      = '';
        $this->closure    = self::CLOSE_CONTENT;

        $this->setTagName($tagName);
        $this->setClosureType($tagClose);
    }

    /**
     * Provide the assembled tag
     *
     * @return string
     */
    public function __toString()
    {
        return $this->output();
    }

    /**
     * Assemble the tag
     *
     * @return string
     */
    public function output()
    {
        $rtn = '';

        if (strlen($this->before) > 0)
        {
            $rtn .= $this->before;
        }

        $rtn .= $this->getOpen();

        if ($this->closure == self::CLOSE_CONTENT)
        {
            $rtn .= $this->content;
        }

        if ($this->closure == self::CLOSE_NONE)
        {
            $rtn .= $this->content;
        }

        if ($this->closure == self::CLOSE_CONTENT)
        {
            $rtn .= $this->getClose();

            if (strlen($this->after) > 0)
            {
                $rtn .= $this->after;
            }
        }

        if ($this->closure == self::CLOSE_SELF)
        {
            if (strlen($this->after) > 0)
            {
                $rtn .= $this->after;
            }
        }

        return $rtn;
    }

    /**
     * Sets what tag name this is for.
     *
     * @param string $tag
     *
     * @return $this
     */
    protected function setTagName($tag)
    {
        $this->tagName = substr(strtolower($tag), 0, 50);

        return $this;
    }

    /**
     * Allow the closure type for this tag be changed.
     *
     * @param integer $closure
     *
     * @return $this
     */
    public function setClosureType($closure)
    {
        switch ( intval($closure) )
        {
            case self::CLOSE_CONTENT:
                $this->closure = $closure;
                break;

            case self::CLOSE_SELF:
                $this->closure = $closure;
                break;

            case self::CLOSE_NONE:
                $this->closure = $closure;
                break;

            default:
                $this->closure = self::CLOSE_CONTENT;
        }

        return $this;
    }

    /**
     * Sets a bit of text that will show up before the opening tag when ouput
     *
     * @param string $text
     *
     * @return $this
     */
    public function setBefore($text)
    {
        $this->before = Text::htmlent($text);

        return $this;
    }

    /**
     * Sets text before the tag, without any filtering
     *
     * @param string $text
     *
     * @return $this
     */
    public function setBeforeRaw($text)
    {
        $this->before = $text;

        return $this;
    }

    /**
     * Provide what is stored in the Before content
     *
     * @return string
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * Sets a bit of text that will show just after the closing tag when output
     *
     * @param string $text
     *
     * @return $this
     */
    public function setAfter($text)
    {
        $this->after = Text::htmlent($text);

        return $this;
    }

    /**
     * Sets a bit of text, without filtering, that will show just after the
     * closing tag when output
     *
     * @param string $text
     *
     * @return $this
     */
    public function setAfterRaw($text)
    {
        $this->after = $text;

        return $this;
    }

    /**
     * Provide what is stored in the After content
     *
     * @return string
     */
    public function getAfter()
    {
        return $this->after;
    }

    /**
     * Provide only the opening for the tag in question.
     *
     * @return string
     */
    public function getOpen()
    {
        $rtn = '<';
        $rtn .= $this->tagName;

        if ( !$this->getAttributeObj()->isEmpty() )
        {
            $rtn .= ' '.$this->getAttributeObj();
        }

        if ($this->closure == self::CLOSE_SELF)
        {
            $rtn .= ' />';
        }
        else
        {
            $rtn .= '>';
        }

        return $rtn;
    }

    /**
     * Provide only the closure for the tag.
     *
     * @return string
     */
    public function getClose()
    {
        $rtn = '';

        if ( $this->closure !== self::CLOSE_SELF )
        {
            $rtn = '</'.$this->tagName.'>';
        }

        return $rtn;
    }

    /**
     * Provide the ability to set an entire attribute object into this tag.
     *
     * @param \Metrol\HTML\Tag\Attribute $attributeObj
     *
     * @return $this
     */
    public function setAttributeObj(Attribute $attributeObj)
    {
        $this->attribObj = $attributeObj;

        return $this;
    }

    /**
     * Provide the attribute object attached to this tag
     *
     * @return \Metrol\HTML\Tag\Attribute
     */
    public function getAttributeObj()
    {
        if ( !is_object($this->attribObj) )
        {
            $this->attribObj = new Attribute;
        }

        return $this->attribObj;
    }

    /**
     * Sets the contents of this tag.  The content is automatically run through
     * htmlentities.
     *
     * @param string $text
     *
     * @return $this
     */
    public function setContent($text)
    {
        $this->content = Text::htmlent($text);

        return $this;
    }

    /**
     * Sets the contents unfiltered
     *
     * @param string $text
     *
     * @return $this
     */
    public function setRawContent($text)
    {
        $this->content = $text;

        return $this;
    }

    /**
     * Provides whatever is stored in the contentsVal
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Add an attribute to the tag
     *
     * @param string $attributeName
     * @param string $attributeValue
     *
     * @return $this;
     */
    public function addAttribute($attributeName, $attributeValue)
    {
        $this->getAttributeObj()->add($attributeName, $attributeValue);

        return $this;
    }

    /**
     * Fetch the specified attribute value
     *
     * @param string $attributeName
     *
     * @return string;
     */
    public function getAttribute($attributeName)
    {
        return $this->getAttributeObj()->get($attributeName);
    }

    /**
     * Assign a JavaScript action to a DOM event
     *
     * @param string $jsCall Javascript to run
     * @param string $eventType What kind of event to bind to.
     *
     * @return $this
     */
    public function setEvent($jsCall, $eventType = 'onclick')
    {
        $this->addAttribute($eventType, $jsCall);

        return $this;
    }

    /**
     * Adds a CSS Class name for this tag
     *
     * @param string
     *
     * @return $this
     */
    public function addClass($className)
    {
        $this->getAttributeObj()->addClass($className);

        return $this;
    }

    /**
     * Sets the CSS ID for this tag
     *
     * @param string
     *
     * @return $this
     */
    public function setID($idName)
    {
        $this->addAttribute('id', $idName);

        return $this;
    }
}

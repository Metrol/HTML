<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

namespace Metrol\HTML;

/**
 * An HTML Anchor tag.
 *
 */
class Anchor extends Tag
{
    /**
     * URL to for the Anchor tag to link to
     *
     * @var URL
     */
    protected $url;

    /**
     * Pass in the URL that will act as the base of this object.
     *
     * As a special case, if the URL passed in is "print" then an anchor will
     * created with a JavaScript page print command.
     *
     * @param string $url  URL to set the HREF to
     * @param string $text The text to link
     */
    public function __construct($url = '', $text = '')
    {
        parent::__construct('a', self::CLOSE_CONTENT);

        $this->url = new URL;

        $this->setContent($text);

        if ( strtolower($url) == 'print' )
        {
            $this->printPage();
        }
        else
        {
            $this->setURL($url);
        }
    }

    /**
     * Assembles the URL and calls to the parent Tag class to put everything
     * together for an output.
     *
     * @return string
     */
    public function output()
    {
        if ( $this->url->isEmpty() )
        {
            $this->getAttributeObj()->delete('href');
        }
        else
        {
            $this->addAttribute('href', $this->url);
        }

        $tag = parent::output();

        return $tag;
    }

    /**
     * Adds a new parameter to the URL
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function param($key, $value)
    {
        $this->url->addParam($key, $value);

        return $this;
    }

    /**
     * Sets the href URL for the Anchor Tag
     *
     * @param string $urlString
     *
     * @return $this
     */
    public function setURL($urlString)
    {
        $this->url->setURL($urlString);

        return $this;
    }

    /**
     * Sets the URL object to use for the href attribute
     *
     * @param URL $urlObj
     *
     * @return $this
     */
    public function setURLObj(URL $urlObj)
    {
        $this->url = $urlObj;

        return $this;
    }


    /**
     * Sets the URL as a Javascript function
     *
     * @param string $javascriptCall Javascript to run
     *
     * @return $this
     */
    public function setJS($javascriptCall)
    {
        $this->url->setURL('javascript:' . $javascriptCall);

        return $this;
    }

    /**
     * Takes in what should be (not checked) a valid Email address and
     * creates the proper URL and linkText for it.
     *
     * @param string $email Email address to send to
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->url->setURL('mailto:' . $email);

        if ( empty($this->content) )
        {
            $this->setRawContent($email);
        }

        $this->addAttribute('title', 'Send an Email to ' . $email);

        return $this;
    }

    /**
     * Sets an image to be linked to.
     * If link text has been set that will become the title attribute for
     * the image.
     *
     * @param Image $image
     *
     * @return $this
     */
    public function setImage(Image $image)
    {
        $img = clone $image;

        // When passing an image on in going to try to set it's title attribute by
        // either using the title from this tag, or the existing content text.
        if ( $this->getAttributeObj()->exists('title') )
        {
            $img->addAttribute('title', $this->getAttribute('title'));
        }
        elseif ( !empty($this->content) )
        {
            $img->addAttribute('title', $this->content);
        }

        // Always toss in a 0 border for a linked image.
        $img->addAttribute('border', 0);

        $this->setRawContent($img->output());

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
     * Puts all the attributes in place for a JavaScript print page call
     *
     * @return $this
     */
    public function printPage()
    {
        $this->setJS('window.print()');
        $this->addAttribute('title', 'Print the contents of this page');

        if ( empty($this->content) )
        {
            $this->setContent('Print Page');
        }

        return $this;
    }

    /**
     * Sets the local anchor name to go to in the URL.
     * In other words, adds the specified name as #name.
     *
     * @param string $anchorName
     *
     * @return $this
     */
    public function setAnchor($anchorName)
    {
        $this->url->setAnchor($anchorName);

        return $this;
    }

    /**
     * Set the target to a new window
     *
     * @return $this
     */
    public function setNewWindow()
    {
        $this->setTarget('_blank');

        return $this;
    }

    /**
     * Set the target to within the existing frame
     *
     * @return $this
     */
    public function setSameFrame()
    {
        $this->setTarget('_self');

        return $this;
    }

    /**
     * Set the target to the top most window that is open
     *
     * @return $this
     */
    public function setTopWindow()
    {
        $this->setTarget('_top');

        return $this;
    }

    /**
     * Set the target to the immediate parent's frame or window
     *
     * @return $this
     */
    public function setParentFrame()
    {
        $this->setTarget('_parent');

        return $this;
    }

    /**
     * Actually sets the target
     *
     * @param string $targetName
     */
    public function setTarget($targetName)
    {
        $this->addAttribute('target', $targetName);
    }
}

<?php
/**
 * @author    Michael Collette <metrol@metrol.net>
 * @package   Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license   Included with package files
 */

namespace Metrol\HTML\Tag;

/**
 * Handles all the attributes that can be stored in a tag.
 *
 */
class Attribute
{
    /**
     * The list of attributes and their values
     *
     * @var array
     */
    private $attribs;

    /**
     * A list of styles that are kept separate from the rest of the attributes
     * until assembled.
     *
     * @var array
     */
    private $styles;

    /**
     * A list of CSS Classes that will be included at the time the attributes
     * are assembled.
     *
     * @var array
     */
    private $classes;

    /**
     * Instantiate the object and get all the member arrays initialized.
     *
     */
    public function __construct()
    {
        $this->attribs = array();
        $this->styles  = array();
        $this->classes = array();
    }

    /**
     * Produce the attributes into a single string suitable for insertion into
     * a Tag object.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->output();
    }

    /**
     * Produce the attributes into a single string suitable for insertion into
     * a Tag object.
     *
     * @return string
     */
    public function output()
    {
        return $this->assemble();
    }

    /**
     * Determines if this object will have any output
     *
     * @return boolean
     */
    public function isEmpty()
    {
        $rtn = true;

        if ( !empty($this->attribs) )
        {
            $rtn = false;
        }

        if ( !empty($this->classes) )
        {
            $rtn = false;
        }

        if ( !empty($this->styles) )
        {
            $rtn = false;
        }

        return $rtn;
    }

    /**
     * Determine if an attribute exists or not
     *
     * @param string $key
     *
     * @return boolean
     */
    public function exists($key)
    {
        $rtn = false;

        if ( strtolower($key) === 'class' and !empty($this->classes) )
        {
            $rtn = true;
        }

        if ( strtolower($key) === 'style' and !empty($this->styles) )
        {
            $rtn = true;
        }

        if ( array_key_exists($key, $this->attribs) )
        {
            $rtn = true;
        }

        return $rtn;
    }

    /**
     * Removes an attribute from the tag based on its name
     *
     * @param string $key
     *
     * @return $this
     */
    public function delete($key)
    {
        switch ( strtolower($key) )
        {
            case 'class':
                $this->deleteClasses();
                break;

            case 'style':
                $this->deleteStyles();
                break;

            default:
                if ( array_key_exists($key, $this->attribs) )
                {
                    unset($this->attribs[$key]);
                }
        }

        return $this;
    }

    /**
     * Provide the value associated with an attribute.
     *
     * @param string $key
     *
     * @return string
     */
    public function get($key)
    {
        $rtn = '';

        switch ( strtolower($key) )
        {
            case 'class':
                $rtn = $this->getClassString();
                break;

            case 'style':
                $rtn = $this->getStyleString();
                break;

            default:
                if ( array_key_exists($key, $this->attribs) )
                {
                    $rtn = $this->attribs[ $key ];
                }
        }

        return $rtn;
    }

    /**
     * Use this to insure that the attribute being passed in only exists a
     * single time within a tag.  Earlier references will be replaced.
     * Other than Styles and Classes this is identical to the add() method.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function set($key, $value)
    {
        switch ( strtolower($key) )
        {
            case 'class':
                $this->deleteClasses();
                break;

            case 'style':
                $this->deleteStyles();
                break;
        }

        $this->add($key, $value);

        return $this;
    }

    /**
     * To be used by all the various attribute methods to get their attributes
     * properly added to the stack.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function add($key, $value)
    {
        switch ( strtolower($key) )
        {
            case 'class':
                $this->addClass($value);
                break;

            case 'style':
                $this->addStyle($value);
                break;

            default:
                $this->attribs[$key] = strval($value);
        }

        return $this;
    }

    /**
     * Adds a CSS Class to the attribute stack.
     *
     * @param string $className
     *
     * @return $this
     */
    public function addClass($className)
    {
        if ( strpos($className, ' ') !== false )
        {
            $classList = explode(' ', $className);
        }
        else
        {
            $classList = array($className);
        }

        foreach ( $classList as $classy )
        {
            $this->classes[] = $classy;
        }

        return $this;
    }

    /**
     * Removes a class from the stack
     *
     * @param string $className
     *
     * @return $this
     */
    public function deleteClass($className)
    {
        $srchResult = array_search($className, $this->classes);

        if ( $srchResult !== false )
        {
            unset($this->classes[$srchResult]);
        }

        return $this;
    }

    /**
     * Removes all the classes from the stack
     *
     * @return $this
     */
    public function deleteClasses()
    {
        $this->classes = array();

        return $this;
    }

    /**
     * Provide the assembled class attribute
     *
     * @return string
     */
    public function getClassString()
    {
        $rtn = '';

        if ( count($this->classes) > 0 )
        {
            $rtn .= implode(' ', $this->classes);
       }

        return $rtn;
    }

    /**
     * Adds a CSS Style to the stack.
     *
     * This expects an input that looks like:
     * 'border: 1px'
     *
     * @param string $style
     * @param string $value
     *
     * @return $this
     */
    public function addStyle($style, $value = null)
    {
        if ( $value !== null )
        {
            $this->styles[ $style ] = $value;
        }
        else
        {
            $styleParts = $this->parseCompoundStyle($style);

            foreach ( $styleParts as $styleKey => $styleValue )
            {
                $this->styles[$styleKey] = $styleValue;
            }
        }

        return $this;
    }

    /**
     * Deletes the specified style from the stack
     *
     * @param string $style
     *
     * @return $this
     */
    public function deleteStyle($style)
    {
        if ( array_key_exists($style, $this->styles) )
        {
            unset($this->styles[$style]);
        }

        return $this;
    }

    /**
     * Deletes all the styles from the stack
     *
     * @return $this
     */
    public function deleteStyles()
    {
        $this->styles = array();

        return $this;
    }

    /**
     * Provide the assembled style string
     *
     * @return string
     */
    public function getStyleString()
    {
        $rtn = '';

        if ( count($this->styles) > 0 )
        {
            $styleStr = '';

            foreach ( $this->styles as $key => $val )
            {
                $styleStr .= "$key: $val; ";
            }

            $rtn .= trim($styleStr);
        }

        return $rtn;
    }

    /**
     * Takes in a compound style assignment and breaks it up into components for
     * the addStyle() method.
     *
     * @param string $compoundStyle
     *
     * @return array
     */
    public function parseCompoundStyle($compoundStyle)
    {
        $rtn = array();

        if ( strpos($compoundStyle, ';') !== false )
        {
            $pairs = explode(';', $compoundStyle);
        }
        else
        {
            $pairs = array($compoundStyle);
        }

        foreach ( $pairs as $pair )
        {
            if ( strpos($pair, ':') === false or substr_count($pair, ':') > 1)
            {
                continue;
            }

            list($key, $value) = explode(':', $pair);

            $rtn[trim($key)] = trim($value);
        }

        return $rtn;
    }

    /**
     * Assembles all the attributes into a string ready to go into a tag's
     * opening
     *
     * @return string
     */
    private function assemble()
    {
        $rtn = '';

        foreach ( $this->attribs as $key => $val )
        {
            $rtn .= $key.'="'.$val.'" ';
        }

        if ( !empty($this->classes) )
        {
            $rtn .= 'class="';
            $rtn .= $this->getClassString();
            $rtn .= '" ';
        }

        if ( !empty($this->styles) )
        {
            $rtn .= 'style="';
            $rtn .= $this->getStyleString();
            $rtn .= '" ';
        }

        return trim($rtn);
    }
}

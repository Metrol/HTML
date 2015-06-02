<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

namespace Metrol\HTML;

/**
 * A set of handy utiliies for cleaning up text prior to displaying within the
 * context of HTML
 *
 */
class Text
{
    const CHAR_ENCODE = 'UTF-8';

    /**
     * Converts all line breaks into HTML bullets
     *
     * @param string $text
     *
     * @return string
     */
    public static function bulletsHTML($text)
    {
        $br_Old = "<br />";
        $br_New = "<br />";
        $ls = "</li>\n<li>";

        $text = self::htmlent($text, false); // Convert special characters
        $para = nl2br(stripslashes(trim($text)));  // No left over back slashes

        $para = str_replace($br_Old, $ls, $para);
        $para = str_replace($br_New, $ls, $para);

        $result  = "<ul>\n";
        $result .= "<li>\n";
        $result .= "$para\n";
        $result .= "</li>\n";
        $result .= "</ul>\n";

        return $result;
    }

    /**
     * Takes plain text meant for HTML output and cleans it up.
     * Line breaks are converted to breaks, entities are fixed, and word
     * wrapping is applied.
     *
     * @param string $text
     *
     * @return string
     */
    public static function plainTextToHTML($text)
    {
        $text = self::htmlent($text, false); // Convert special characters
        $para = stripslashes($text);         // No left over back slashes
        $para = nl2br($para);                // Get them breaks in there

        // For nice clean source code, wrap the text.  Deal with them break tags
        // that will wrap apart if we don't.
        $para = str_replace("<br />", "<br/>", $para); // So tags aren't split up
        $para = wordwrap($para);
        $para = str_replace("<br/>", "<br />", $para); // Now back to correct

        return $para;
    }

    /**
     * A preconfigured extension for htmlentities().
     *
     * @param string $text What text to convert entities for
     * @param boolean $double_encode If entities should be encoded or not
     *
     * @return string
     */
    public static function htmlent($text, $double_encode = true)
    {
        // ENT_QUOTES enabeld to prevent XSS
        // ENT_HTML5 as the default HTML version to support
        // Handle encoding based on the class constant
        $rtn = htmlentities($text, ENT_QUOTES | ENT_HTML5, self::CHAR_ENCODE,
                            $double_encode);

        return $rtn;
    }
}

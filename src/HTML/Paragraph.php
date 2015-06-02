<?php
/**
 * @author    Michael Collette <metrol@metrol.net>
 * @package   Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license   Included with package files
 */

namespace Metrol\HTML;

/**
 * Define the paragraph tag
 *
 */
class Paragraph extends Tag
{
    /**
     * Instantiate the tag
     *
     */
    public function __construct()
    {
        parent::__construct('p', self::CLOSE_CONTENT);
    }
}

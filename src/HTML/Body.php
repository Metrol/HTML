<?php
/**
 * @author    Michael Collette <metrol@metrol.net>
 * @package   Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license   Included with package files
 */

namespace Metrol\HTML;

/**
 * Define the body tag
 *
 */
class Body extends Tag
{
    /**
     * Instantiate the body tag object
     *
     */
    public function __construct()
    {
        parent::__construct('body', self::CLOSE_CONTENT);
    }
}

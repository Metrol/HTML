<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

use \Metrol\HTML\Image;

/**
 * Testing the methods that suppor the image tag
 *
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creating the tag and basic usage
     *
     */
    public function testImageTagCreate()
    {
        $i = new Image('/assets/images/blue.png');

        $this->assertEquals('<img src="/assets/images/blue.png" />',
                            $i->output());
    }

    /**
     * Various attribute helper methods
     *
     */
    public function testImageAttributeMethods()
    {
        $i = new Image('blue.png');

        $this->assertEquals('<img src="blue.png" />', $i->output());

        $i->setSize(200, 300);
        $this->assertEquals('<img src="blue.png" height="200" width="300" />', $i->output());

        $i->getAttributeObj()->delete('height')->delete('width');

        $i->setBorder(12);
        $this->assertEquals('<img src="blue.png" border="12" />', $i->output());

        // Remove extra attributes already tested
        $i->getAttributeObj()->delete('border')->delete('style');
        $this->assertEquals('<img src="blue.png" />', $i->output());


        $i->setAlt('Howdy there from the "cool" image');
        $this->assertEquals('<img src="blue.png" alt="Howdy there from the &quot;cool&quot; image" />', $i->output());

        $i->getAttributeObj()->delete('alt');
        $i->setTitle('Howdy there from the "cool" image');

        $this->assertEquals('<img src="blue.png" title="Howdy there from the &quot;cool&quot; image" />', $i->output());
    }

    /**
     * Alignment and style testing.  Some restricted entries worth testing on
     * their own.
     *
     */
    public function testAlignmentAndStyles()
    {
        $i = new Image('blue.png');
        $i->output(); // so the src attribute gets added to the top of the stack

        $i->setAlign('left');
        $this->assertEquals('<img src="blue.png" align="left" />', $i->output());

        // Trying to set an alignment not allowed should remove the align
        // attribute.
        $i->setAlign('lefft');
        $this->assertEquals('<img src="blue.png" />', $i->output());

        $i->setVerticalAlign('super');
        $this->assertEquals('<img src="blue.png" style="vertical-align: super;" />', $i->output());

        // Try a bad alignment
        $i->setVerticalAlign('suxper');
        $this->assertEquals('<img src="blue.png" />', $i->output());

        // Now try a good alignment with other styles
        $i->setVerticalAlign('super')->addStyle('border', '1px');
        $this->assertEquals('<img src="blue.png" style="vertical-align: super; border: 1px;" />', $i->output());

        // A bad vertical alignment should only remove that specific style
        $i->setVerticalAlign('suxper');
        $this->assertEquals('<img src="blue.png" style="border: 1px;" />', $i->output());
    }
}

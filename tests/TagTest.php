<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

use \Metrol\HTML\Tag;

/**
 * Testing the Tag class
 *
 */
class TagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Try out different closure types
     *
     */
    public function testTagClosureTypes()
    {
        $tag = new Tag('xyz', Tag::CLOSE_CONTENT);
        $this->assertEquals('<xyz></xyz>', (string) $tag);
        $this->assertEquals('<xyz>', $tag->getOpen());
        $this->assertEquals('</xyz>', $tag->getClose());

        $tag->setClosureType(Tag::CLOSE_SELF);
        $this->assertEquals('<xyz />', (string) $tag);

        $tag->setClosureType(Tag::CLOSE_NONE);
        $this->assertEquals('<xyz>', (string) $tag);
    }

    /**
     * Add some content within, before, and after a tag.
     *
     */
    public function testAddContentAroundTag()
    {
        $tag = new Tag('xyz', Tag::CLOSE_CONTENT);
        $tag->setContent('Howdy');

        $this->assertEquals('<xyz>Howdy</xyz>', $tag->output());

        $tag->setBefore('Hey &')->setAfter('There!');
        $this->assertEquals('Hey &amp;<xyz>Howdy</xyz>There&excl;', $tag->output());

        $this->assertEquals('Hey &amp;', $tag->getBefore());
        $this->assertEquals('Howdy', $tag->getContent());
        $this->assertEquals('There&excl;', $tag->getAfter());
    }

    /**
     * Test access through to the attributes object and some of the helper
     * methods in there to do that.
     *
     */
    public function testAtributeObjectHelperMethods()
    {
        $tag = new Tag('xyz', Tag::CLOSE_NONE);
        $tag->addAttribute('title', 'Howdy')
            ->addAttribute('size', 'big');

        $this->assertEquals('<xyz title="Howdy" size="big">', $tag->output());

        $tag->setID('X123');
        $this->assertEquals('<xyz title="Howdy" size="big" id="X123">', $tag->output());

        $tag = new Tag('xyz', Tag::CLOSE_NONE);
        $tag->addClass('shiny happy people');
        $this->assertEquals('<xyz class="shiny happy people">', $tag->output());

        $tag->getAttributeObj()->deleteClass('shiny');
        $this->assertEquals('<xyz class="happy people">', $tag->output());
    }
}

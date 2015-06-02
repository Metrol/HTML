<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

use \Metrol\HTML\Tag\Attribute;

/**
 * Testing the URL class
 *
 */
class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Make sure an empty URL does what it's supposed to... nothing.
     *
     */
    public function testWithoutAnyInput()
    {
        $attr = new Attribute;
        $this->assertInstanceOf('\Metrol\HTML\Tag\Attribute', $attr);
        $this->assertEmpty($attr->output());

        $this->assertTrue($attr->isEmpty());

        $attr->add('foo', 'bar');
        $this->assertFalse($attr->isEmpty());
    }

    /**
     * Try adding attributes
     *
     */
    public function testAddingAttributes()
    {
        $attr = new Attribute;

        $attr->add('x', 1234);
        $this->assertEquals('x="1234"', $attr->output());

        $attr->add('y', 'foo');
        $this->assertEquals('x="1234" y="foo"', $attr->output());

        $attr->add('z', 'bar');
        $this->assertEquals('x="1234" y="foo" z="bar"', $attr->output());

        // Change an existing attribute
        $attr->add('x', 'snafu');
        $this->assertEquals('x="snafu" y="foo" z="bar"', $attr->output());

        // Remove an attribute
        $attr->delete('z');
        $this->assertEquals('x="snafu" y="foo"', $attr->output());
    }

    /**
     * Adding classes along with other attributes
     *
     */
    public function testAddingEditingClasses()
    {
        $attr = new Attribute;

        $attr->add('id', 'tag1');
        $attr->add('class', 'snazzy');
        $this->assertEquals('id="tag1" class="snazzy"', $attr->output());

        $attr->addClass('fuzzy');
        $this->assertEquals('id="tag1" class="snazzy fuzzy"', $attr->output());

        $attr->deleteClass('snazzy')->add('src', 'river');
        $this->assertEquals('id="tag1" src="river" class="fuzzy"', $attr->output());

        $attr->deleteClasses();
        $this->assertEquals('id="tag1" src="river"', $attr->output());

        // Try adding multiple class names at once
        $attr->addClass('foo bar snafu');
        $this->assertEquals('id="tag1" src="river" class="foo bar snafu"', $attr->output());

        // They are still treated like individual classes though.
        $attr->deleteClass('bar');
        $this->assertEquals('id="tag1" src="river" class="foo snafu"', $attr->output());
    }

    /**
     * Testing in line styles.
     *
     */
    public function testAddingEditingStyles()
    {
        $attr = new Attribute;
        $attr->addStyle('color', 'blue');
        $this->assertEquals('style="color: blue;"', strval($attr));

        $attr->deleteStyles()
             ->add('style', 'border:2134;color:red;size:small');
        $this->assertEquals('style="border: 2134; color: red; size: small;"', $attr->output());

        $attr->addStyle('color', '#FFF');
        $this->assertEquals('style="border: 2134; color: #FFF; size: small;"', $attr->output());

        // Clear the styles, then add a simple compound style
        $attr->deleteStyles()
            ->addStyle('color: blue;');
        $this->assertEquals('style="color: blue;"', strval($attr));

        // Clear the styles, then add a more complex style
        $attr->deleteStyles()
             ->addStyle('border:2134;color:red;size:small');
        $this->assertEquals('style="border: 2134; color: red; size: small;"', $attr->output());
    }
}

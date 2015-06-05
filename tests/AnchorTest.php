<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

use \Metrol\HTML\Image;
use \Metrol\HTML\URL;
use \Metrol\HTML\Anchor;

/**
 * Testing the methods that support the anchor tag
 *
 */
class AnchorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creating the tag and basic usage
     *
     */
    public function testAnchorTagCreate()
    {
        $a = new Anchor('/sub/dir/file.html', 'Click Here');

        $this->assertEquals('<a href="/sub/dir/file.html">Click Here</a>',
                            $a->output());
    }

    /**
     * Setting the target of the anchor
     *
     */
    public function testAnchorTargetSet()
    {
        $a = new Anchor('/sub/dir/file.html', 'Click Here');

        $a->output(); // So the href is the first attribute

        $a->setNewWindow();

        $this->assertEquals('<a href="/sub/dir/file.html" target="_blank">Click Here</a>',
                            $a->output());

        $a->setSameFrame();

        $this->assertEquals('<a href="/sub/dir/file.html" target="_self">Click Here</a>',
                            $a->output());

        $a->setTopWindow();

        $this->assertEquals('<a href="/sub/dir/file.html" target="_top">Click Here</a>',
                            $a->output());

        $a->setParentFrame();

        $this->assertEquals('<a href="/sub/dir/file.html" target="_parent">Click Here</a>',
                            $a->output());

        // Manually assign a target
        $a->setTarget('foobar');

        $this->assertEquals('<a href="/sub/dir/file.html" target="foobar">Click Here</a>',
                            $a->output());
    }

    /**
     * Try out the print page short cut
     *
     */
    public function testPrintPageAnchorTag()
    {
        $a = new Anchor('pRiNt'); // Not case sensitive

        $this->assertEquals('<a title="Print the contents of this page" '.
                            'href="javascript:window.print()">Print Page</a>',
                            $a->output());
    }

    /**
     * Work with URLs, parameters and related.
     *
     */
    public function testURLWrapperMethods()
    {
        $a = new Anchor('x.htm', 'y');
        $this->assertEquals('<a href="x.htm">y</a>', $a->output());

        $a->param('id', '123')->param('g', 'foo');
        $this->assertEquals('<a href="x.htm?id=123&g=foo">y</a>', $a->output());

        $a->param('h', 'S o m#%'); // Make sure encoding is happening
        $this->assertEquals('<a href="x.htm?id=123&g=foo&h=S+o+m%23%25">y</a>', $a->output());
    }

    /**
     * Working with injected Images and URLs
     *
     */
    public function testInjectedObjects()
    {
        $u = new URL('/foo/bar/');
        $a = new Anchor;
        $img = new Image('/assets/red.png');

        $a->setURLObj($u);

        $this->assertEquals('<a href="/foo/bar/"></a>', $a->output());

        $a->setImage($img);

        $this->assertEquals('<a href="/foo/bar/"><img border="0" src="/assets/red.png" /></a>', $a->output());

        // Reset things, this time with content in the anchor
        $a = new Anchor('/foo/bar/', 'xyz');
        $img = new Image('/assets/red.png');
        $a->setImage($img);

        $this->assertEquals('<a href="/foo/bar/"><img title="xyz" border="0" src="/assets/red.png" /></a>', $a->output());
    }

    /**
     * Special URL setters
     *
     */
    public function testURLSetters()
    {
        $a = new Anchor;
        $a->setEmail('metrol@metrol.net');
        $this->assertEquals('<a title="Send an Email to metrol@metrol.net" href="mailto:metrol@metrol.net">metrol@metrol.net</a>', $a->output());

        $a = new Anchor;
        $a->setContent('Send EMail')
          ->setEmail('metrol@metrol.net');
        $this->assertEquals('<a title="Send an Email to metrol@metrol.net" href="mailto:metrol@metrol.net">Send EMail</a>', $a->output());

        $a = new Anchor;
        $a->setJS('anEvent()');
        $this->assertEquals('<a href="javascript:anEvent()"></a>', $a->output());

        $a = new Anchor;
        $a->setURL('/foo/bar/');
        $this->assertEquals('<a href="/foo/bar/"></a>', $a->output());

        $a->setAnchor('xyz');
        $this->assertEquals('<a href="/foo/bar/#xyz"></a>', $a->output());
    }
}

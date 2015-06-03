<?php
/**
 * @author        Michael Collette <metrol@metrol.net>
 * @package       Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license       Included with package files
 */

use \Metrol\HTML\URL;

/**
 * Testing the URL class
 *
 */
class URLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Make sure an empty URL does what it's supposed to... nothing.
     *
     */
    public function testWithoutAnyInput()
    {
        $url = new URL;
        $this->assertInstanceOf('\Metrol\HTML\URL', $url);
        $this->assertEmpty($url->output());

        $this->assertTrue( $url->isEmpty() );

        $url->setDomain('www.domain.com')
            ->setTransport('ftp');

        $this->assertFalse( $url->isEmpty() );
    }

    /**
     * Testing various transports
     *
     */
    public function testTransports()
    {
        $url = new URL;

        $url->setURL('http://www.domain.com/');
        $this->assertEquals('http', $url->transport);

        $url->setURL('https://www.domain.com/');
        $this->assertEquals('https', $url->transport);

        $url->setURL('ftp://www.domain.com/');
        $this->assertEquals('ftp', $url->transport);

        $url->setURL('javascript:doSomething("xyz")');
        $this->assertEquals('javascript', $url->transport);

        $url->setURL('file://www.domain.com/');
        $this->assertEquals('file', $url->transport);

        $url->setURL('sftp://www.domain.com/');
        $this->assertEquals('sftp', $url->transport);

        $url->setURL('mailto://www.domain.com/');
        $this->assertEquals('mailto', $url->transport);

        // A bad transport should force the value to be bad
        $url->setURL('abcde://www.domain.com/');
        $this->assertEmpty($url->transport);
    }

    /**
     * Making sure the domain is properly extracted from a URL
     *
     */
    public function testDomainBeingExtractedFromURL()
    {
        $url = new URL;

        $url->setURL('http://abc.domain.com');
        $this->assertEquals('abc.domain.com', $url->domain);

        $url->setURL('http://abc.do ma   in.com');
        $this->assertEquals('abc.domain.com', $url->domain);

        $url->setURL('http://abc/blah');
        $this->assertEquals('abc', $url->domain);
    }

    /**
     * Getting the port properly out of the URL when specified
     *
     */
    public function testPortNumberExtractedFromURL()
    {
        $url = new URL;

        $url->setURL('http://domain.com');
        $this->assertNull($url->port);

        $url->setURL('http://domain.com:1234');
        $this->assertEquals(1234, $url->port);

        $url->setURL('http://domain.com:1234/blah/index.html');
        $this->assertEquals(1234, $url->port);

        $url->setURL('http://domain.com:1234/index.html');
        $this->assertEquals(1234, $url->port);
    }

    /**
     * If there are letters in a port number an exception will be thrown
     *
     */
    public function testBadPortNumberInURL()
    {
        $url = new URL;

        $this->setExpectedException('DomainException');
        $url->setURL('http://domain.com:asdf');
    }

    /**
     * Getting the directory out of the URL
     *
     */
    public function testDirectoryExtractedFromURL()
    {
        $url = new URL;

        $url->setURL('http://domain.com');
        $this->assertEquals('', $url->dir);

        $url->setURL('http://domain.com/');
        $this->assertEquals('/', $url->dir);

        $url->setURL('http://domain.com/blah');
        $this->assertEquals('/', $url->dir);

        $url->setURL('http://domain.com/blah/');
        $this->assertEquals('/blah/', $url->dir);

        $url->setURL('http://domain.com/blah/index');
        $this->assertEquals('/blah/', $url->dir);

        $url->setURL('http://domain.com/foo/bar/index');
        $this->assertEquals('/foo/bar/', $url->dir);
    }

    /**
     * Getting the file name out of the URL passed in
     *
     */
    public function testFileNameExtractedFromURL()
    {
        $url = new URL;

        $url->setURL('http://domain.com/');
        $this->assertEmpty($url->fileName);

        $url->setURL('http://domain.com/index.html');
        $this->assertEquals('index.html', $url->fileName);

        $url->setURL('http://domain.com/blah/index.html');
        $this->assertEquals('index.html', $url->fileName);

        $url->setURL('http://domain.com/blah/index.html?x=43&y=23');
        $this->assertEquals('index.html', $url->fileName);
    }

    /**
     * Adding/Deleting key/value pairs to the URL and making sure they work
     *
     */
    public function testAddingValuesToGetQueryOnURL()
    {
        $url = new URL('http://domain.com');

        $url->addParam('x', '1234');
        $this->assertEquals(1, count($url->passVars));
        $this->assertContains('x=1234', $url->output());

        $url->addParam('y', 'foo bar');
        $this->assertContains('y=foo+bar', $url->output());
        $this->assertContains('?x=1234&y=foo+bar', $url->output());

        $url->addParam('y', 'snafu');
        $this->assertContains('?x=1234&y=snafu', $url->output());

        $url->delParam('y');
        $this->assertContains('x=1234', $url->output());

        $url->delAllParam();
        $this->assertEquals(0, count($url->passVars));
    }

    /**
     * Setting the URL should not replace the pass variables
     *
     */
    public function testSetURLDoesNotChangeExistingPassVariables()
    {
        $url = new URL('http://domain.com');

        $url->addParam('x', '1234');
        $this->assertEquals('http://domain.com/?x=1234', $url->output());

        $url->setURL('http://www.domain.com');
        $this->assertEquals('http://www.domain.com/?x=1234', $url->output());

        $url->setURL('http://domain.com/?y=foobar');

        $this->assertEquals('http://domain.com/?x=1234&y=foobar', $url->output());
    }

    /**
     * Javascript callers
     *
     */
    public function testJavaScriptCallers()
    {
        $url = new URL;

        $url->setTransport('javascript')
            ->setDomain('doSomething("xyz")');

        $this->assertEquals('javascript:doSomething("xyz")', $url->output());

        $url->setURL('javascript:doSomething("xyz")', $url->output());
        $this->assertEquals('javascript:doSomething("xyz")', $url->output());
    }

    /**
     * Try putting together a URL entirely by using setters
     *
     */
    public function testPuttingTogetherURLWithSetters()
    {
        $url = new URL;

        $url->setTransport('ftp');;
        $this->assertEquals('ftp:', $url->output());

        $url->setTransport('http');;
        $this->assertEquals('http:', $url->output());

        $url->setDomain('domain.com');
        $this->assertEquals('http://domain.com/', $url->output());

        $url->setDir('/foo/bar/');
        $this->assertEquals('http://domain.com/foo/bar/', $url->output());

        $url->setPort(1234);
        $this->assertEquals('http://domain.com:1234/foo/bar/', $url->output());

        $url->setFile('itsAFile.pdf');
        $this->assertEquals('http://domain.com:1234/foo/bar/itsAFile.pdf', $url->output());

        $url->addParam('x', 4321)->addParam('y', 'foo bar');
        $this->assertEquals('http://domain.com:1234/foo/bar/itsAFile.pdf?x=4321&y=foo+bar', $url->output());

        $url->setUser('bob')->setPassword('dylan');
        $this->assertEquals('http://bob:dylan@domain.com:1234/foo/bar/itsAFile.pdf?x=4321&y=foo+bar', $url->output());
    }
}

<?php
/**
 * @author    Michael Collette <metrol@metrol.net>
 * @package   Metrol_Lib
 * @copyright (c) 2015, Michael Collette
 * @license   Included with package files
 */

namespace Metrol\HTML;

/**
 * Handles Uniform Resource Locators (URL) for the Metrol library
 *
 */
class URL
{
    /**
     * The fully assembled URL
     *
     * @var string
     */
    private $urlValue;

    /**
     * The original URL passed into the setURL() method.
     *
     * @var string
     */
    private $urlOrig;

    /**
     * The setTransport method to be used.  Ex: http, https, ftp
     *
     * @var string
     */
    public $transport;

    /**
     * The setDomain name portion.  Ex: www.setDomain.com
     *
     * @var string
     */
    public $domain;

    /**
     * The directory of the URL
     *
     * @var string
     */
    public $dir;

    /**
     * File name being referenced
     *
     * @var string
     */
    public $fileName;

    /**
     * Key/value pairs to be added to the URL
     *
     * @var array
     */
    public $passVars;

    /**
     * Port number to be added to the URL.
     *
     * @var integer
     */
    public $port;

    /**
     * The setUser name to be used in the URL
     *
     * @var string
     */
    public $userName;

    /**
     * The password to be used along with the setUser name
     *
     * @var string
     */
    public $password;

    /**
     * The trailing portion of the URL that specifies an anchor name on a page.
     *
     * @var string
     */
    public $anchorName;

    /**
     * Can optionally take in a URL
     * If the URL string passed in is simply "ref" then the URL will be changed
     * to the referring page.
     *
     * @param string $url
     */
    public function __construct($url = '')
    {
        $this->passVars = array();

        $this->setURL($url);
    }

    /**
     * Provides the output for this class when the URL is being used as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->output();
    }

    /**
     * Assembles the URL parts and outputs the string
     *
     * @return string
     */
    public function output()
    {
        $this->assemble();

        return $this->urlValue;
    }

    /**
     * Will take apart the passed in URL and store its various components as
     * member variables.
     *
     * @param string
     * @throws \DomainException
     *
     * @return $this
     */
    public function setURL($url)
    {
        if ( strlen($url) == 0 )
        {
            return $this;
        }

        if ( strtolower($url) == 'ref' )
        {
            $this->setToReferrer();

            return $this;
        }

        $this->urlOrig = $url;

        $this->initializeUrlParts();

        $parts = parse_url($url);

        if ( $parts === false )
        {
            throw new \DomainException('Invalid URL');
        }

        if ( array_key_exists('scheme', $parts) )
        {
            $this->setTransport($parts['scheme']);
        }

        if ( array_key_exists('host', $parts) )
        {
            $this->setDomain($parts['host']);
        }

        if ( array_key_exists('port', $parts) )
        {
            $this->setPort($parts['port']);
        }

        if ( array_key_exists('path', $parts) )
        {
            $path = $parts['path'];
            $path = str_replace('\\', '/', $path);

            if ( substr($path, -1) == '/' )
            {
                $this->setDir($path);
            }
            else
            {
                $file = basename($path);
                $flen = strlen($file) * -1;
                $dir  = substr($path, 0, $flen);
                $this->setDir($dir);
                $this->setFile($file);
            }
        }

        if ( array_key_exists('query', $parts) )
        {
            $qp = explode('&', $parts['query']);

            foreach ( $qp as $keyval )
            {
                list($key, $val) = explode('=', $keyval);
                $this->passVars[ $key ] = $val;
            }
        }

        if ( array_key_exists('setUser', $parts) )
        {
            $this->setUser($parts['setUser']);
        }

        if ( array_key_exists('setPassword', $parts) )
        {
            $this->setPassword($parts['setPassword']);
        }

        if ( array_key_exists('fragment', $parts) )
        {
            $this->setAnchor($parts['fragment']);
        }

        return $this;
    }

    /**
     * Resets all the stored values about the URL to be reset to a fully empty
     * state.
     *
     */
    private function initializeUrlParts()
    {
        $this->urlValue   = null;
        $this->transport  = null;
        $this->domain     = null;
        $this->dir        = null;
        $this->fileName   = null;
        $this->port       = null;
        $this->userName   = null;
        $this->password   = null;
        $this->anchorName = null;
    }

    /**
     * Takes all the various parts of the URL this object holds and tries to get
     * something reasonable squeezed out of it.
     *
     */
    private function assemble()
    {
        $url = "";

        if ( $this->anchorName !== null )
        {
            $url .= '#' . $this->anchorName;
        }

        if ( count($this->passVars) > 0 )
        {
            $vars = '?';

            foreach ( $this->passVars as $key => $val )
            {
                $vars .= $key . '=' . $val;
                $vars .= '&';
            }

            $vars = substr($vars, 0, -1);

            $url = $vars . $url;
        }

        if ( strlen($this->fileName) > 0 )
        {
            $url = $this->fileName . $url;
        }

        if ( strlen($this->dir) > 0 )
        {
            if ( substr($this->dir, -1) == '/' )
            {
                $url = $this->dir . $url;
            }
            else
            {
                $url = $this->dir . '/' . $url;
            }
        }

        // Put together the setUser:setPassword portion ahead of time.  Only
        // gets added if both a setTransport and setDomain exist.
        $userPass = '';

        if ( strlen($this->userName) > 0 )
        {
            $userPass = $this->userName;

            if ( strlen($this->password) > 0 )
            {
                $userPass .= ':' . $this->password;
            }

            $userPass .= '@';
        }

        if ( strlen($this->transport) > 0 )
        {
            if ( strlen($this->domain) > 0 )
            {
                $dom = $this->domain;

                if ( intval($this->port) > 0 )
                {
                    $dom .= ":" . intval($this->port);
                }

                if ( strlen($this->dir) > 0 AND substr($this->dir, 0,
                                                       1) != '/'
                )
                {
                    $dom .= '/';
                }
                elseif ( strlen($this->dir) == 0 )
                {
                    $dom .= '/';
                }

                $url = $this->transport . '://' . $userPass . $dom . $url;
            }
            else
            {
                if ( $this->transport == 'setFile' )
                {
                    $url = $this->transport . '://' . $url;
                }
                else
                {
                    $url = $this->transport . ':' . $url;
                }
            }
        }

        // With no setDomain name, setTransport, setDir, or setFile value but we
        // do have parameters then the prefix of the URL needs to be set to "./"
        if ( !strlen($this->transport) OR !strlen($this->domain) )
        {
            if ( !strlen($this->dir) AND !strlen($this->fileName) )
            {
                if ( count($this->passVars) > 0 )
                {
                    $url = './' . $url;
                }
            }
        }

        $this->urlValue = $url;
    }

    /**
     * Redirects a web browser to the URL assembled by this object then exits
     * all code execution.
     *
     * @param integer
     */
    public function redirect($status = 302)
    {
        $this->assemble();

        header('Status: ' . intval($status));
        header('Location: ' . $this->urlValue);

        exit;
    }

    /**
     * Sets the URL to the HTTP Referrer
     *
     * @return $this
     */
    public function setToReferrer()
    {
        if ( array_key_exists('HTTP_REFERER', $_SERVER) )
        {
            $ref = $_SERVER['HTTP_REFERER'];
        }
        else
        {
            $ref = '/';
        }

        if ( strlen($ref) == 0 )
        {
            $this->setURL('/');
        }
        else
        {
            $this->setURL($ref);
        }

        return $this;
    }

    /**
     * Sets the setTransport to use
     *
     * @param string $transport
     *
     * @return $this
     */
    public function setTransport($transport)
    {
        $allowed = array('http', 'https', 'ftp', 'javascript', 'mailto', 'sftp',
                         'file');

        // Make sure to strip off any extra punctuation here
        if ( substr($transport, -3) == '://' )
        {
            $transport = substr($transport, 0, -3);
        }

        if ( substr($transport, -2) == ':/' )
        {
            $transport = substr($transport, 0, -2);
        }

        $transport = strtolower($transport);

        if ( !in_array($transport, $allowed) )
        {
            return $this;
        }

        $this->transport = $transport;

        return $this;
    }

    /**
     * Set the setDomain name
     *
     * @param string $dom
     *
     * @return $this
     */
    public function setDomain($dom)
    {
        $this->domain = str_replace(' ', '', $dom);

        return $this;
    }

    /**
     * Set the TCP/IP port for the connection.
     * If the value isn't greater than zero the port will be nulled out.
     *
     * @param integer $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = null;

        if ( intval($port) > 0 )
        {
            $this->port = intval($port);
        }

        return $this;
    }

    /**
     * Set the directory
     *
     * @param string
     *
     * @return $this
     */
    public function setDir($dir)
    {
        // A single dot is the same as no directory, so blank it out
        if ( $dir == '.' )
        {
            $dir = '';
        }

        // Replace any backslashes with forward ones
        $dir = str_replace('\\', '/', $dir);

        $this->dir = $dir;

        return $this;
    }

    /**
     * Specify the setFile to be pointed to.
     *
     * @param string $file
     *
     * @return $this
     */
    public function setFile($file)
    {
        $this->fileName = $file;

        return $this;
    }

    /**
     * User name to be placed in the URL
     *
     * @param string $userName
     *
     * @return $this
     */
    public function setUser($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * A plain text password that gets attached to the URL
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * The anchor name to be added to the end of the URL
     *
     * @param string $anchorName
     *
     * @return $this
     */
    public function setAnchor($anchorName)
    {
        $this->anchorName = $anchorName;

        return $this;
    }

    /**
     * Adds a key/value pair to the URL
     *
     * @param string
     * @param string
     *
     * @return $this
     */
    public function addParam($key, $value)
    {
        $this->passVars[ $key ] = urlencode($value);

        return $this;
    }

    /**
     * Adds all the parameters found in the $_GET variable if it hasn't already
     * been added.
     *
     * @return $this
     */
    public function addGetParams()
    {
        foreach ( $_GET as $key => $val )
        {
            if ( !array_key_exists($key, $this->passVars) )
            {
                $this->addParam($key, $val);
            }
        }

        return $this;
    }

    /**
     * Provides the value of a parameter, if one exists.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getParam($key)
    {
        $rtn = null;

        if ( array_key_exists($key, $this->passVars) )
        {
            $rtn = $this->passVars[ $key ];
        }

        return $rtn;
    }

    /**
     * Removes a key/value pair from the URL based on the key
     *
     * @param string $key
     *
     * @return $this
     */
    public function delParam($key)
    {
        if ( array_key_exists($key, $this->passVars) )
        {
            unset($this->passVars[ $key ]);
        }

        return $this;
    }

    /**
     * Clear out all the parameters from a URL.
     *
     * @return $this
     */
    public function delAllParam()
    {
        $this->passVars = array();
    }
}

<?php
/* vim: set ts=4 sw=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Mika Tuupola <tuupola@appelsiini.net>                        |
// +----------------------------------------------------------------------+
//
// $Id: HtAccess.php 151 2006-09-08 17:11:50Z jfiat $
//

require_once 'PEAR.php' ;

/**
* Class for manipulating .htaccess files
*
* A class which provided common methods to manipulate Apache / NCSA
* style .htaccess files.
*
* Example 1 (modifying existing file):
*
* $h = new File_HtAccess('.htaccess');
* $h->load();
* $h->setRequire('valid-user');
* $h->save();
*
* Example 2 (modifying existing file):
*
* $h = new File_HtAccess('.htaccess');
* $h->load();
* $h->addRequire('newuser');
* $h->save();
*
* Example 3 (creating a new file):
*
* $params['authname']      = 'Private';
* $params['authtype']      = 'Basic';
* $params['authuserfile']  = '/path/to/.htpasswd';
* $params['authgroupfile'] = '/path/to/.htgroup';
* $params['require']       = array('group', 'admins');
*
* $h = new File_HtAccess('/path/to/.htaccess', $params);
* $h->save();
*
* @author  Mika Tuupola <tuupola@appelsiini.net>
* @access  public
* @version 1.0.0
* @package File_HtAccess
* @category File
*/

class File_HtAccess {

    var $file;
    var $authname;
    var $authtype;
    var $authuserfile;
    var $authgroupfile;
    var $authdigestfile;
    var $authdigestgroupfile;
    var $require    = array();
    var $additional = array();

    /**
    * Constructor
    *
    * @access public
    * @param  string $file
    * @param  array  $params 
    * @return object File_HtAccess
    */
       
    function File_HtAccess($file='.htaccess', $params='') {

        $this->file = $file;
        $this->setProperties($params);

    }
    
    /**
    * Load the given .htaccess file
    *
    * @access public
    * @return mixed   true on success, PEAR_Error on failure
    */

    function load() {

        $retval = true;
        
        $fd = @fopen($this->getFile(), 'r');
        if ($fd) {
            while ($buffer = fgets($fd, 4096)) {
                $buffer = trim($buffer);
                if ($buffer) {
                    $data = split(' ', $buffer, 2);
                    if (preg_match('/AuthName/i', $data[0])) {
                       $this->setAuthName($data[1]);

                    } elseif (preg_match('/AuthType/i', $data[0])) {
                       $this->setAuthType($data[1]);                
                    
                    } elseif (preg_match('/AuthUserFile/i', $data[0])) {
                       $this->setAuthUserFile($data[1]);            
                           
                    } elseif (preg_match('/AuthGroupFile/i', $data[0])) {
                       $this->setAuthGroupFile($data[1]);

                    } elseif (preg_match('/AuthDigestFile/i', $data[0])) {
                       $this->setAuthDigestFile($data[1]);

                    } elseif (preg_match('/AuthDigestGroupFile/i', $data[0])) {
                       $this->setAuthDigestGroupFile($data[1]);

                    } elseif (preg_match('/Require/i', $buffer)) {
                       $require = split(' ', $data[1]);
                       $this->setRequire($require);

                    } elseif (trim($buffer)) {
                       $this->addAdditional($buffer);
                    }
                }
            }
            fclose($fd);

        } else {
            $retval = PEAR::raiseError('Could not open ' . $this->getFile() . 
                                       ' for reading.');
        }

        return($retval);

    }

    /**
    * Set class properties
    *
    * @access public
    * @param  array  $params
    */

    function setProperties($params) {
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $method = 'set' . $key;
                $this->$method($value);
            }
        }
    }
    
    /**
    * Set the value of authname property
    * 
    * @access public
    * @param  string $name
    */

    function setAuthName($name='Restricted') {
        $this->authname = $name;
    }

    /**
    * Set the value of authtype propery
    *
    * @access public
    * @param  string $type
    */

    function setAuthType($type='Basic') {
        $this->authtype = $type;
    }

    /**
    * Set the value of authuserfile propery
    *
    * @access public
    * @param  string $file
    */

    function setAuthUserFile($file='') {
        $this->authuserfile = $file;
    }

    /**
    * Set the value of authgroupfile property
    *
    * @access public
    * @param  string $file
    */

    function setAuthGroupFile($file='') {
        $this->authgroupfile = $file;
    }

    /**
    * Set the value of authdigestfile property
    *
    * @access public
    * @param  string $file
    */

    function setAuthDigestFile($file='') {
        $this->authdigestfile = $file;
    }

    /**
    * Set the value of authdigestgroupfile property
    *
    * @access public
    * @param  string $file
    */

    function setAuthDigestGroupFile($file='') {
        $this->authdigestgroupfile = $file;
    }

    /**
    * Set the value of require property
    *
    * Parameter can be given as an array or string. If given as a string
    * the value will be exploded in to an array by using space as a 
    * separator.
    *
    * @access public
    * @param  mixed $require
    */

    function setRequire($require='') {
        if (is_array($require)) {
            $this->require = $require;
        } else {
            $this->require = explode(' ', $require);
        }
    }

    /**
    * Add a value to require property
    *
    * @access public
    * @param  string $require
    */
    function addRequire($require) {
        $this->require[] = $require;
    }

    /**
    * Delete a value from require property
    *
    * @access public
    * @param  string $require
    */
    function delRequire($require) {
        $pos = array_search($require, $this->require);
        unset($this->require[$pos]);
    }

    /**
    * Set the value of additional property
    *
    * Additional property is used for all the extra things in .htaccess
    * file which don't have specific accessor method for them. 
    *
    * @access public
    * @param  array  $additional
    */

    function setAdditional($additional='') {
        $this->additional = (array)$additional;
    }

    /**
    * Add a value to additional property
    *
    * @access public
    * @param  string $additional
    */

    function addAdditional($additional='') {
        $this->additional[] = $additional;
    }
    
    /**
    * Set the value of file property
    *
    * @access public
    * @param  file   $file
    */

    function setFile($file) {
        $this->file = $file;
    }
    
    /**
    * Get the value of authname property
    *
    * @access public
    * @return string  
    */

    function getAuthName() {
        return($this->authname);
    }

    /**
    * Get the value of authtype property
    *
    * @access public
    * @return string  
    */

    function getAuthType() {
        return($this->authtype);
    }
    
    /**
    * Get the value of authuserfile property
    *
    * @access public
    * @return string  
    */

    function getAuthUserFile() {
        return($this->authuserfile);
    }

    /**
    * Get the value of authgroupfile property
    *
    * @access public
    * @return string  
    */


    function getAuthGroupFile() {
        return($this->authgroupfile);
    }

    /**
    * Get the value of authdigestfile property
    *
    * @access public
    * @return string  
    */

    function getAuthDigestFile() {
        return($this->authdigestfile);
    }

    /**
    * Get the value of authdigestgroupfile property
    *
    * @access public
    * @return string  
    */

    function getAuthDigestGroupFile() {
        return($this->authdigestgroupfile);
    }

    /**
    * Get the value(s) of require property
    *
    * @access public
    * @param  string $type whether to return an array or string
    * @return mixed  string or array, defaults to an array  
    */
 
    function getRequire($type='') {
        $retval = $this->require;

        if ($type == 'string') {
            $retval = implode($retval, ' ');
        }
        return($retval);
    }

    /**
    * Get the value(s) of additional property
    *
    * @access public
    * @param  string $type whether to return an array or string
    * @return mixed  string or array, defaults to an array  
    */

    function getAdditional($type='') {
        $retval = $this->additional;

        if ($type == 'string') {
            $retval = implode($retval, "\n");
        }
        return($retval);
    }

    /**
    * Get the value of file property
    *
    * @access public
    * @return string  
    */

    function getFile() {
        return($this->file);
    }

    /**
    * Save the .htaccess file
    *
    * @access public
    * @return mixed      true on success, PEAR_Error on failure
    * @see    PEAR_Error
    */

    function save() {

        $retval = true;

        $str  = 'AuthName '     . $this->getAuthName() . "\n";
        $str .= 'AuthType '     . $this->getAuthType() . "\n";

        if ('basic' == strtolower($this->getAuthType())) {
            $str .= 'AuthUserFile ' . $this->getAuthUserFile() . "\n";
            if (trim($this->getAuthGroupFile())) {
                $str .= 'AuthGroupFile ' . $this->getAuthGroupFile() . "\n";   
            }
        } elseif ('digest' == strtolower($this->getAuthType())) {
            $str .= 'AuthDigestFile ' . $this->getAuthDigestFile() . "\n";
            if (trim($this->getAuthDigestGroupFile())) {
                $str .= 'AuthDigestGroupFile ' . $this->getAuthDigestGroupFile() . "\n";   
            }
        }

        $str .= 'Require ' . $this->getRequire('string') . "\n";
        $str .= $this->getAdditional('string') . "\n";
        
        $fd = @fopen($this->getFile(), 'w');
        if ($fd) {
            fwrite($fd, $str, strlen($str));
        } else {
            $retval = PEAR::raiseError('Could not open ' . $this->getFile() . 
                                       ' for writing.');

        }

        return($retval);
        
    }

}

?>

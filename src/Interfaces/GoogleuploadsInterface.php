<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * Interface
 *
 * PHP version 5
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
namespace FgBackups\Interfaces;

/**
 * GoogleuploadsInterface
 *
 * @category Interface
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
interface GoogleuploadsInterface
{
     /**
      * Return configuration id
      *
      * @return int
      */
     public function getId();

     /**
      * Return backup name
      *
      * @return string
      */
     public function getBackup();

     /**
      * Return virtual host id
      *
      * @return int
      */
     public function getVhost();
	 
     /**
      * Return date upload
      *
      * @return time
      */
     public function getDateupload();
	 
     /**
      * Return size
      *
      * @return int
      */
     public function getSize();
	 
	 
     /**
      * Return fileid
      *
      * @return int
      */
     public function getFileid();
	 
 	 
     /**
      * Set id
	  *
      * @param int $value Value
      */
     public function setId($value);

     /**
      * Set backup file name
      *
      * @param string $value Value
      */
     public function setBackup($value);

     /**
      * Set vhost id
      *
      * @param int $value Value
      */
     public function setVhost($value);
	 
     /**
      * Set timesaved
      *
      * @param int $value Value
      */
     public function setDateupload($value);
	 
     /**
      * Set size
      *
      * @param int $value Value
      */
     public function setSize($value);
	 
	 
     /**
      * Set file id
      *
      * @param int $value Value
      */
     public function setFileid($value);
}
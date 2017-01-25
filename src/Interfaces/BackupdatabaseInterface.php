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
 * BackupdatabaseInterface
 *
 * @category Interface
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
interface BackupdatabaseInterface
{
     /**
      * Return configuration id
      *
      * @return int
      */
     public function getId();

     /**
      * Return virtual host id
      *
      * @return int
      */
     public function getVhost();

     /**
      * Return database name
      *
      * @return string
      */
     public function getDatabase(); 
 	 
     /**
      * Set id
	  *
      * @param int $value Value
      */
     public function setId($value);

     /**
      * Set vhost id
      *
      * @param int $value Value
      */
     public function setVhost($value);
	 
     /**
      * Set database name
      *
      * @param string $value Value
      */
     public function setDatabase($value);
}
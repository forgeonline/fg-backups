<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * Manager
 *
 * PHP version 5
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
namespace FgBackups\Model;

use FgBackups\Interfaces\BackupdatabaseInterface;
/**
 * Backupdatabase Model
 *
 * @category Model
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class Backupdatabase implements BackupdatabaseInterface
{
	/*
	* @var int
	*/
	protected $id;
	
	/*
	* @var int
	*/
	protected $vhost;
	
	/*
	* @var string
	*/
	protected $database;
	

	 /**
	 * {@inheritDoc}
	 */
     public function getId()
	 {
		 return $this->id;
	 }
	 
	 /**
	 * {@inheritDoc}
	 */
     public function getVhost()
	 {
		 return $this->vhost;
	 }
	 
	 /**
	 * {@inheritDoc}
	 */
     public function getDatabase()
	 {
		 return $this->database;
	 }	 
	 
	/**
	* {@inheritDoc}
	*/
	public function setId($value)
	{
		$this->id = $value;
	}
	
	/**
	* {@inheritDoc}
	*/
	public function setVhost($value)
	{
		$this->vhost = $value;
	}
	
	/**
	* {@inheritDoc}
	*/
	public function setDatabase($value)
	{
		$this->database = $value;
	}
}
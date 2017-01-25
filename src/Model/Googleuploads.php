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

use FgBackups\Interfaces\GoogleuploadsInterface;
/**
 * Googleuploads Model
 *
 * @category Model
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class Googleuploads implements GoogleuploadsInterface
{
	/*
	* @var int
	*/
	protected $id;
	
	/*
	* @var string
	*/
	protected $backup;
	
	/*
	* @var int
	*/
	protected $vhost;
	
	/*
	* @var int
	*/
	protected $dateupload;
	
	/*
	* @var int
	*/
	protected $size;
	
	/*
	* @var int
	*/
	protected $fileid;
	

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
     public function getBackup()
	 {
		 return $this->backup;
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
     public function getDateupload()
	 {
		 return $this->dateupload;
	 }	 
	 
	 /**
	 * {@inheritDoc}
	 */
     public function getSize()
	 {
		 return $this->size;
	 }
	 
	 /**
	 * {@inheritDoc}
	 */
     public function getFileid()
	 {
		 return $this->fileid;
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
	public function setBackup($value)
	{
		$this->backup = $value;
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
	public function setDateupload($value)
	{
		$this->dateupload = $value;
	}
	
	/**
	* {@inheritDoc}
	*/
	public function setSize($value)
	{
		$this->size = $value;
	}
	
	/**
	* {@inheritDoc}
	*/
	public function setFileid($value)
	{
		$this->fileid = $value;
	}
}
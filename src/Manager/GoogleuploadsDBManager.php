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
namespace FgBackups\Manager;

use Interop\Container\ContainerInterface;
use FgBackups\Mapper\DbGoogleuploadsMapper;
/**
 * VhostsDBManager Class
 *
 * @category Manager
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class GoogleuploadsDBManager
{
	/*
	* @var FgBackups\Mapper\DbGoogleuploadsMapper
	*/
	protected $googleuploadMapper;
	
	public function __construct(DbGoogleuploadsMapper $googleuploadMapper)
	{
		$this->googleuploadMapper = $googleuploadMapper;
	}

	/**
	* {@inheritDoc}
	*/
	public function listhosts($offset=0, $limit=20)
	{
		return $this->googleuploadMapper->listhosts($offset, $limit);
	}
	
	/**
	* {@inheritDoc}
	*/
	public function findByName($name, $list=false, $source='')
	{
		return $this->googleuploadMapper->findByName($name,$list,$source);	
	}
	
	/**
	* {@inheritDoc}
	*/
	public function save(\FgBackups\Interfaces\GoogleuploadsInterface $googleupload)
	{
		return $this->googleuploadMapper->save($googleupload);	
	}
}
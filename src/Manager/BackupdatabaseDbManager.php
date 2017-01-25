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
use FgBackups\Mapper\DbBakupdatabaseMapper;
/**
 * BackupdatabaseDbManager Class
 *
 * @category Manager
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class BackupdatabaseDbManager
{
	/*
	* @var FgBackups\Mapper\DbBakupdatabaseMapper
	*/
	protected $databasedbMapper;
	
	public function __construct(DbBakupdatabaseMapper $databasedbMapper)
	{
		$this->databasedbMapper = $databasedbMapper;
	}

	/**
	* {@inheritDoc}
	*/
	public function listhosts($offset=0, $limit=20)
	{
		return $this->databasedbMapper->listhosts($offset, $limit);
	}
	
	/**
	* {@inheritDoc}
	*/
	public function findByName($name, $list=false, $source='')
	{
		return $this->databasedbMapper->findByName($name,$list,$source);	
	}
	
	/**
	* {@inheritDoc}
	*/
	public function findByVhost($name, $list=false)
	{
		return $this->databasedbMapper->findByVhost($name,$list);	
	}
	
	/**
	* {@inheritDoc}
	*/
	public function save(\FgBackups\Interfaces\BackupdatabaseInterface $databasedbMapper)
	{
		return $this->databasedbMapper->save($databasedbMapper);	
	}
}
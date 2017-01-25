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
 * DbBakupdatabaseMapperInterface
 *
 * @category Interface
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
interface DbBakupdatabaseMapperInterface
{
 
     /**
      * Find uploads by filename
      *
	  * @param string $name
	  * @param boolean $list
	  *
	  * @return ConfigurationInterface
      * @throws \InvalidArgumentException
      */
     public function findByName($name, $list, $source);

     /**
      * List uploads
      *
	  * @param int   $start Start
	  * @param limit $limit Limit
	  *
	  * @return CustomersInterface
      * @throws \InvalidArgumentException
      */
     public function listhosts($offset, $limit);

     /**
      * Save upload
      *
	  * @param \FgBackups\Interfaces\BackupdatabaseInterface $backupdatabase
	  *
	  * @return BackupdatabaseInterface
      * @throws \InvalidArgumentException
      */
     public function save(\FgBackups\Interfaces\BackupdatabaseInterface $backupdatabase);
}
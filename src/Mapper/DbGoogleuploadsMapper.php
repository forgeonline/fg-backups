<?php
/**
 * Zend Backend (http://forge.co.nz/)
 *
 * Mapper
 *
 * PHP version 5
 *
 * @category Module
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
namespace FgBackups\Mapper;

use FgBackups\Interfaces\DbGoogleuploadsMapperInterface;
use FgBackups\Interfaces\GoogleuploadsInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Hydrator\HydratorInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Hydrator\Reflection as ReflectionHydrator;
use FgBackups\Model\Googleuploads;

/**
 * DbGoogleuploadsMapper Mapper
 *
 * @category Mapper
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class DbGoogleuploadsMapper
implements DbGoogleuploadsMapperInterface
{
	/**
	* @var \Zend\Db\Adapter\AdapterInterface
	*/
	protected $dbAdapter;
	
	/**
	* @var \Zend\Stdlib\Hydrator\HydratorInterface
	*/
	protected $hydrator;
	
	/**
	* @var \FgBackups\Interfaces\GoogleuploadsInterface
	*/
	protected $googleuploads;
	
	/**
	* @param AdapterInterface  $dbAdapter
	* @param HydratorInterface $hydrator
	* @param PostInterface    $postPrototype
	*/
	public function __construct(
		AdapterInterface $dbAdapter,
		HydratorInterface $hydrator,
		GoogleuploadsInterface $googleuploads
	) {
		$this->dbAdapter      = $dbAdapter;
		$this->hydrator       = $hydrator;
		$this->googlemapPrototype  = $googleuploads;
	}

	/*
	* @param string $name Name
	*
	* @return GoogleuploadsInterface
	* @throws \InvalidArgumentException
	*/
	public function findByName($name, $list=false, $source='')
	{
		if (empty($name)) {
			return false;
		}
		$sql    = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select->from('googleuploads');
		$select->where(['backup = ?' => $name]);
		if(!empty($source)){
			$select->where(['vhost = ?' => $source]);
		}

		if ($list) {
			$buildsql = $sql->buildSqlString($select);
//			 \Zend\Debug\Debug::dump($buildsql);
			$results = $this->dbAdapter->query(
				$buildsql,
				$this->dbAdapter::QUERY_MODE_EXECUTE
			)->toArray();;
			if($results) {
				return $results;
			}
		} else {
			$select->limit(1);
			$buildsql = $sql->buildSqlString($select);
			$result = $this->dbAdapter->query(
				$buildsql,
				$this->dbAdapter::QUERY_MODE_EXECUTE
			)->toArray();
			if ($result) {
				return $this->hydrator->hydrate($result[0], $this->googlemapPrototype);
			}
		}
		return false;
	}
	
	/**
	* {@inheritDoc}
	*/
	public function save(\FgBackups\Interfaces\GoogleuploadsInterface $googleupload)
	{
		if($googleupload) {
			$sql    = new Sql($this->dbAdapter);
			if(!empty($googleupload->getId())){
				$query = $sql->update('googleuploads');
				$query->set(
					[
						'backup' => $googleupload->getBackup(),
						'vhost' => $googleupload->getVhost(),
						'size' => $googleupload->getSize(),
						'fileid' => $googleupload->getFileid(),
					]
				);
				if(!empty($googleupload->getDateupload())) {
					$query->set(
						[
							'dateupload' => $googleupload->getDateupload(),
						]
					);
				}
				$query->where(['id = ?' => $googleupload->getId()]);
			} else {
				$query = $sql->insert('googleuploads');
				$query->columns(
					[
						$googleupload->getBackup() => 'backup',
						$googleupload->getVhost() => 'vhost',
						$googleupload->getSize() => 'size',
						$googleupload->getFileid() => 'fileid',
					]
				);
				if(!empty($googleupload->getDateupload())) {
					$query->columns(
						[
							$updatedDate => 'dateupload',
						]
					);
				}
			}
			$buildsql = $sql->buildSqlString($query);
			$result = $this->dbAdapter->query(
				$buildsql,
				$this->dbAdapter::QUERY_MODE_EXECUTE
			);
			if($result) {
				return true;
			}
			return false;
		}
	}

	/**
	* {@inheritDoc}
	*/
	public function listhosts($offset, $limit)
	{
		$sql    = new Sql($this->dbAdapter);
		$select = $sql->select('googleupload');
		$select->limit($limit);
		if($offset) {
			$select->offset($offset);
		}
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if ($result instanceof ResultInterface && $result->isQueryResult()) {
            $resultSet = new HydratingResultSet(new ReflectionHydrator, new Vhosts());
            $resultSet->initialize($result);
            return $resultSet;
        }
		return false;
	}

}
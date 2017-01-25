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

use FgBackups\Interfaces\DbBakupdatabaseMapperInterface;
use FgBackups\Interfaces\BackupdatabaseInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Hydrator\HydratorInterface;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Hydrator\Reflection as ReflectionHydrator;
use FgBackups\Model\Backupdatabase;

/**
 * DbBakupdatabaseMapper Mapper
 *
 * @category Mapper
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class DbBakupdatabaseMapper
implements DbBakupdatabaseMapperInterface
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
	* @var \FgBackups\Interfaces\BackupdatabaseInterface
	*/
	protected $backupdatabase;
	
	/**
	* @param AdapterInterface  $dbAdapter
	* @param HydratorInterface $hydrator
	* @param BackupdatabaseInterface    $postPrototype
	*/
	public function __construct(
		AdapterInterface $dbAdapter,
		HydratorInterface $hydrator,
		BackupdatabaseInterface $backupdatabase
	) {
		$this->dbAdapter      = $dbAdapter;
		$this->hydrator       = $hydrator;
		$this->backupdatabase  = $backupdatabase;
	}

	/*
	* @param string $name Name
	*
	* @return backupdatabaseInterface
	* @throws \InvalidArgumentException
	*/
	public function findByName($name, $list=false, $source='')
	{
		if (empty($name)) {
			return false;
		}
		$sql    = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select->from('backupdatabase');
		$select->where(['`database` = ?' => $name]);
		if(!empty($source)){
			$select->where(['vhost = ?' => $source]);
		}

		if ($list) {
			$buildsql = $sql->buildSqlString($select);
			$results = $this->dbAdapter->query(
				$buildsql,
				$this->dbAdapter::QUERY_MODE_EXECUTE
			)->toArray();
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
			//\Zend\Debug\Debug::dump($buildsql);
			if ($result) {
				return $this->hydrator->hydrate($result[0], $this->backupdatabase);
			}
		}
		return false;
	}
	
	/*
	* @param string $name Name
	*
	* @return backupdatabaseInterface
	* @throws \InvalidArgumentException
	*/
	public function findByVhost($name, $list=false)
	{
		if (empty($name)) {
			return false;
		}
		$sql    = new Sql($this->dbAdapter);
		$select = $sql->select();
		$select->from('backupdatabase');
		$select->where(['`vhost` = ?' => $name]);

		if ($list) {
			$buildsql = $sql->buildSqlString($select);
			//\Zend\Debug\Debug::dump($buildsql);
			$results = $this->dbAdapter->query(
				$buildsql,
				$this->dbAdapter::QUERY_MODE_EXECUTE
			)->toArray();
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
				return $this->hydrator->hydrate($result[0], $this->backupdatabase);
			}
		}
		return false;
	}
	
	/**
	* {@inheritDoc}
	*/
	public function save(\FgBackups\Interfaces\BackupdatabaseInterface $databasedbMapper)
	{
		if($databasedbMapper) {
			
			$sql    = new Sql($this->dbAdapter);
			if(!empty($databasedbMapper->getId())){
				$query = $sql->update('backupdatabase');
				$query->set(
					[
						'vhost' => $databasedbMapper->getVhost(),
						'database' => $databasedbMapper->getDatabase(),
					]
				);
				$query->where(['id = ?' => $databasedbMapper->getId()]);
			} else {
				$query = $sql->insert('backupdatabase');
				$query->columns(
					[
						$databasedbMapper->getVhost() => 'vhost',
						$databasedbMapper->getDatabase() => 'database',
					]
				);
			}
			$buildsql = $sql->buildSqlString($query);
			//\Zend\Debug\Debug::dump($buildsql);die();
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
		$select = $sql->select('backupdatabase');
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
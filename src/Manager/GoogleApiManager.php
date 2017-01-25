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
use FgCore\Model\zbeMessage;
use Zend\Session\Container;

/**
 * GoogleApiManager Class
 *
 * @category Manager
 * @package  FgBackups
 * @author   Don Nuwinda <nuwinda@gmail.com>
 * @license  GPL http://forge.co.nz
 * @link     http://forge.co.nz
 */
class GoogleApiManager
{
	/*
	* @var Interop\Container\ContainerInterface
	*/
	protected $container;
	
	protected $config;
	
	protected $client;
	
	protected $service;
	
	protected $drive;
	
    /**
     * Constructor
     *
     * @param ServiceLocatorInterface $servicelocator ServiceLocatorInterface
     *
     * @return void
     */
    public function __construct(
		ContainerInterface $container
	){
        $this->container = $container;
		$this->config = $this->container->get('FgCore\Service\ConfigurationFactory')
						->getConfiguration();
    }
	
	protected function getScopes()
	{
		$scopearray = array(
				\Google_Service_Drive::DRIVE_METADATA_READONLY,
				\Google_Service_Drive::DRIVE,
				\Google_Service_Drive::DRIVE_FILE,
				\Google_Service_Drive::DRIVE_APPDATA
			) ; 
		return implode(" ", $scopearray );
	}
	
	public function getClient()
	{	
		$message = new zbeMessage();
		$client = new \Google_Client();
		$scops = $this->getScopes();
		
		$client->setApplicationName($this->config['googleapplicationname']);
		$client->setScopes($scops);	
		$client->setAuthConfig($this->config['googleclientsecretpath']);
		$client->setAccessType('offline');
	
		// Load previously authorized credentials from a file.
		$credentialsPath = $this->expandHomeDirectory($this->config['googlecredentialspath']);

		if (! file_exists($credentialsPath)) {		//\Zend\Debug\Debug::dump($credentialsPath);die();
			$authUrl = $client->createAuthUrl();
			$session = new Container('authurl');
			$session->authUrl = $authUrl;
			return false;
		}

		if (file_exists($credentialsPath)) {
			$accessToken = json_decode(file_get_contents($credentialsPath), true);
		}
		$client->setAccessToken($accessToken);		
		// Refresh the token if it's expired.

		if ($client->isAccessTokenExpired()) {
			if(!$client->getRefreshToken()){
				$authUrl = $client->createAuthUrl();
				$session = new Container('authurl');
				$session->authUrl = $authUrl;
				return false;
			}
			$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
			file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
		}
		$this->client = $client;
		return $this->client;
	}
	

	public function createBackupLocation($directorylist)
	{
		$directoryNew = array();
		$service = new \Google_Service_Drive($this->client);
		foreach($directorylist as $value ){
			$folder = new \Google_Service_Drive_DriveFile(array(
			  'name' => str_replace(".","_", $value ),
			  'mimeType' => 'application/vnd.google-apps.folder',
			  'parents' => 
					array(
						$this->config['backupsgoogleparentdirectoryid']
					)
				)
			);
			$file = $service->files->create(
				$folder, 
				array(
					'fields' => 'id'
				)
			);
			if($file){
				$directoryNew[$value] = $file->id;
			}
		}
		return $directoryNew;
	}
	
	public function insertFile($host, $path,$filedetail, $size)
	{
		$this->client = new \Google_Client();
		$scops = $this->getScopes();
		
		$this->client->setApplicationName($this->config['googleapplicationname']);
		$this->client->setScopes($scops);	
		$this->client->setAuthConfig($this->config['googleclientsecretpath']);
		$this->client->setAccessType('offline');
		
		// Load previously authorized credentials from a file.
		$credentialsPath = $this->expandHomeDirectory($this->config['googlecredentialspath']);
		if (file_exists($credentialsPath)) {
			$accessToken = json_decode(file_get_contents($credentialsPath), true);
		}
		$this->client->setAccessToken($accessToken);	
		if ($this->client->isAccessTokenExpired()) {
			$this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
			file_put_contents($credentialsPath, json_encode($this->client->getAccessToken()));
		}
		
		
		$uploadType = $this->selectUploadType($size);
		$service = new \Google_Service_Drive($this->client);
		$folder = new \Google_Service_Drive_DriveFile(array(
		  'name' => $filedetail->getFilename(),
		  'parents' => 
				array(
					$host["backupkey"]
				)
			)
		);
		//$fh = fopen($path, 'r'); 
		//$data = fread($fh, filesize($path)); 
		//fclose($fh); 
		$data = file_get_contents($path);
		$file = $service->files->create(
			$folder,
			array(
				'data' => $data,
				'mimeType' => 'application/octet-stream',
				'uploadType' => $this->selectUploadType($size)
			)
		);
		
		if($file){
			return $file;
		}
		return false;
	}
	
	protected function selectUploadType($size)
	{
		$type = 'media';
		switch($size){
			case ( $size>((1024*1000)*5) ):
					$type = 'resumable';
				break;
			default:	
				break;
		}
		return $type;
	}
	
	public function updateAccessToken($authCode)
	{
		$message = new zbeMessage();
		$client = new \Google_Client();
		$scops = $this->getScopes();
		$client->setApplicationName($this->config['googleapplicationname']);
		$client->setScopes($scops);	
		$client->setAuthConfig($this->config['googleclientsecretpath']);
		$client->setAccessType('offline');
		$accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
		$credentialsPath = $this->expandHomeDirectory($this->config['googlecredentialspath']);
		if($accessToken["error"]) {
			$message->setError($accessToken["error_description"]);	
			return false;
		}
		// Store the credentials to disk.
		try{
			if (! file_exists(dirname($credentialsPath))) {
				mkdir(dirname($credentialsPath), 0755, true);
			}
			$return = file_put_contents($credentialsPath, json_encode($accessToken));
			
			if ($return!==FALSE) {
				$message->setSuccess('Access token is saved');				
				return true;
			}
			return false;
		} catch(Exception $e) {
			$message->setError($e->getMessage());
		}
		return false;
	}
	
	/**
	 * Expands the home directory alias '~' to the full path.
	 * @param string $path the path to expand.
	 * @return string the expanded path.
	 */
	protected function expandHomeDirectory($path) {
		return $path;
	}
	
	public function checkGoogleDriveConnection()
	{
		if ($this->config['googleapplicationname']) {
			$client = new \Google_Client();
			$scops = $this->getScopes();

		
			$client->setApplicationName($this->config['googleapplicationname']);
			$client->setScopes($scops);	
			$client->setAuthConfig($this->config['googleclientsecretpath']);
			$client->setAccessType('offline');
			// Load previously authorized credentials from a file.
			$credentialsPath = $this->expandHomeDirectory($this->config["googlecredentialspath"]);
			
			if (! file_exists($credentialsPath)) {
				mail(
					$this->config['serveradminemail'],
					'Missing Google Drive API file',
					sprintf('Please login to %s, and verify google drive api', $this->config['servername'])
				);
				return false;
			} else {
				$accessToken = json_decode(file_get_contents($credentialsPath), true);var_dump($accessToken);
				$client->setAccessToken($accessToken);
	
				if ($client->isAccessTokenExpired()) {
					if(!$client->getRefreshToken()){
						mail(
							$this->config['serveradminemail'],
							'Missing Google Drive API file',
							sprintf('Please login to %s, and verify google drive api', $this->config['servername'])
						);
						die("Process stop. verify google drive api");
					}
					
					$client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
					file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
					printf('Access token update success '. $_SERVER['SERVER_ADMIN']);
				}
				$this->client = $client;
				return $client;
			}
		}
		return false;
	}
}
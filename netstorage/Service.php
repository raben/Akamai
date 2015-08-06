<?php
/**
 * Akamai NetStorage HTTP-CMS API
 *
 * Akamai NetStorage HTTP-CMS API
 * It is implemented in accordance with the document the following specifications:
 * https://control.akamai.com/dl/customers/NS/NetStrgHttpCM.pdf
 * April 8, 2013
 *
 * PHP versions 5.2.11+
 *
 * @author	Arita Yuuki <rabe.ame@gmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version	CVS: <?php
 $
 ?> Id:$
 * @see https://control.akamai.com/dl/customers/NS/NetStrgHttpCM.pdf
 */

/**
 * Akamai_Netstorage_Service Class
 *
 * Akamai NetStorage HTTP-CMS API
 *
 * @package	Akamai/Netstorage
 * @author	Arita Yuuki <rabe.ame@gmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version	Release: @package_version@
 * @see		tests/Netstorage_Service_test.phpt
 **/
class Akamai_Netstorage_Service
{
	public $host;
	public $auth;
	public $version = 1;

	private $_http;
	private $_last_status_code = null;

	public function __construct($host){
		$this->host = $host;
	}

	public function authorize($key, $key_name, $version = 5){
		$this->auth = new Akamai_Netstorage_Authorize($key, $key_name, $version);
	}

	public function download($url){
		return $this->_readOnlyAction('download', $url);
	}

	public function du($url){
		return $this->_readOnlyAction('du', $url);
	}

	public function dir($url){
		return $this->_readOnlyAction('dir', $url);
	}

	public function stat($url){
		return $this->_readOnlyAction('stat', $url);
	}

	private function _readOnlyAction($action, $url){
		if(!$this->auth) throw new Exception('it is not authorized yet.');

		$action_string = 'version='.$this->version;
		$action_string .= '&action='.$action;

		if($action != 'download') {
			$action_string .= "&format=xml";
		}

		$auth_data	= $this->auth->getAuthData();
		$auth_sign	= $this->auth->getAuthSign($url, $action_string);
		
		$headers	= array(
			"Host: " . $this->host,
			"Accept:",
			"Accept-Encoding: identity",
			"X-Akamai-ACS-Auth-Data: {$auth_data}",
			"X-Akamai-ACS-Auth-Sign: {$auth_sign}",
			"X-Akamai-ACS-Action: {$action_string}"
		);

		return $this->request('GET', $url, null, $headers);
	}

	public function mtime($url, $time){
		$this->_updateAction('mtime', $url, array('mtime' => $time));
	}

	public function rename($url, $destination){
		$this->_updateAction('rename', $url, array('destination' => $destination));
	}

	public function upload($url, $body){
		$this->_updateAction('upload', $url, array('body' => $body));
	}

	public function symlink($url, $target){
		$this->_updateAction('symlink', $url, array('target' => $target));
	}

	public function mkdir($url){
		$this->_updateAction('mkdir', $url);
	}

	public function rmdir($url){
		$this->_updateAction('rmdir', $url);
	}

	public function delete($url){
		$this->_updateAction('delete', $url);
	}

	/**
	 * quick_delete
	 *
         * Used to perform a “quick-delete” of a selected directory (including all of its contents).
	 * NOTE: The “quick-delete” action is disabled by default for security reasons, as it allows recursive 
	 *       removal of non-empty directory structures in a matter of seconds. If you wish to enable this feature, 
         *       please contact your Akamai Representative with the NetStorage CPCode(s) for which you wish to 
         *       use this feature.
	 */
	public function quick_delete($url, $qd_confirm){
		$this->_updateAction('quick-delete', $url, array('qd_confirm' => $qd_confirm));
	}

	private function _updateAction($action, $url, $options=array()){
		if(!$this->auth) throw new Exception('it is not authorized yet.');

		$action_string = 'version='.$this->version;
		$action_string .= '&action='.$action;
		if($action != 'download') {
			$action_string .= "&format=xml";
		}

		foreach($options as $key => $value){
			if(in_array($key, array('index_zip', 'mtime', 'size', 'md5', 'sha1', 'md5', 'destination', 'target', 'qd_confirm'))){
				if($key == 'target' || $key == 'destination') $value = urlencode($value);
				if($key == 'qd_confirm') $key = 'quick-delete';
				$action_string .= "&{$key}={$value}";
			}
		}

		$auth_data	= $this->auth->getAuthData();
		$auth_sign	= $this->auth->getAuthSign($url, $action_string);
		
		$headers	= array(
			"Host: " . $this->host,
			"Accept:",
			"Accept-Encoding: identity",
			"X-Akamai-ACS-Auth-Data: {$auth_data}",
			"X-Akamai-ACS-Auth-Sign: {$auth_sign}",
			"X-Akamai-ACS-Action: {$action_string}"
		);

		// TODO $options["form_upload"]

		$body = (isset($options["body"])) ? $options["body"] : "";
#		$method = ($action == 'upload' || $action == 'symlink') ? 'PUT' : 'POST';
		$method = 'PUT';
		return $this->request($method, $url, $body, $headers);

	}

	public function request($method, $url, $body, $headers){
		$curl = curl_init('https://'.$this->host.$url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

		if($method == 'PUT') {
			$length = strlen($body);
			if($length != 0){
				$tmpfile = tmpfile();
				fwrite($tmpfile, $body);
				fseek($tmpfile, 0);
				curl_setopt($curl, CURLOPT_INFILE, $tmpfile);
			}
			curl_setopt($curl, CURLOPT_UPLOAD, 1);
			curl_setopt($curl, CURLOPT_INFILESIZE, strlen($body));


		}

#		$fp = fopen('./curl.log', 'a');
#		curl_setopt($curl, CURLOPT_VERBOSE, true);
#		curl_setopt($curl, CURLOPT_STDERR, $fp);

		$data = curl_exec($curl);
		$this->_last_status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
		curl_close($curl);

		// close tmpfile after curl request
                if(isset($tmpfile))
                        fclose($tmpfile);

		return $data;
	}

	public function getLastStatusCode(){
		return $this->_last_status_code;
	}

}


--TEST--
Akamai NetStorage HTTP-CMS API demonstration script
This is a quick and dirty script to illustrate how to call the methods.
It is not meant to be a polished
general-purpose tool for manipulating files in NetStorage.

DO NOT RUN THIS SCRIPT AGAINST A LIVE ACCOUNT!

Please Edit the configure file and describe the account information.
$ mv tests/akamai.ini.sample tests/akamai.ini

[
 * dir
 * download
 * du
 * stat
 * delete
 * mkdir
 * mtime
 * quick-delete
 * rename
 * rmdir
 * symlink
 * upload
]
--FILE--
<?php
#require dirname(__FILE__).DIRECTORY_SEPARATOR.'../Akamai.php';
require 'Akamai.php';
$config = parse_ini_file(dirname(__FILE__).DIRECTORY_SEPARATOR.'akamai.ini');

define('TEST_DIR', $config["base_url"].'/test_dir');
define('TEST_FILE', TEST_DIR.'/test_file.txt');
define('TEST_RENAMED_FILE', TEST_DIR.'/test_file_renamed.txt');
define('TEST_LINKED_FILE', TEST_DIR.'/test_file.symlink');


$service = new Akamai_Netstorage_Service($config["host"]);
$service->authorize($config["key"], $config["key_name"], $config["version"]);

/**
 * initialized function
 **/
/* check test directory exists? */
function initialize(Akamai_Netstorage_Service $service){
  $dir_string = $service->dir(TEST_DIR);
  $is_dir = ($service->getLastStatusCode() == 200) ? true : false;
  
  /* quick-delete test directory if it exists. */
  if($is_dir){
    $service->quick_delete(TEST_DIR, "imreallyreallysure");
    $quick_delete_status = $service->getLastStatusCode();
  
    /* if quick-delete disabled */
    if($quick_delete_status == 403){
      $xml =  simplexml_load_string($dir_string);
      foreach($xml as $node){
        if($node->attributes()->type == 'file' || $node->attributes()->type == 'symlink'){
          $service->delete(TEST_DIR.DIRECTORY_SEPARATOR.$node->attributes()->name);
        }
      }
      $service->rmdir(TEST_DIR);
    }
  }
}
initialize($service);


/**
 * test
 */
/* mkdir */
$service->mkdir(TEST_DIR);
echo "mkdir: ".$service->getLastStatusCode()."\n";

/* upload */
$service->upload(TEST_FILE, "hello");
echo "upload: ".$service->getLastStatusCode()."\n";

/* du */
$service->stat(TEST_FILE);
echo "stat: ".$service->getLastStatusCode()."\n";

/* symlink */
$service->symlink(TEST_LINKED_FILE, TEST_FILE);
echo "symlink: ".$service->getLastStatusCode()."\n";

/* du */
$service->du(TEST_DIR);
echo "du: ".$service->getLastStatusCode()."\n";

/* mtime */
$service->mtime(TEST_FILE, strtotime('2013-01-01 00:00:00'));
echo "mtime: ".$service->getLastStatusCode()."\n";

/* download */
$download = $service->download(TEST_FILE);
echo "download: ".$download."\n";

/* rename */
$service->rename(TEST_FILE, TEST_RENAMED_FILE);
echo "rename: ".$service->getLastStatusCode()."\n";

/* re-upload */
$service->upload(TEST_FILE, "hello");
echo "re-upload: ".$service->getLastStatusCode()."\n";

initialize($service);

?>
--EXPECT--
mkdir: 200
upload: 200
stat: 200
symlink: 200
du: 200
mtime: 200
download: hello
rename: 200
re-upload: 200

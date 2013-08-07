Akamai NetStorage HTTP API for PHP-PEAR.
=========
It is implemented in accordance with the document the following specifications:
https://control.akamai.com/dl/customers/NS/NetStrgHttpCM.pdf
April 8, 2013

### PHP versions 5.2.11+

### Usage

```
require 'Akamai.php';
$service = new Akamai_Netstorage_Service({host});
$service->authorize({key}, {key_name}, {version});
/* mkdir */
$service->mkdir('https://[example]-nsu.akamaihd.net/[dir_name]');
/* upload */
$service->upload('https://[example]-nsu.akamaihd.net/[dir_name]/[file_name]', '[string]');
/* symlink */
$service->symlink('https://[example]-nsu.akamaihd.net/[dir_name]/[symlinkfile_name]', 'https://[example]-nsu.akamaihd.net/[dir_name]/[file_name]');
/* du */
$service->du('https://[example]-nsu.akamaihd.net/[dir_name]');
/* dir */
$service->dir('https://[example]-nsu.akamaihd.net/[dir_name]');
/* mtime */
$service->mtime('https://[example]-nsu.akamaihd.net/[dir_name]', strtotime('2013-01-01 00:00:00'));
/* rename */
$service->rename('https://[example]-nsu.akamaihd.net/[dir_name]/[file_name]', 'https://[example]-nsu.akamaihd.net/[dir_name]/[renamefile_name]');
/* upload */
$service->upload('https://[example]-nsu.akamaihd.net/[dir_name]/[file_name]', '[string]');
/* stat */
$service->stat('https://[example]-nsu.akamaihd.net/[dir_name]/[file_name]');
/* delete */
$service->delete('https://[example]-nsu.akamaihd.net/[dir_name]/[file_name]');
/* rmdir */
$service->rmdir('https://[example]-nsu.akamaihd.net/[dir_name]');
/* quick-delete */
$service->quick_delete('https://[example]-nsu.akamaihd.net/[dir_name]'); 
/* get latest status */
$service->getLastStatusCode();
```

### Test
#### !! DO NOT RUN THIS SCRIPT AGAINST A LIVE ACCOUNT !!
Please Edit the configure file and describe the account information.
```
$ mv tests/akamai.ini.sample tests/akamai.ini
$ vi tests/akamai.ini
$ pear run-tests tests/
```


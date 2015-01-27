Akamai NetStorage HTTP API for PHP-PEAR.
=========

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/raben/Akamai?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)
It is implemented in accordance with the document the following specifications:
https://control.akamai.com/dl/customers/NS/NetStrgHttpCM.pdf
April 8, 2013

### PHP versions 5.2.11+

### Build
```
$ pear pv
$ pear package
```

### Install
```
$ pear info Akamai-{version}.tgz
$ pear list Akamai-{version}.tgz
$ pear install -f Akamai-{version}.tgz
```

### Usage

```
require 'Akamai.php';
$service = new Akamai_Netstorage_Service({host});
$service->authorize({key}, {key_name}, {version});
/* mkdir */
$service->mkdir('[dir_name]');
/* upload */
$service->upload('[dir_name]/[file_name]', '[string]');
/* symlink */
$service->symlink('[dir_name]/[symlinkfile_name]', '[dir_name]/[file_name]');
/* du */
$service->du('[dir_name]');
/* dir */
$service->dir('[dir_name]');
/* mtime */
$service->mtime('[dir_name]', strtotime('2013-01-01 00:00:00'));
/* rename */
$service->rename('[dir_name]/[file_name]', '[dir_name]/[renamefile_name]');
/* upload */
$service->upload('[dir_name]/[file_name]', '[string]');
/* stat */
$service->stat('[dir_name]/[file_name]');
/* delete */
$service->delete('[dir_name]/[file_name]');
/* rmdir */
$service->rmdir('[dir_name]');
/* quick-delete */
$service->quick_delete('[dir_name]'); 
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


## Introduction

Simple wrapper class for cPanel UAPI. It uses php magic methods to make simple interface.

## Overview

[UAPI](https://api.docs.cpanel.net/cpanel/introduction/) accesses the cPanel interface's features. 

#### How cPanel API Works


![image](https://user-images.githubusercontent.com/15669109/115955664-37cda200-a4f8-11eb-983c-39de9dc281a0.png)

#### How this class works ?

This class creates methods on the fly using php magic method ```__call()``` to construct methods you call. 

Usage is very simple you copy exact cPanel API module name from the cPanel endpoint and add word **Module** to it then call it as php method.

Example:
```EmailModule()```

Then you copy exact cPanel API method name and call it as php method.

Example:
``` add_mx()```

## Installation
```
composer require dezento/cpanel-api
```

## Usage


For example you want to list all domains under cPanel account you can copy 
endpoint from the official [documentation](https://api.docs.cpanel.net/openapi/cpanel/operation/list_domains/).

```
/DomainInfo/list_domains
```

All you need to do then is to add word **Module** to the module name and call it as method,

 **DomainInfoModule()**

and then write cPanel method name as php method.

***list_domains()***

```
use Dezento\CpanelApi;

$cpanel = CpanelApi::setCredentials("cPanelUrl", "cPanelUser", "cPanelPassword")
        ->DomainInfoModule() // cPanel module
        ->list_domains() // cPanel Function
        ->get();
```

If you need to send some parameters you can use ```setQueryParams()``` method
which accepts array as input.
```
use Dezento\CpanelApi;

$cpanel = CpanelApi::setCredentials("cPanelUrl", "cPanelUser", "cPanelPassword")
        ->DomainInfoModule() // cPanel module
        ->setQueryParams([
          'domain' => 'examplewebsite.com',
          'return_https_redirect' => 1 
        ])
        ->single_domain_data() // cPanel Function
        ->get();
```


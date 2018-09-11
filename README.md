# LazaLend 📱🖥🔨🖍

## Project Prompt 

Topic C, Stuff Sharing: the system allows people to borrow or lend stuff that they own (tools, appliances, furniture or books) either free or for a fee. Users advertise stuff available (what stuff, where to pick up and return, when it is available, etc.) or can browse the available stuff and bid to borrow some stuff. The stuff owner or the system (your choice) chooses the successful bid. Each user has an account. Administrators can create, modify and delete all entries. Please refer to www.snapgoods.com, www.peerby.com or other stuff sharing sites for examples and data.

## Setup Guide 

1. Create `secrets.php` in root folder
2. Paste these stuff in 

```php
<?php

//Production server
define("PROD_HOST", "localhost");
define("PROD_PORT", "5432");
define("PROD_DBNAME","cs2102");
define("PROD_USER","postgres");
define("PROD_PASSWORD","<password here>");

//Local stuff
define("HOST","178.128.20.161");
define("PORT", "5432");
define("DBNAME","cs2102");
define("USER","postgres");
define("PASSWORD","<password here>");

?>

```
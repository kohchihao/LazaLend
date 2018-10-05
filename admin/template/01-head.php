<!DOCTYPE html>

<html lang = "en">
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title><?=$_M['HEAD']['TITLE'];?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.rawgit.com/needim/noty/77268c46/lib/noty.css" crossorigin="anonymous">

        <!-- Page Level CSS -->
        <?=$_M['HEAD']['CSS']?>

        <!-- Shared CSS -->
        <link rel="stylesheet" href="./css/ll.css?v=1">
    </head>
    <!-- END HEAD -->

    <body>
    <div class="header">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="d-flex flex-grow-1">
                        <span class="w-100 d-lg-none d-block"><!-- hidden spacer to center brand on mobile --></span>
                        <a class="navbar-brand d-none d-lg-inline-block" href="./">
                            LazaLend Admin
                        </a>
                        <a class="navbar-brand-two mx-auto d-lg-none d-inline-block" href="./">
                            LazaLend Admin
                        </a>

                        <div class="w-100 text-right">
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#myNavbar">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                        </div>

                        
                    </div>

                
                <div class="collapse navbar-collapse flex-grow-1 text-right" id="myNavbar">
                    <ul class="nav-pills navbar-nav ml-auto flex-nowrap">
                        
                        <li class="nav-item">
                            <a href="#" class="nav-link m-2 menu-item active text-light">Categories</a>
                        </li>

                        <li class="nav-item">
                            <a href="#" class="nav-link m-2 menu-item active text-light">Items</a>
                        </li>

                        <li class="nav-item">
                            <a href="all-user" class="nav-link m-2 menu-item active text-light">Show user stats</a>
                        </li>
                    </ul>
                </div>
                    
                </nav>
            </div>
        </div>
        <div class = "container">
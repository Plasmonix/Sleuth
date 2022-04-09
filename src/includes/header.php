<?php require_once "includes/database.php"; ?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <link rel="shortcut icon" type="image/jpg" href="assets/images/logo.jpg">
      <meta charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title><?php echo !empty($title) ? $title : ''; ?></title>
      <link rel="stylesheet" href="assets/css/main.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
      <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
      <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
   </head>
   <body>
      <header>
         <nav>
            <a target="_blank" class="header_logo">
               <!-- <span><?= $licenseSystem->getInfo("title") ?></span> -->
            </a>
            <div class="header_links">
               <ul class="nav_links">
                  <li class="nav_link"><a href="index.php"><i class="fa fa-house"></i>Home</a></li>
                  <li class="nav_link"><a href="users.php"><i class="fa fa-user"></i>Users</a></li>
                  <li class="nav_link"><a href="keys.php"><i class="fa fa-key"></i>Keys</a></li>
                  <li class="nav_link"><a href="logout.php"><i class="fa fa-right-from-bracket"></i>Logout</a></li>
               </ul>
            </div>
         </nav>
      </header>

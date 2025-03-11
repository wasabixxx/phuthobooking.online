<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <title>Hotel Booking Website</title>
  <link rel="icon" type="image/png" href="assets/img/logo.png"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
  
  
  
  <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />


  <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
<?php include 'layouts/alert.php';
 $db = new Database();
session_start();
if (isset($_SESSION['id'])) {
  $status_user = $db->select('users', 'id = "' . $_SESSION['id'] . '"', 1);
  if ($status_user[0]['status'] == 0) {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    header('Location: login.php?&err=1&alert=Bạn đã bị ban');
}
}
 ?>
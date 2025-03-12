<?php
include dirname(__DIR__) . '/config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Kiểm tra mật khẩu nhập lại
    if ($password !== $confirm_password) {
        $alert = "Mật khẩu nhập lại không khớp!";
        header("Location: ../register.php?alert=" . urlencode($alert) . "&err=1");
        exit();
    }

    // Mã hóa mật khẩu
    $password = password_hash($password, PASSWORD_DEFAULT);

    $db = new Database();

    $target_dir = dirname(__DIR__) . "/assets/upload/avatars/";
    $alert = "";
    $profile_picture = $db->uploadImage($_FILES["profile_picture"], $target_dir, $alert);

    if ($profile_picture === false) {
        $err = 1;
        header("Location: ../register.php?alert=" . urlencode($alert) . "&err=1");
        exit();
    }

    // Loại bỏ dấu nháy đơn trong array data
    $data = array(
        "name" => $name,
        "email" => $email,
        "password" => $password,
        "profile_picture" => $profile_picture
    );

    $db->insert("users", $data);

    $alert = "Đăng ký thành công!";
    header("Location: ../login.php?alert=" . urlencode($alert) . "&err=0");
    exit();
}
?>
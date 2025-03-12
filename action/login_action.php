<?php
include dirname(__DIR__) . '/config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $phone = $_POST["phone"];

    // Kiểm tra mật khẩu nhập lại
    if ($password !== $confirm_password) {
        $alert = "Mật khẩu nhập lại không khớp!";
        header("Location: ../register.php?alert=" . urlencode($alert) . "&err=1");
        exit();
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    $db = new Database();

    // Dữ liệu không có dấu nháy đơn
    $data = array(
        "username" => $username,
        "email" => $email,
        "password" => $password,
        "phone" => $phone
    );

    $db->insert("owners", $data); // Giả sử bảng là "owners", bạn có thể đổi tên bảng nếu khác

    $alert = "Đăng ký thành công!";
    header("Location: ../login.php?alert=" . urlencode($alert) . "&err=0");
    exit();
}
?>
<?php
session_start();
include '../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ biểu mẫu và làm sạch
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';

    // Debug: Kiểm tra dữ liệu gửi từ biểu mẫu
    echo "Email gửi: '$email'<br>";
    echo "Password gửi: '$password'<br>";

    // Kiểm tra dữ liệu đầu vào
    if ($email === '' || $password === '') {
        $alert = "Vui lòng điền đầy đủ thông tin!";
        header("Location: ../login.php?alert=" . urlencode($alert) . "&err=1");
        exit();
    }

    // Kết nối cơ sở dữ liệu
    $db = new Database();

    // Truy vấn bảng users
    $where = "email = '$email'";
    $users = $db->select("users", $where, "1");

    // Debug: Kiểm tra kết quả truy vấn
    if ($users && count($users) > 0) {
        $user = $users[0];
        echo "Tìm thấy user với email: " . $user["email"] . "<br>";
        echo "Mật khẩu trong DB (đã băm): " . $user["password"] . "<br>";
        echo "Kết quả password_verify: " . (password_verify($password, $user["password"]) ? "True" : "False") . "<br>";
    } else {
        echo "Không tìm thấy user với email: '$email'<br>";
    }
    // exit; // Tạm dừng để xem debug, xóa hoặc comment sau khi kiểm tra

    // Kiểm tra kết quả
    if ($users && count($users) > 0) {
        $user = $users[0];
        if (password_verify($password, $user['password'])) {
            // Lưu thông tin vào session
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $alert = "Xin chào, " . $user['name'] . "!";
            header("Location: ../index.php?alert=" . urlencode($alert));
            exit();
        } else {
            $alert = "Mật khẩu không đúng!";
            $err = 1;
        }
    } else {
        $alert = "Không tìm thấy người dùng với email này!";
        $err = 1;
    }

    // Chuyển hướng về login nếu thất bại
    header("Location: ../login.php?alert=" . urlencode($alert) . "&err=" . $err);
    exit();
} else {
    // Nếu không phải POST, chuyển hướng về trang đăng nhập
    header("Location: ../login.php");
    exit();
}
?>
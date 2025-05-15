<?php
include('../../config/database.php');
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['email'])) {
    header("location: ../login.php");
    exit();
}

// Kết nối database
$db = new Database();

// Lấy thông tin owner
$owners = $db->select("owners", "email = '$_SESSION[email]'", 1);
if (!$owners || count($owners) === 0) {
    $_SESSION['error'] = "Không tìm thấy thông tin owner.";
    header("location: ../index.php");
    exit();
}

$owner = $owners[0];
$owner_id = $owner['id'];

// Kiểm tra status của owner
if ($owner['status'] != 1) {
    $_SESSION['error'] = "Tài khoản của bạn chưa được xác nhận. Liên hệ admin để kích hoạt.";
    header("location: ../index.php");
    exit();
}

// Xử lý form thêm khách sạn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    // Lấy dữ liệu từ form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $coordinates = $_POST['coordinates'];
    $description = $_POST['description'];
    $location_id = (int)$_POST['location_id'];
    $hotel_type_id = (int)$_POST['hotel_type_id'];
    $stars = (int)$_POST['stars'];
    $starting_price = (float)$_POST['starting_price'];
    $status = 1; // Mặc định chưa xác nhận

    // Xử lý file ảnh
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/upload/imgHotels/';
        $photo = basename($_FILES['photo']['name']);
        $uploadFile = $uploadDir . $photo;

        // Di chuyển file ảnh vào thư mục
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
            $_SESSION['error'] = "Lỗi khi tải ảnh lên.";
            header("location: ../index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Vui lòng chọn ảnh cho khách sạn.";
        header("location: ../index.php");
        exit();
    }

    // Chuẩn bị dữ liệu để chèn vào bảng hotels
    $data = [
        'owner_id' => $owner_id,
        'name' => $name,
        'address' => $address,
        'photo' => $photo,
        'stars' => $stars,
        'coordinates' => $coordinates,
        'description' => $description,
        'starting_price' => $starting_price,
        'location_id' => $location_id,
        'hotel_type_id' => $hotel_type_id,
        'status' => $status
    ];

    // Thêm khách sạn vào database
    $result = $db->insert("hotels", $data);

    if ($result) {
        $_SESSION['success'] = "Thêm khách sạn thành công!";
        header("location: ../index.php");
        exit();
    } else {
        $_SESSION['error'] = "Lỗi khi thêm khách sạn: " . $db->getLastError();
        header("location: ../index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Yêu cầu không hợp lệ.";
    header("location: ../index.php");
    exit();
}
?>
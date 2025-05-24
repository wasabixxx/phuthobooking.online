<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['name'])) {
    header("Location: ../login.php");
    exit();
}

include '../config/database.php';
$db = new Database();

// Lấy action từ URL hoặc POST
$action = $_POST['action'] ?? $_GET['action'] ?? ''; 

// Thêm bài viết
if ($action == 'add') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $img = $_FILES['img']['name'];
        $status = 1; // Trạng thái bài viết mặc định là 1 (hiển thị)

        // Upload ảnh
        $target_dir = "../assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);

        // Dữ liệu cần chèn vào bảng
        $data = [
            'title' => $title,
            'description' => $description,
            'img' => $img,
            'status' => $status
        ];

        // Gọi phương thức insert
        $result = $db->insert('blog', $data);

        if ($result) {
            echo "Blog đã được đăng thành công!";
            header("Location: ../admin/blog.php"); // Chuyển hướng về trang danh sách blog
            exit();
        } else {
            echo "Lỗi khi thêm blog.";
        }
    }
}

// Sửa bài viết
if ($action == 'edit') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $blog_id = $_POST['id'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $img = $_FILES['img']['name'];

        // Nếu có ảnh mới, upload ảnh
        if ($img) {
            $target_dir = "../assets/img/";
            $target_file = $target_dir . basename($img);
            move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
        } else {
            // Nếu không có ảnh mới, giữ nguyên ảnh cũ
            $img = $_POST['old_img'];
        }

        // Dữ liệu cần cập nhật
        $data = [
            'title' => $title,
            'description' => $description,
            'img' => $img
        ];

        // Gọi phương thức update
        $result = $db->update('blog', $blog_id, $data);

        if ($result) {
            echo "Blog đã được cập nhật thành công!";
            header("Location: ../admin/blog.php"); // Chuyển hướng về trang danh sách blog
            exit();
        } else {
            echo "Lỗi khi cập nhật blog.";
        }
    }
}

// Xóa bài viết
if ($action == 'delete') {
    $blog_id = $_GET['id'];

    // Gọi phương thức delete
    $db->delete('blog', $blog_id);

    echo "Blog đã được xóa thành công!";
    header("Location: ../admin/blog.php"); // Chuyển hướng về trang danh sách blog
    exit();
}

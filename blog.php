<?php
    include 'config/database.php';
    include 'layouts/header.php';
    include 'layouts/navbar.php';

    $db = new Database();

    // Xử lý khi form được gửi để thêm blog
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $img = $_FILES['img']['name'];
        $status = 1;

        // Upload ảnh
        $target_dir = "../assets/img/";
        $target_file = $target_dir . basename($img);
        move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);

        // Dữ liệu cần chèn vào bảng blog
        $data = [
            'title' => $title,
            'description' => $description,
            'img' => $img,
            'status' => $status
        ];

        // Gọi phương thức insert
        $result = $db->insert('blog', $data);

        if ($result) {
            $message = "Blog đã được đăng thành công!";
        } else {
            $message = "Lỗi khi thêm blog.";
        }
    }

    // Lấy dữ liệu các bài viết từ bảng blog
    $query = "SELECT * FROM blog WHERE status = 1"; 
    $blogs = $db->select('blog');
?>

<section class="blog top margin-bottom" id="blog">
    <div class="container">
        <div class="heading">
            <h3>Inspiration for your next trip</h3>
        </div>

        <div class="content grid mtop">
            <!-- Form thêm blog mới -->
            <div class="box">
                <h4>Thêm blog mới</h4>
                <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
                <form action="blog.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <label for="title">Tiêu đề:</label>
                    <input type="text" name="title" required><br><br>

                    <label for="description">Mô tả:</label><br>
                    <textarea name="description" rows="5" required></textarea><br><br>

                    <label for="img">Ảnh đại diện:</label>
                    <input type="file" name="img" required><br><br>

                    <button type="submit" class="btn btn-primary">Thêm blog</button>
                </form>
            </div>

            <!-- Hiển thị các bài blog hiện có -->
            <?php
                if ($blogs) {
                    foreach ($blogs as $blog) {
                        echo "<div class='box'>";
                        echo "<div class='img'>";
                        echo "<img src='assets/img/" . htmlspecialchars($blog['img']) . "' alt=''>";
                        echo "<span>TRAVEL</span>";
                        echo "</div>";
                        echo "<div class='text'>";
                        echo "<h3>" . htmlspecialchars($blog['title']) . "</h3>";
                        echo "<p>" . substr(htmlspecialchars($blog['description']), 0, 150) . "...</p>";
                        echo "<a href='blog-detail.php?id=" . $blog['id'] . "'>Read More <i class='far fa-long-arrow-alt-right'></i></a>";
                        echo "<br><a href='action/blog_action.php?action=edit&id=" . $blog['id'] . "'>Sửa</a> | <a href='action/blog_action.php?action=delete&id=" . $blog['id'] . "' onclick='return confirm(\"Bạn có chắc muốn xóa bài viết này không?\")'>Xóa</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No blogs found.</p>";
                }
            ?>
        </div>
    </div>
</section>

<?php
    include 'layouts/footer.php';
?>

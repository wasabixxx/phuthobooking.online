<?php
include('../config/database.php');
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['email'])) {
    header("location: login.php");
    exit();
}

// Biến xác định vai trò và trang
$role = "owner";
$page = "hotelManagement";

// Kết nối database
$db = new Database();

// Lấy thông tin owner
$owners = $db->select("owners", "email = '$_SESSION[email]'", 1);
if (!$owners || count($owners) === 0) {
    die("Không tìm thấy thông tin owner.");
}

$owner = $owners[0];
$owner_id = $owner['id'];

// Lấy thông tin khách sạn của owner từ view_hotel_details
$hotel = $db->select("view_hotel_details", "owner_id = $owner_id");

// Nếu có khách sạn thì lấy status khách sạn đầu tiên (dùng để hiển thị bản đồ nếu cần)
$status = null;
if (!empty($hotel) && isset($hotel[0]['status'])) {
    $status = $hotel[0]['status'];
}

include('../layouts/headerAd.php');
?>

<style>
    .row .col-md-6 {
        margin-bottom: 1rem;
    }
    .form-select {
        padding: 5px;
        border-radius: 15px;
    }
    .card img {
        height: 350px !important;
    }
    .card-footer a {
        width: 40%;
        padding: 5px;
    }
    .map iframe {
        width: 100%;
        height: 500px;
    }
</style>

<!-- Hiển thị thông báo thành công hoặc lỗi -->
<?php if (isset($_SESSION['success'])) : ?>
    <div class="alert alert-success">
        <?= $_SESSION['success'] ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])) : ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error'] ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Kiểm tra status của owner trước khi hiển thị form thêm khách sạn -->
<?php if ($owner['status'] != 1) : ?>
    <div class="alert alert-warning">
        Tài khoản của bạn chưa được xác nhận. Liên hệ admin để kích hoạt.
    </div>
<?php else : ?>
    <!-- Form thêm khách sạn mới -->
    <div class="card shadow p-4 mb-4">
        <h4 class="card-title mb-3">Thêm Khách Sạn Mới</h4>
        <form action="action/addHotel.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Tên Khách Sạn</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="address" class="form-label">Địa Chỉ</label>
                    <input type="text" name="address" id="address" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tọa độ Google</label>
                    <input type="text" name="coordinates" placeholder="Nhúng bản đồ HTML từ Google Maps" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="photo" class="form-label">Ảnh</label>
                    <input type="file" name="photo" id="photo" class="form-control" accept="image/*" required>
                </div>
                <div class="col-md-6">
                    <label for="stars" class="form-label">Số Sao</label>
                    <input type="number" name="stars" id="stars" class="form-control" min="0" max="5" required>
                </div>
                <div class="col-md-6">
                    <label for="starting_price" class="form-label">Giá Khởi Điểm</label>
                    <input type="number" name="starting_price" id="starting_price" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="col-md-6">
                    <label for="location_id" class="form-label">Địa Điểm</label>
                    <select name="location_id" id="location_id" class="form-select" required>
                        <?php
                        $locations = $db->select("locations");
                        foreach ($locations as $location) {
                            echo "<option value='{$location['id']}'>{$location['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="hotel_type_id" class="form-label">Loại Khách Sạn</label>
                    <select name="hotel_type_id" id="hotel_type_id" class="form-select" required>
                        <?php
                        $hotelTypes = $db->select("hotel_types");
                        foreach ($hotelTypes as $hotelType) {
                            echo "<option value='{$hotelType['id']}'>{$hotelType['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Mô Tả</label>
                    <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-success w-100">Thêm Khách Sạn</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Hiển thị danh sách khách sạn nếu có -->
<?php if (!empty($hotel)) : ?>
    <div class="container">
        <div class="row g-4">
            <?php foreach ($hotel as $h) : ?>
                <div class="w-75">
                    <div class="card shadow-sm h-100">
                        <img src="../assets/upload/imgHotels/<?= htmlspecialchars($h['photo']) ?>" class="card-img-top" alt="Hotel Image" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><b>Tên:</b> <?= htmlspecialchars($h['name']) ?></h5>
                            <p class="card-text mb-1"><strong>Địa chỉ:</strong> <?= htmlspecialchars($h['address']) ?></p>
                            <p class="card-text mb-1"><strong>Địa điểm:</strong> <?= htmlspecialchars($h['location_name'] ?? 'N/A') ?></p>
                            <p class="card-text">
                                <strong>Số sao:</strong>
                                <?php
                                for ($i = 0; $i < (int)$h['stars']; $i++) {
                                    echo "<i class='fa fa-star text-warning'></i>";
                                }
                                ?>
                            </p>
                            <p class="card-text mb-2"><strong>Mô tả:</strong> <?= htmlspecialchars($h['description']) ?></p>
                            <p class="card-text">
                                <strong>Trạng thái:</strong>
                                <?php
                                echo match ($h['status']) {
                                    default => "<span class='badge bg-success'>Đã xác nhận</span>",
                                    2 => "<span class='badge bg-warning'>Bị cấm</span><br><span class='text-warning'>Liên hệ admin để gỡ cấm</span>",
                                    0 => "<span class='badge bg-danger'>Chưa xác nhận</span><br><span class='text-danger'>Liên hệ admin để xác nhận</span>",
                                };
                                ?>
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="editHotel.php?id=<?= $h['id'] ?>&table=hotels" class="btn btn-primary btn-sm">Sửa</a>
                            <a href="action/delete.php?page=index&id=<?= $h['id'] ?>&table=hotels" class="btn btn-danger btn-sm">Xóa</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="map mt-4">
        <?php
        if (isset($hotel[0]['coordinates'])) {
            echo $hotel[0]['coordinates'];
        }
        ?>
    </div>
<?php endif; ?>

<?php include('../layouts/footerAd.php'); ?>
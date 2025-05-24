<?php
class Database
{
    public $conn;

    private const DB_SERVER = "localhost";
    private const DB_USERNAME = "root";   
    private const DB_PASSWORD = "";     
    private const DB_NAME = "pjtn";

    public function __construct()
    {
        // Kết nối với cơ sở dữ liệu
        $this->conn = new mysqli(self::DB_SERVER, self::DB_USERNAME, self::DB_PASSWORD, self::DB_NAME);

        if ($this->conn->connect_error) {
            die("Database connection failed: " . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8mb4");

        // Cập nhật trạng thái voucher
        $this->conn->query("UPDATE vouchers
            SET status = CASE
                WHEN start_date <= CURDATE() AND end_date >= CURDATE() THEN 1
                ELSE 0
            END;");
    }

    // Phương thức insert
    public function insert($table, $data)
    {
        $columns = implode(", ", array_keys($data)); // Tạo danh sách các cột
        $placeholders = implode(", ", array_fill(0, count($data), "?")); // Tạo các dấu hỏi cho các tham số

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)"; // Câu lệnh SQL

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Failed to prepare statement: " . $this->conn->error);
            return false;
        }

        // Xác định loại dữ liệu và bind tham số vào câu lệnh
        $types = "";
        $values = [];
        foreach ($data as $value) {
            $types .= $this->getType($value);
            $values[] = $value;
        }

        $stmt->bind_param($types, ...$values); // Bind các tham số vào câu lệnh

        // Thực thi câu lệnh
        if (!$stmt->execute()) {
            error_log("Insert query failed: " . $stmt->error);
            return false;
        }

        return true;
    }

    // Phương thức update
    public function update($table, $id, $data)
    {
        $set = "";
        $types = "";
        $values = [];

        foreach ($data as $column => $value) {
            $set .= "$column = ?, ";
            $types .= $this->getType($value);
            $values[] = $value;
        }
        $set = rtrim($set, ", ");

        $sql = "UPDATE $table SET $set WHERE id = ?";
        $types .= "i";

        $values[] = $id;

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Failed to prepare statement: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param($types, ...$values);

        if (!$stmt->execute()) {
            error_log("Update query failed: " . $stmt->error);
            return false;
        }

        return true;
    }

    // Xác định loại dữ liệu cho bind_param
    private function getType($value)
    {
        if (is_int($value)) {
            return "i";  // 'i' cho integer
        } elseif (is_float($value)) {
            return "d";  // 'd' cho double (float)
        } elseif (is_string($value)) {
            return "s";  // 's' cho string
        } elseif (is_null($value)) {
            return "s";  // 's' cho NULL (bạn có thể thay đổi nếu muốn xử lý khác)
        }
        return "s";  // Mặc định là string
    }

    // Lấy một bản ghi theo ID
    public function getById($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE id = $id";
        $result = $this->conn->query($sql);

        if ($result === false) {
            error_log("GetById query failed: " . $this->conn->error);
            return null;
        }
        return $result->fetch_assoc();
    }

    // Lấy nhiều bản ghi
    public function select($table, $condition = "", $limit = "")
    {
        $sql = "SELECT * FROM $table";

        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }

        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
        }

        error_log("SQL Query: " . $sql);

        $result = $this->conn->query($sql);

        if ($result === false) {
            error_log("Select query failed: " . $this->conn->error);
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Hàm xóa bản ghi theo ID
    public function delete($table, $id)
    {
        $sql = "DELETE FROM $table WHERE id = $id";
        $this->conn->query($sql);
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

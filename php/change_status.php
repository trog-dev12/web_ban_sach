<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caitiembansach";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra tham số truyền vào
if (isset($_GET['id']) && isset($_GET['status']) && $_GET['status'] == 'shipped') {
    $id = intval($_GET['id']); // Lấy ID đơn hàng

    // Bước 1: Cập nhật trạng thái đơn hàng (nếu cần)
    // Ở đây, giả sử bạn cần cập nhật trạng thái đơn hàng trước khi xóa
    $sql_update = "UPDATE orders SET status = 'Shipped' WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("i", $id);
    $stmt_update->execute();

    // Bước 2: Xóa đơn hàng
    $sql_delete = "DELETE FROM orders WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id);

    if ($stmt_delete->execute()) {
        // Nếu xóa thành công, hiển thị thông báo và chuyển hướng về trang quản lý đơn hàng
        echo "<script>
                alert('Đơn hàng đã được giao thành công và đã xóa!');
                window.location.href = 'QLDH.php'; // Quay lại trang quản lý đơn hàng
              </script>";
    } else {
        // Nếu có lỗi trong việc xóa đơn hàng
        echo "<script>
                alert('Có lỗi xảy ra khi xóa đơn hàng!');
                window.location.href = 'QLDH.php'; // Quay lại trang quản lý đơn hàng
              </script>";
    }
} else {
    // Nếu không có tham số hợp lệ
    echo "<script>
            alert('Tham số không hợp lệ!');
            window.location.href = 'QLDH.php'; // Quay lại trang quản lý đơn hàng
          </script>";
}

$conn->close();

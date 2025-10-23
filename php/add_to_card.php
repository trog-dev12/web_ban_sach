<?php
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caitiembansach";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy user_id (Giả sử là 1 nếu chưa đăng nhập)
$user_id = $_SESSION['user_id'] ?? 1;

// Kiểm tra form thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $soluong = $_POST['soluong'];  // Lấy size từ form
    $quantity = $_POST['quantity'];

    // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
    $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND soluong = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("iis", $user_id, $product_id, $soluong);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu sản phẩm đã có trong giỏ hàng, chỉ cần cập nhật số lượng
        $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND soluong = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iiis", $quantity, $user_id, $product_id, $soluong);
        $stmt->execute();
    } else {
        // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới
        $add_to_cart_sql = "
        INSERT INTO cart (user_id, product_id, soluong, quantity)
        VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($add_to_cart_sql);
        $stmt->bind_param("iiis", $user_id, $product_id, $soluong, $quantity);
        $stmt->execute();
    }

    header("Location: cart.php"); // Chuyển hướng về giỏ hàng
    exit();
}

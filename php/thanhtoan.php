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

// Lấy user_id (Giả sử user_id là 1 nếu chưa đăng nhập)
$user_id = $_SESSION['user_id'] ?? 1;

// Xử lý thanh toán
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    $note = $_POST['note'] ?? null;

    // Tính tổng tiền từ giỏ hàng
    $cart_sql = "
        SELECT c.quantity, s.price, s.id AS product_id, c.soluong
        FROM cart c
        INNER JOIN book s ON c.product_id = s.id
        WHERE c.user_id = ?";
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    $total_amount = 0;
    $order_items = [];
    while ($row = $cart_result->fetch_assoc()) {
        $total_amount += $row['price'] * $row['quantity'];
        $order_items[] = $row; // Lưu chi tiết sản phẩm
    }

    // Nếu giỏ hàng trống, không thực hiện thanh toán
    if (empty($order_items)) {
        echo "Giỏ hàng trống. Không thể thanh toán.";
        exit();
    }

    // Thêm dữ liệu vào bảng `orders`
    $order_sql = "
        INSERT INTO orders (user_id, total_amount, payment_method, name, email, phone, address, note, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param(
        "idssssss",
        $user_id,
        $total_amount,
        $payment_method,
        $name,
        $email,
        $phone,
        $address,
        $note
    );
    $stmt->execute();

    // Lấy `order_id` vừa được tạo
    $order_id = $stmt->insert_id;

    // Thêm dữ liệu vào bảng `order_items`
    $item_sql = "
        INSERT INTO order_items (order_id, product_id, quantity, price, soluong)
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($item_sql);

    foreach ($order_items as $item) {
        $stmt->bind_param(
            "iiids",
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $item['soluong']
        );
        $stmt->execute();
    }

    // Xóa giỏ hàng sau khi thanh toán
    $delete_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($delete_cart_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Chuyển hướng đến trang cảm ơn
    header("Location: thanks.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
    <link rel="stylesheet" href="../css/thanhtoan.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="./img/OIP(2).webp">
</head>

<body>
    <div class="container">
        <h1>Thanh Toán</h1>
        <form action="thanhtoan.php" method="POST">
            <div class="form-group">
                <label for="name">Họ và tên:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" name="phone" id="phone" required>
            </div>
            <div class="form-group">
                <label for="address">Địa chỉ:</label>
                <textarea name="address" id="address" required></textarea>
            </div>
            <div class="form-group">
                <label for="payment_method">Phương thức thanh toán:</label>
                <select name="payment_method" id="payment_method" required>
                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                    <option value="credit_card">Thẻ tín dụng</option>
                    <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                </select>
            </div>
            <div class="form-group">
                <label for="note">Ghi chú:</label>
                <textarea name="note" id="note"></textarea>
            </div>
            <button type="submit">Xác nhận thanh toán</button>
        </form>
    </div>
</body>

</html>
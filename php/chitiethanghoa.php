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

// Lấy ID sản phẩm từ URL
$product_id = intval($_GET['id']);
$sql = "SELECT * FROM book WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Sản phẩm không tồn tại.");
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
</head>

<body>
    <div class="product-details">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        <p>Giá: <?= number_format($product['price'], 0) ?> VND</p>
        <p><?= htmlspecialchars($product['description']) ?></p>

        <!-- Form thêm vào giỏ hàng -->
        <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
            <label for="quantity">Số lượng:</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1">
            <button type="submit">Thêm vào giỏ hàng</button>
        </form>
        <a href="index.php">Quay lại</a>
    </div>
</body>

</html>

<?php
$conn->close();
?>
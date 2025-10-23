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

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    $size = $_POST['size']; // Lấy size từ form

    // Kiểm tra nếu sản phẩm đã có trong giỏ
    $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND size = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("iis", $user_id, $product_id, $size);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Cập nhật số lượng nếu sản phẩm đã có trong giỏ hàng
        $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND size = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("iiis", $quantity, $user_id, $product_id, $size);
    } else {
        // Thêm mới sản phẩm vào giỏ hàng
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $size);
    }
    $stmt->execute();
    // Sau khi thêm vào giỏ hàng, tiếp tục load lại trang
    header("Location: shop.php");
    exit();
}

// Lấy tổng số lượng sản phẩm trong giỏ hàng để hiển thị ở icon giỏ hàng
$cart_count_sql = "SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_id = ?";
$stmt = $conn->prepare($cart_count_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_count = $result->fetch_assoc()['total_quantity'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="../css/shop.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Danh sách sản phẩm</h1>
            <div class="iconCart">
                <a href="cart.php">
                    <img src="./img/icon.png" alt="Cart">
                    <div class="totalQuantity">
                        <?= $cart_count; ?>
                    </div>
                </a>
            </div>
        </header>

        <div class="listProduct">
            <?php
            // Lấy danh sách sản phẩm
            $product_sql = "SELECT * FROM book";
            $product_result = $conn->query($product_sql);

            while ($product = $product_result->fetch_assoc()):
                $sizes = explode(',', $product['size']); // Lấy các size giày từ cơ sở dữ liệu
            ?>
                <div class="item">
                    <img src="img/<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image">
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <div class="price"><?= number_format($product['price'], 2) ?> VND</div>
                    <form action="shop.php" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">

                        <!-- Dropdown chọn size -->
                        <div class="size">
                            <label for="size">Chọn size:</label>
                            <select name="size" required>
                                <?php for ($size = 37; $size <= 45; $size++): ?>
                                    <option value="<?= $size ?>"><?= $size ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <button type="submit" name="add_to_cart">Thêm vào giỏ</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <footer>
        <div class="title">
            CÁI TIỆM BÁN GIÀY
        </div>
        <ul class="menuBottom">
            <li><i class="fa-brands fa-facebook fa-2xl" style="color: #225dc3;"></i>&nbsp;FACEBOOK</li>
            <li><i class="fa-brands fa-youtube fa-2xl" style="color: #f50a0a;"></i></i>&nbsp;YOUTUBE</li>
            <li><i class="fa-brands fa-twitter fa-2xl" style="color: #1da1f2;"></i></i>&nbsp;TWITTER</li>
        </ul>
    </footer>

    <?php $conn->close(); ?>
</body>

</html>
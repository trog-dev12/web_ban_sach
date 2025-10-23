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

// Xử lý xóa sản phẩm trong giỏ hàng
if (isset($_POST['remove_item'])) {
    $product_id = $_POST['product_id'];
    $remove_sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($remove_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Xử lý cập nhật số lượng sản phẩm trong giỏ hàng
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $update_sql = "UPDATE cart SET soluong = ? WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Lấy tất cả các sản phẩm trong giỏ hàng của người dùng
$cart_sql = "
    SELECT c.quantity, s.name, s.price, s.image_url, c.product_id, c.soluong
    FROM cart c
    INNER JOIN book s ON c.product_id = s.id
    WHERE c.user_id = ?";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
</head>

<body>
    <div class="container">
        <header>
            <div class="box">
                <div class="logo">
                    <img src="../img/OIP(1).webp" width="50" height="auto">
                    BOOK STORE
                </div>
                <div class="menu">
                    <ul>
                        <li class="tc"><a href="index.php">HOME</a></li>
                        <li class="tc"><a href="info.php">INFO</a></li>
                        <li class="tc"><a href="../html/contact.html">CONTACT</a></li>
                        <li class="tc"><a href="index.php">SHOP</a></li>
                    </ul>
                </div>
                <div class="shop">
                    <div class="icon-container">
                        <?php if (isset($_SESSION['username'])): ?>
                            <div id="account-info" style="display: block;">
                                <span>Xin chào, <?= htmlspecialchars($_SESSION['username']) ?></span>
                                <a href="logout.php">Đăng xuất</a>
                            </div>
                        <?php else: ?>
                            <a href="login.php" id="black">
                                <i class="fa-solid fa-user" style="color: #050505;"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </header>
        <br><br><br><br>
        <a href="index.php" class="back-to-shop" style="color:#ffd154;">Quay lại cửa hàng</a>


        <div class="cart-items">
            <?php if ($cart_result->num_rows > 0): ?>
                <?php while ($cart_item = $cart_result->fetch_assoc()) : ?>
                    <div class="item">
                        <img src="../img/<?= htmlspecialchars($cart_item['image_url']) ?>" alt="Product Image">
                        <div class="content">
                            <div class="name"><?= htmlspecialchars($cart_item['name']) ?></div>
                            <!-- Hiển thị size của sản phẩm -->
                            <div class="price"><?= number_format($cart_item['price'], 2) ?> VND</div>

                            <!-- Form cập nhật số lượng -->
                            <form action="cart.php" method="POST" class="update-quantity-form">
                                <input type="hidden" name="product_id" value="<?= $cart_item['product_id'] ?>">
                                <label for="quantity_<?= $cart_item['product_id'] ?>">Số lượng:</label>
                                <input type="number" name="quantity" id="quantity_<?= $cart_item['product_id'] ?>" value="<?= $cart_item['soluong'] ?>" min="1">
                                <button type="submit" name="update_quantity">Cập nhật</button>
                            </form>

                            <!-- Hiển thị tổng tiền của sản phẩm -->
                            <div class="total">Tổng: <?= number_format($cart_item['price'] * $cart_item['soluong'], 2) ?> VND</div>

                            <!-- Form xóa sản phẩm khỏi giỏ hàng -->
                            <form action="cart.php" method="POST" class="remove-item-form">
                                <input type="hidden" name="product_id" value="<?= $cart_item['product_id'] ?>">
                                <button type="submit" name="remove_item" class="remove-button">Xóa</button>
                            </form>
                        </div>
                    </div>
                    <?php
                    // Tính tổng tiền
                    $total_price += $cart_item['price'] * $cart_item['soluong'];
                    ?>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Giỏ hàng của bạn đang trống.</p>
            <?php endif; ?>
        </div>

        <?php if ($cart_result->num_rows > 0): ?>
            <div class="total-price">
                <p style="color:#ffd154;">Tổng tiền: <?= number_format($total_price, 2) ?> VND</p>
                <a href="thanhtoan.php" class="checkout-button">Thanh toán</a>
            </div>
        <?php endif; ?>
    </div>
    <footer>
        <div class="title">
            CÁI TIỆM BÁN SÁCH
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
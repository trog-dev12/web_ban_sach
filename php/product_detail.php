<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
</head>

<body>
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
    $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $cart_count = 0;
    if (isset($_SESSION['cart'])) {
        // Count the number of items in the cart
        $cart_count = count($_SESSION['cart']);
    }
    // Lấy thông tin sản phẩm từ cơ sở dữ liệu
    $sql = "SELECT * FROM book WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product_result = $stmt->get_result();

    // Kiểm tra xem sản phẩm có tồn tại không
    if ($product_result->num_rows > 0) {
        $product = $product_result->fetch_assoc();
    } else {
        echo "Sản phẩm không tồn tại.";
        exit();
    }

    // Thêm vào giỏ hàng khi nhấn nút
    if (isset($_POST['add_to_cart'])) {
        // Kiểm tra nếu người dùng đã đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: /caitiembansach.final/php/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];  // user_id trong session
        $quantity = $_POST['quantity'];
        $soluong = $_POST['soluong'];

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $check_cart_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND soluong = ?";
        $stmt = $conn->prepare($check_cart_sql);
        $stmt->bind_param("iis", $user_id, $product_id, $soluong);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Cập nhật số lượng sản phẩm trong giỏ hàng
            $update_cart_sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND soluong = ?";
            $stmt = $conn->prepare($update_cart_sql);
            $stmt->bind_param("iiis", $quantity, $user_id, $product_id, $soluong);
        } else {
            // Thêm sản phẩm mới vào giỏ hàng
            $insert_cart_sql = "INSERT INTO cart (user_id, product_id, quantity, soluong) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_cart_sql);
            $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $soluong);
        }

        // Execute and update the cart
        if ($stmt->execute()) {
            // Cập nhật số lượng giỏ hàng trong session
            $cart_count_sql = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = ?";
            $stmt = $conn->prepare($cart_count_sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $cart_result = $stmt->get_result();
            $cart_count = $cart_result->fetch_assoc()['total_items'];

            // Lưu số lượng vào session
            $_SESSION['cart_count'] = $cart_count;

            // Redirect to cart page
            header("Location: cart.php");
            exit();
        } else {
            echo "Lỗi khi thêm vào giỏ hàng.";
        }
    }
    ?>
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
                    <li class="tc"><a href="contact.html">CONTACT</a></li>
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
                <div class="cart-items" id="cartItems">
                    <a href="cart.php">
                        <i class="fa-solid fa-basket-shopping" style="color: #050505;"></i>
                        <span><?= htmlspecialchars($cart_count) ?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <br><br><br><br>
    <div class="product-detail">
        <div class="product-image">
            <img style="width: 150px; height:200px;" src="../img/<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
        <div class="product-info">
            <h1 style="color:#ffd154;"><?= htmlspecialchars($product['name']) ?></h1>
            <p class="description" style="color:#ffd154;"><?= htmlspecialchars($product['description']) ?></p>
            <p class="price"><?= number_format($product['price'], 2) ?> VND</p>

            <!-- Form thêm vào giỏ hàng -->
            <form action="" method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <label for="quantity">Số lượng: </label>
                <input style="border-radius: 7px ;" type="number" name="quantity" value="1" min="1" required>
                <br>
                <label for="soluong">Chọn số lượng: </label>
                <select name="soluong" required style="border-radius: 7px ;">
                    <?php for ($soluong = 1; $soluong <= 10; $soluong++): ?>
                        <option valuesize="<?= $soluong ?>"><?= $soluong ?></option>
                    <?php endfor; ?>
                </select>
                <br>
                <button type="submit" name="add_to_cart">Thêm vào giỏ</button>
            </form>
        </div>
    </div>

    <footer>
        <div class="title">CÁI TIỆM BÁN SÁCH</div>
        <ul class="menuBottom">
            <li><i class="fa-brands fa-facebook fa-2xl" style="color: #225dc3;"></i>&nbsp;FACEBOOK</li>
            <li><i class="fa-brands fa-youtube fa-2xl" style="color: #f50a0a;"></i>&nbsp;YOUTUBE</li>
            <li><i class="fa-brands fa-twitter fa-2xl" style="color: #1da1f2;"></i>&nbsp;TWITTER</li>
        </ul>
    </footer>
</body>

</html>

<?php
$conn->close();
?>
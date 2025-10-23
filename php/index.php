<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CÁI TIỆM BÁN SÁCH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body>
    <?php
    session_start();

    // Kiểm tra trạng thái đăng nhập
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
    }
    // Kết nối cơ sở dữ liệu
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "caitiembansach";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Lấy thông tin người dùng từ cơ sở dữ liệu
    $sql = "SELECT * FROM register WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Cập nhật thông tin người dùng
        $username = $_POST['username'] ?? ''; // Nếu không tồn tại, gán giá trị mặc định là ''
        $email = $_POST['email'] ?? '';       // Tương tự

        $sql = "UPDATE register SET username = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $username, $email, $user_id);
    } // Xử lý thêm sản phẩm vào giỏ hàng
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
        if (!isset($_SESSION['user_id'])) {
            // Nếu chưa đăng nhập, chuyển hướng đến login.php
            header("Location: /caitiembansach.final/php/login.php");
            exit();
        }
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        $soluong = $_POST['soluong']; // Lấy size từ form

        // Kiểm tra nếu sản phẩm đã có trong giỏ
        $check_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ? AND soluong = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("iis", $user_id, $product_id, $soluong);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Cập nhật số lượng nếu sản phẩm đã có trong giỏ hàng
            $update_sql = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ? AND soluong = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("iiis", $quantity, $user_id, $product_id, $soluong);
        } else if (isset($user_id) && $user_id !== null) {
            $insert_sql = "INSERT INTO cart (user_id, product_id, quantity, soluong) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iiis", $user_id, $product_id, $quantity, $soluong);
            $stmt->execute();
        } else {
            die("Lỗi: user_id không xác định. Vui lòng đăng nhập.");
        }
    }
    // Lấy tổng số lượng sản phẩm trong giỏ hàng để hiển thị ở icon giỏ hàng
    $cart_count = 0;
    if (isset($user_id)) {
        $cart_count_sql = "SELECT SUM(quantity) AS total_quantity FROM cart WHERE user_id = ?";
        $stmt = $conn->prepare($cart_count_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart_count = $result->fetch_assoc()['total_quantity'] ?? 0;
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
                    <li class="tc"><a href="../html/contact.html">CONTACT</a></li>
                    <li class="tc"><a href="#Products">SHOP</a></li>
                </ul>
            </div>
            <div class="shop">
                <div class="search-container">
                    <form action="search_result.php">
                        <input type="text" name="query" placeholder="Tìm kiếm">&nbsp;
                        <button type="submit" class="search-button">Tìm kiếm</button>
                    </form>
                </div>
                <div class="user-actions">
                    <div class="icon-container">
                        <?php if (isset($_SESSION['username'])): ?>
                            <!-- Nếu người dùng đã đăng nhập, hiển thị tên người dùng -->
                            <div id="account-info" style="display: block;">
                                <span>Xin chào, <?= htmlspecialchars($_SESSION['username']) ?></span>
                                <a href="logout.php">Đăng xuất</a>
                            </div>
                        <?php else: ?>
                            <!-- Nếu người dùng chưa đăng nhập, hiển thị icon đăng nhập -->
                            <a href="login.php" id="black">
                                <i class="fa-solid fa-user" style="color: #050505;"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="cart-items" id="cartItems">
                        <a href="cart.php">
                            <i class="fa-solid fa-basket-shopping" style="color: #050505;"></i>
                            <span><?= $cart_count ?></span>
                        </a>
                    </div>
                </div>


            </div>
        </div>
    </header>
    <br>
    <div class="container">
        <div class="banner">
            <div class="title">
                CÁI TIỆM BÁN SÁCH
            </div>
            <div class="detail">
                <div class="info">
                    <h2>Những cuốn sách mang hương thời gian</h2>
                    <div class="des">
                        <!-- lorem 15 -->
                        "Giữa nhịp sống hiện đại, <b>Cái Tiệm Bán Sách</b> là góc nhỏ dành cho những tâm hồn yêu chữ nghĩa — nơi từng trang giấy lưu giữ ký ức, tri thức và những xúc cảm bình yên."
                    </div>
                </div>
                <div class="img">
                    <img src="../img/muado.jpg" alt="">
                </div>
                <div class="option">
                    "Tại đây, chúng tôi không chỉ bày bán những cuốn sách, mà còn gìn giữ một thế giới cũ – nơi bạn có thể tìm lại chính mình qua từng câu chữ, từng dòng văn."
                    <!--<br>
                    <b>Màu sắc</b> <br>
                    <div class="ellipse" style="background-color:#555"></div>
                    <div class="ellipse" style="background-color:aqua"></div>
                    <div class="ellipse" style="background-color:brown"></div>
                    <br>
                    <button class="card">Add to card</button>-->
                    <br><br>
                    <b>Thể loại được yêu thích</b> <br>
                    <div class="ellipse" style="background-color:#6b4f4f"></div> Văn học cổ điển
                    <div class="ellipse" style="background-color:#a18d6c"></div> Triết học
                    <div class="ellipse" style="background-color:#4a5d73"></div> Tản văn & Hồi ký
                    <br><br>
                </div>
            </div>
        </div>
        &nbsp;&nbsp;&nbsp;<h1 style="color:#ffd154;">PRODUCT</h1>
        <!-- list item -->
        <div class="list" id="Products">
            <?php
            // Lấy danh sách sản phẩm
            $sql = "SELECT * FROM book";
            $product_result = $conn->query($sql);

            while ($product = $product_result->fetch_assoc()):
                $soluongs = explode(',', $product['soluong']);
            ?>
                <div class="item">
                    <!-- Liên kết đến trang chi tiết sản phẩm -->
                    <a href="product_detail.php?id=<?= $product['id'] ?>">
                        <img src="../img/<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image">
                    </a>
                    <h2><?= htmlspecialchars($product['name']) ?></h2>
                    <div class="price"><?= number_format($product['price'], 2) ?> VND</div>
                    <form action="" method="POST">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="quantity" value="1">

                        <!-- Dropdown chọn size -->
                        <div class="soluong">
                            <label for="soluong">Số lượng:</label>
                            <select name="soluong" required style="border-radius: 7px ;">
                                <?php for ($soluong = 1; $soluong <= 10; $soluong++): ?>
                                    <option value="<?= $soluong ?>"><?= $soluong ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <button type="submit" name="add_to_cart" id="btn_them" style="background: linear-gradient(135deg, #1ca7ec, #000000); /* Hiệu ứng màu gradient */
            color: #FFFFFF; /* Màu chữ trắng */
            padding: 12px 20px; /* Đệm bên trong nút */
            margin: 10px;
            font-size: 16px; /* Cỡ chữ */
            font-weight: bold; /* Đậm chữ */
            border: none; /* Không có viền */
            border-radius: 25px; /* Bo góc */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Bóng đổ */
            cursor: pointer; /* Con trỏ khi di chuột */
            transition: all 0.3s ease; /* Hiệu ứng chuyển động */">
                            Thêm vào giỏ hàng</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
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
    <!--TRỞ LẠI ĐẦU TRANG-->
    <a class="totop" href="#">
        <img src="../img/arrow.png" alt="">
    </a>
    <!--<script src="index.js"></script>-->
</body>

</html>
<?php $conn->close(); ?>
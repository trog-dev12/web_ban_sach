<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caitiembansach";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy từ khóa tìm kiếm từ URL
$query = $_GET['query'] ?? '';  // Sử dụng `??` để kiểm tra nếu `query` không tồn tại trong URL

// Nếu từ khóa không rỗng, thực hiện tìm kiếm
if (!empty($query)) {
    $sql = "SELECT * FROM book WHERE name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%"; // Dùng dấu "%" để tìm kiếm chứa từ khóa
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = [];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm</title>
    <link rel="stylesheet" href="../css/search_result.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
</head>

<body>
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
                        <input type="text" name="query" placeholder="Tìm kiếm" value="<?= htmlspecialchars($query) ?>">
                        <button type="submit" class="search-button">Tìm kiếm</button>

                    </form>
                </div>&nbsp;&nbsp;&nbsp;
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
            </div>
        </div>
    </header>
    <div class="search-results">
        <h1>Kết quả tìm kiếm cho: <?= htmlspecialchars($query) ?></h1>
        <div class="products">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-item">
                        <a href="product_detail.php?id=<?= $row['id'] ?>">
                            <img src="../img/<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                        </a>
                        <div class="product-name"><?= htmlspecialchars($row['name']) ?></div>
                        <div class="product-price"><?= number_format($row['price'], 2) ?> VND</div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Không tìm thấy sản phẩm nào.</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();  // Đóng kết nối cơ sở dữ liệu
?>
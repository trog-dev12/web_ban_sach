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
echo "";

// Biến lưu trạng thái sửa
$edit_mode = false;
$current_shoe = null;

// Xử lý Thêm/Sửa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image_url = null;

    // Kiểm tra và xử lý upload ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "img"; // Thư mục lưu ảnh
        $image_url = $target_dir . basename($_FILES["image"]["name"]);

        // Di chuyển file vào thư mục
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_url)) {
            die("Lỗi khi upload ảnh.");
        }
    }

    // Kiểm tra có phải là chế độ sửa không
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Chế độ sửa
        $id = intval($_POST['id']);
        $sql = "UPDATE book SET name = ?, price = ?, description = ?, image_url = IFNULL(?, image_url) WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssi", $name, $price, $description, $image_url, $id);
    } else {
        // Chế độ thêm mới
        $sql = "INSERT INTO book (name, price, description, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdss", $name, $price, $description, $image_url);
    }

    if ($stmt->execute()) {
        echo "Sản phẩm đã được thêm/sửa thành công!";
        header("Location: admin_dashboard.php"); // Chuyển hướng về trang quản lý sau khi thêm/sửa
        exit();
    } else {
        echo "Lỗi: " . $stmt->error; // Nếu có lỗi trong quá trình thêm/sửa
    }
}

// Xử lý Xóa sản phẩm
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM book WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php"); // Chuyển hướng về trang quản lý sau khi xóa
        exit();
    } else {
        echo "Lỗi khi xóa: " . $stmt->error;
    }
}

// Xử lý Sửa sản phẩm (lấy dữ liệu sản phẩm để sửa)
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM book WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_shoe = $result->fetch_assoc();
}

// Lấy danh sách sản phẩm để hiển thị
$sql = "SELECT * FROM book ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sách - Cái Tiệm Bán Sách</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        form {
            margin: 20px 0;
        }

        img {
            width: 50px;
            height: auto;
        }

        body {
            background-image: url('../img/thuvien.jpg');
            background-size: cover;
            /* Điều chỉnh để ảnh phủ đầy màn hình */
            background-position: center center;
            /* Căn giữa ảnh nền */
            background-attachment: fixed;
            /* Giữ ảnh nền cố định khi cuộn trang */
        }

        .container {
            padding: 50px;
            color: white;
            text-align: center;
        }
    </style>
</head>

<body>
    <header>
        <div class="box">
            <div class="logo">
                <a href="index.php"><img src="../img/OIP(1).webp" width="50" height="auto"></a>
                BOOK STORE
            </div>
            <div class="menu">
                <ul>
                    <!--<li class="tc"><a href="acount_management.php">QUẢN LÝ TÀI KHOẢN</a></li>
                    <li class="tc"><a href="QLHH.php">QUẢN LÝ HÀNG HÓA</a></li>
                    <li class="tc"><a href="QLDH.php">QUẢN LÝ ĐƠN HÀNG</a></li>
                    <li class="tc"><a href="/Caitiembangiay.final/Caitiembangiay.final/index.php">THOÁT</a></li>-->
                    <li><a href="acount_management.php">QUẢN LÝ TÀI KHOẢN</a></li>
                    <li><a href="QLHH.php">QUẢN LÝ HÀNG HÓA</a></li>
                    <li><a href="QLDH.php">QUẢN LÝ ĐƠN HÀNG</a></li>
                    <li><a href="/caitiembansach.final/php/index.php">THOÁT</a></li>
                </ul>
            </div>
            <div class="shop">

    </header>
</body>

</html>

<?php $conn->close(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
</head>

<body>
    <?php

    session_start();

    $servername = "localhost";
    $username = "root"; // Tên người dùng MySQL
    $password = ""; // Mật khẩu MySQL
    $dbname = "caitiembansach"; // Tên cơ sở dữ liệu

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    //Kiểm tra kết nối
    // if (mysqli_connect_errno()) {
    //     echo "Kết nối thất bại".mysqli_connect_error();
    // 		exit();
    // }else{
    // 	echo"Kết nối thành công";
    // }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST["username"];
        $pass = $_POST["password"];

        if (empty($user) || empty($pass)) {
            echo "<script>alert('Vui lòng nhập đầy đủ thông tin!');</script>";
        } else {

            $sql = "SELECT * FROM register WHERE username = '$user'";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if ($pass == $row["password"]) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['role'] = $row['role'];

                    //phân quyền
                    if ($row['role'] == 'admin') {
                        echo "<script>
							alert('Đăng nhập thành công! Chào mừng Admin.');
							window.location.href = '/caitiembansach.final/php/admin_dashboard.php';
						</script>";
                    } else {
                        echo "<script>
							alert('Đăng nhập thành công! Chào mừng User.');
							window.location.href = '/caitiembansach.final/php/index.php';
						</script>";
                    }
                    exit;
                } else {
                    echo "<script>alert('Sai mật khẩu. Vui lòng nhập lại mật khẩu!');</script>";
                }
            } else {
                echo "<script>alert('Tên người dùng không tồn tại');</script>";
            }
        }
    }
    $conn->close();
    ?>
    <div class="login">
        <form action="" method="POST">
            <div class="title">ĐĂNG NHẬP</div>
            <div class="group">
                <input type="text" id="username" name="username" placeholder="Tên đăng nhập">
            </div>
            <div class="group">
                <input type="password" id="password" name="password" placeholder="Mật khẩu">
            </div>
            <div class="recovery">
                <a href="forgetpwd.html">Quên mật khẩu?</a>
            </div>
            <div class="signIn">
                <button type="submit">Đăng nhập</button>
            </div>
            <div class="signIn">
                <button><a href="register.php">Đăng ký</a></button>
                <div class="or">
                    HOẶC
                </div>
                <div class="list">
                    <div class="item">
                        <img src="https://cdn1.iconfinder.com/data/icons/google-s-logo/150/Google_Icons-09-512.png" alt="">
                    </div>
                    <div class="item">
                        <img src="https://museumandgallery.org/wp-content/uploads/2020/03/Facebook-Icon-Facebook-Logo-Social-Media-Fb-Logo-Facebook-Logo-PNG-and-Vector-with-Transparent-Background-for-Free-Download.png" alt="">
                    </div>
                    <div class="item">
                        <img src="https://www.iconpacks.net/icons/2/free-twitter-logo-icon-2429-thumb.png" alt="">
                    </div>
                </div>
        </form>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
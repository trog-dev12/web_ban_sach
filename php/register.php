<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>CÁI TIỆM BÁN SÁCH</title>
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
</head>

<body>
    <?php
    // Kết nối cơ sở dữ liệu
    $servername = "localhost";
    $username = "root"; // Tên người dùng MySQL
    $password = ""; // Mật khẩu MySQL
    $dbname = "caitiembansach"; // Tên cơ sở dữ liệu

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    // if (mysqli_connect_errno()) {
    //     echo "Kết nối thất bại".mysqli_connect_error();
    // 		exit();
    // }else{
    // 	echo"Kết nối thành công";
    // }
    $error_message = "Mật khẩu không khớp.Vui lòng thử lại!";
    // Xử lý form đăng ký
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $user = $_POST["username"];
        $pass = $_POST["password"];
        $confirm_pass = $_POST["confirmpassword"];
        $email = $_POST["email"];


        // Kiểm tra mật khẩu có khớp
        if ($pass !== $confirm_pass) {
            echo "<script>alert('Mật khẩu không khớp.Vui lòng thử lại!');</script>";
        } else {
            // Mã hóa mật khẩu
            //$hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            $check_sql = "SELECT * FROM register WHERE username = '$user' OR email = '$email'";
            $result = mysqli_query($conn, $check_sql);

            if (mysqli_num_rows($result) > 0) {
                // Nếu có tài khoản trùng, hiển thị thông báo lỗi
                echo "<script>alert('Tên người dùng hoặc email đã tồn tại. Vui lòng chọn tên khác hoặc sử dụng email khác!');</script>";
            } else {
                // Chèn dữ liệu vào bảng `users`
                $sql = "INSERT INTO register (username, password, email) 
            VALUES ('$user', '$pass', '$email')";
                if (mysqli_query($conn, $sql)) {
                    echo "<script> alert('Đăng ký thành công!');
		window.location.href = 'login.php';
		</script>";
                } else {
                    echo "Lỗi: " . mysqli_error($conn);
                }
            }
        }
    }

    $conn->close();
    ?>
    <div class="form-popup" id="formPopup">
        <form action="" method="POST">
            <div class="login">
                <div class="title">ĐĂNG KÝ</div>
                <div class="group">
                    <label for="username"></label>
                    <input type="text" id="username" name="username" placeholder="Họ tên" required>
                </div>
                <div class="group">
                    <label for="password"></label>
                    <input type="password" id="password" name="password" placeholder="Mật khẩu" required>
                </div>
                <div class="group">
                    <label for="confirmPassword"></label>
                    <input type="password" id="confirmpassword" name="confirmpassword" placeholder="Nhập lại mật khẩu" required>
                </div>
                <div class="group">
                    <label for="mail"></label>
                    <input type="email" id="mail" name="email" placeholder="Email" required>
                </div>
                <div class="signIn">
                    <button type="submit">Đăng ký</button>
                </div>
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
            </div>
        </form>
    </div>

    <script src="register.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>Định vị trên Google maps</title>
    <link rel="icon" type="image/x-icon" href="../img/OIP(2).webp">
    <link rel="stylesheet" href="../css/info.css?v=1">
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
                    <li class="tc"><a href="index.php">SHOP</a></li>
                </ul>
            </div>
            <div class="shop">
                <!-- <div class="search-container">
                    <form action="search_resutlt.php">
                        <input type="text" name="query" placeholder="Tìm kiếm">&nbsp;
                        <button type="submit" class="search-button">Tìm kiếm</button>
                    </form>
                </div>&nbsp;&nbsp;&nbsp; -->
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
    <h2 style="color:#ffd154; ">Định vị cửa hàng trên Google maps</h2>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3926.1455256783106!2d105.9591483745112!3d10.249844868686877!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310a82ce95555555%3A0x451cc8d95d6039f8!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBTxrAgcGjhuqFtIEvhu7kgdGh14bqtdCBWxKluaCBMb25n!5e0!3m2!1svi!2s!4v1731161668298!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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

</body>

</html>
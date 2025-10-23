<?php
session_start();
session_unset();  // Hủy tất cả các biến trong session
session_destroy();  // Hủy session

header("Location: index.php");  // Chuyển hướng người dùng trở lại trang chủ hoặc trang đăng nhập
exit();

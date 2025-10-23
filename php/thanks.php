<?php
$order_id = $_GET['order_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cảm Ơn</title>
    <!-- <link rel="stylesheet" href="thank_you.css"> -->
    <link rel="stylesheet" href="../css/thanks.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="./img/OIP(2).webp">
</head>

<body>
    <div class="container">
        <h1>Cảm ơn bạn đã đặt hàng!</h1>
        <?php if ($order_id): ?>
            <p>Mã đơn hàng của bạn là: <strong>#<?= htmlspecialchars($order_id) ?></strong></p>
        <?php endif; ?>
        <a href="../php/index.php" class="continue-shopping">Tiếp tục mua sắm</a>
    </div>
</body>

</html>
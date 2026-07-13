<?php

session_start();


include 'db.php';

$error_message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); 
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

   
        header("Location: index.php");
        exit();
    } else {
        $error_message = "اسم المستخدم أو كلمة المرور غير صحيحة!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول - العيادة الطبية</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <div class="login-container">
        <h2>تسجيل الدخول للعيادة</h2>
        
        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label>اسم المستخدم:</label>
            <input type="text" name="username" required>

            <label>كلمة المرور:</label>
            <input type="password" name="password" required>

            <button type="submit">دخول</button>
        </form>
    </div>

</body>
</html>
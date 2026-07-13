<?php
// 1. بدء الجلسة للوصول إلى البيانات الحالية
session_start();

// 2. إخلاء جميع متغيرات الجلسة
$_SESSION = array();

// 3. تدمير الجلسة بالكامل من السيرفر
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// 4. توجيه المستخدم تلقائياً إلى صفحة تسجيل الدخول
header("Location: login.php");
exit;
?>
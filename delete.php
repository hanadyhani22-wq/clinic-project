<?php
// 1. بدء الجلسة وتضمين ملف الاتصال
session_start();
include 'db.php';

// 2. التحقق من إرسال المعرّف (ID) عبر الرابط (GET)
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    // 3. استعلام الحذف (Delete) بناءً على رقم المريض
    $sql_delete = "DELETE FROM patients WHERE patient_id = $patient_id";

    if ($conn->query($sql_delete) === TRUE) {
        // بعد الحذف بنجاح، نعود فوراً لصفحة index.php لتحديث الجدول تلقائياً
        header("Location: index.php");
        exit();
    } else {
        echo "خطأ أثناء الحذف: " . $conn->error;
    }
} else {
    // إذا تم فتح الصفحة بشكل خاطئ بدون ID نرجعه للرئيسية
    header("Location: index.php");
    exit();
}
?>
<?php
// 1. بدء الجلسة والتحقق من الأمان لحماية الصفحة
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. تضمين ملف الاتصال بقاعدة البيانات بطريقة الدكتورة
include 'db.php';

$message = "";

// 3. التحقق من إرسال الفورم (ضغط زر الحفظ)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name  = $_POST['full_name'];
    $gender     = $_POST['gender'];
    $phone      = $_POST['phone'];
    $birth_date = $_POST['birth_date'];

    // 4. استعلام الإضافة (Create) بأسلوب mysqli المعتمد على الكائنات
    $sql = "INSERT INTO patients (full_name, gender, phone, birth_date) 
            VALUES ('$full_name', '$gender', '$phone', '$birth_date')";

    if ($conn->query($sql) === TRUE) {
        // إذا نجحت الإضافة، يتم التوجيه تلقائياً إلى لوحة التحكم لرؤية المريض الجديد
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "حدث خطأ أثناء إضافة المريض: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة مريض جديد - العيادة</title>
</head>
<body>

    <div class="form-container">
        <h2>إضافة مريض جديد إلى النظام (Create)</h2>
        
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="add_patient.php" method="POST">
            <p>
                <label>الاسم الكامل للمريض:</label><br>
                <input type="text" name="full_name" required>
            </p>

            <p>
                <label>الجنس:</label><br>
                <select name="gender" required>
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                </select>
            </p>

            <p>
                <label>رقم الهاتف:</label><br>
                <input type="text" name="phone" required>
            </p>

            <p>
                <label>تاريخ الميلاد:</label><br>
                <input type="date" name="birth_date" required>
            </p>

            <p>
                <button type="submit">حفظ بيانات المريض</button>
                <a href="dashboard.php">إلغاء والعودة</a>
            </p>
        </form>
    </div>

</body>
</html>
<?php
// 1. بدء الجلسة وتضمين ملف الاتصال
session_start();
include 'db.php';

// 2. جلب رقم المريض المرسل عبر الرابط (GET)
if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    
    // استعلام لجلب بيانات هذا المريض المعين لعرضها داخل الحقول
    $sql = "SELECT * FROM patients WHERE patient_id = $patient_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
    } else {
        echo "المريض غير موجود!";
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// 3. معالجة البيانات عند الضغط على زر التحديث (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_patient'])) {
    $full_name  = $_POST['full_name'];
    $gender     = $_POST['gender'];
    $phone      = $_POST['phone'];
    $birth_date = $_POST['birth_date'];

    // استعلام التحديث (Update) بناءً على رقم المريض
    $sql_update = "UPDATE patients SET 
                   full_name = '$full_name', 
                   gender = '$gender', 
                   phone = '$phone', 
                   birth_date = '$birth_date' 
                   WHERE patient_id = $patient_id";

    if ($conn->query($sql_update) === TRUE) {
        // بعد نجاح التعديل نعود فوراً للصفحة الرئيسية index.php لرؤية التعديل حياً
        header("Location: index.php");
        exit();
    } else {
        echo "خطأ في تحديث البيانات: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل بيانات المريض</title>
</head>
<body>

    <div style="max-width: 600px; margin: 50px auto; font-family: Arial, sans-serif;">
        <h2>🔄 تعديل سجل المريض رقم: <?php echo $patient['patient_id']; ?></h2>
        <hr>

        <form action="edit_patient.php?id=<?php echo $patient_id; ?>" method="POST" style="background: #f9f9f9; padding: 20px; border: 1px solid #ccc; border-radius: 5px;">
            <p>
                <label>الاسم الكامل:</label>
                <input type="text" name="full_name" value="<?php echo $patient['full_name']; ?>" required style="width: 100%; padding: 8px;">
            </p>
            <p>
                <label>الجنس:</label>
                <select name="gender" required style="width: 100%; padding: 8px;">
                    <option value="male" <?php if($patient['gender'] == 'male') echo 'selected'; ?>>ذكر</option>
                    <option value="female" <?php if($patient['gender'] == 'female') echo 'selected'; ?>>أنثى</option>
                </select>
            </p>
            <p>
                <label>رقم الهاتف:</label>
                <input type="text" name="phone" value="<?php echo $patient['phone']; ?>" required style="width: 100%; padding: 8px;">
            </p>
            <p>
                <label>تاريخ الميلاد:</label>
                <input type="date" name="birth_date" value="<?php echo $patient['birth_date']; ?>" required style="width: 100%; padding: 8px;">
            </p>
            <p style="text-align: center;">
                <button type="submit" name="update_patient" style="padding: 10px 20px; background-color: #28a745; color: white; border: none; cursor: pointer;">تحديث البيانات الآن</button>
                <a href="index.php" style="margin-right: 10px; color: red; text-decoration: none;">إلغاء</a>
            </p>
        </form>
    </div>

</body>
</html>
<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['full_name']);
    $gender = $_POST['gender'];
    $phone = trim($_POST['phone']);
    $dob = $_POST['birth_date'];

    if (empty($name) || empty($phone) || empty($dob)) {
        exit("الرجاء تعبئة جميع الحقول الإلزامية.");
    }

    // 2. معالجة رفع الصورة
    $image_name = "";
    if (isset($_FILES['patient_image']) && $_FILES['patient_image']['error'] == 0) {
        $target_dir = "./uploads/";
        $image_name = time() . "_" . basename($_FILES["patient_image"]["name"]);
        $target_file = $target_dir . $image_name;
        
        move_uploaded_file($_FILES["patient_image"]["tmp_name"], $target_file);
    } else {
        die("فشل رفع الملف! رمز الخطأ من السيرفر هو: " . $_FILES["patient_image"]["error"]);
    }

    // 3. إدخال البيانات في قاعدة البيانات (تم تعديل علامات الاستفهام لـ 5 فقط لتطابق الحقول)
    $sql = "INSERT INTO patients (full_name, gender, phone, birth_date, patient_image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $gender, $phone, $dob, $image_name);

    if ($stmt->execute()) {
        header("Location: index.php?success=تم إضافة المريض وصورته بنجاح!");
    } else {
        header("Location: index.php?error=حدث خطأ أثناء حفظ البيانات");
    }

    $stmt->close();
    $conn->close();
}
?>
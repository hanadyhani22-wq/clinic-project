<?php
session_start();
include 'db.php';

// التحقق من الأمان وثبات الجلسة (Session) حسب طلب الدكتورة
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// 1. معالجة إرسال الفورم ورفع الصورة إلى السيرفر المحلي (Create)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_record'])) {
    $patient_id   = $_POST['patient_id'];
    $doctor_id    = $_POST['doctor_id'];
    $diagnosis    = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];
    $visit_date   = $_POST['visit_date'];
    
    // إعدادات رفع الملف والصورة
    $target_dir  = "uploads/"; 
    // تغيير اسم الصورة بإضافة طابع زمني (time) لمنع تداخل الأسماء إذا تشابهت الملفات
    $image_name  = time() . "_" . basename($_FILES["medical_image"]["name"]); 
    $target_file = $target_dir . $image_name;
    $uploadOk    = 1;
    
    // التحقق من صحة المدخلات (Validation) - التأكد أن الملف المرفوع هو صورة فعلاً
    $check = getimagesize($_FILES["medical_image"]["tmp_name"]);
    if($check === false) {
        $message = "الملف المرفوع ليس صورة حقيقية! ❌";
        $uploadOk = 0;
    }

    // إذا كانت المدخلات سليمة والصورة صحيحة، يتم رفعها وإدخال البيانات لقاعدة البيانات
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["medical_image"]["tmp_name"], $target_file)) {
            
            // استعلام الإضافة للجدول الخامس (medical_records)
            $sql = "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, prescription, attachment_path, visit_date) 
                    VALUES ('$patient_id', '$doctor_id', '$diagnosis', '$prescription', '$target_file', '$visit_date')";
            
            if ($conn->query($sql) === TRUE) {
                $message = "تم إضافة السجل الطبي ورفع الصورة بنجاح! ✅";
            } else {
                $message = "حدث خطأ في قاعدة البيانات: " . $conn->error;
            }
        } else {
            $message = "عذراً، حدث خطأ أثناء نسخ الصورة إلى مجلد uploads. ❌";
        }
    }
}

// 2. جلب السجلات المخزنة لعرضها في الجدول (Read) باستخدام INNER JOIN لربط الأسماء
$sql_select = "SELECT mr.*, p.full_name AS patient_name, d.doctor_name 
               FROM medical_records mr
               INNER JOIN patients p ON mr.patient_id = p.patient_id
               INNER JOIN doctors d ON mr.doctor_id = d.doctor_id
               ORDER BY mr.record_id DESC";
$result = $conn->query($sql_select);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إدارة السجلات الطبية والتقارير</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px;">

    <div style="max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0,0,0,0.1);">
        
        <p><a href="index.php" style="text-decoration: none; color: #007bff; font-weight: bold;">⬅️ العودة للوحة التحكم الرئيسية</a></p>
        
        <h2 style="text-align: center; color: #333;">🩺 إدارة السجلات الطبية ورفع صور الأشعة والتقارير</h2>
        <hr>

        <?php if (!empty($message)): ?>
            <p style="color: green; font-weight: bold; text-align: center; background: #e2f0d9; padding: 10px; border-radius: 5px;"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="medical_records.php" method="POST" enctype="multipart/form-data" style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 30px;">
            <legend style="font-weight: bold; font-size: 1.2em; color: #0056b3;">📝 إضافة تشخيص وصورة مستند للمريض:</legend>
            <br>
            
            <p>
                <label>اختر المريض:</label>
                <select name="patient_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    <?php
                    $patients = $conn->query("SELECT patient_id, full_name FROM patients");
                    while($p = $patients->fetch_assoc()) {
                        echo "<option value='".$p['patient_id']."'>".$p['full_name']."</option>";
                        }
                    ?>
                </select>
            </p>

            <p>
                <label>اختر الطبيب المعالج:</label>
                <select name="doctor_id" required style="width: 100%; padding: 8px; margin-top: 5px;">
                    <?php
                    $doctors = $conn->query("SELECT doctor_id, doctor_name FROM doctors");
                    while($d = $doctors->fetch_assoc()) {
                        echo "<option value='".$d['doctor_id']."'>".$d['doctor_name']."</option>";
                    }
                    ?>
                </select>
            </p>

            <p>
                <label>التشخيص الطبي للحالة:</label>
                <textarea name="diagnosis" required style="width: 100%; height: 60px; padding: 8px; margin-top: 5px;"></textarea>
            </p>

            <p>
                <label>الوصفة الطبية (العلاج والملاحظات):</label>
                <textarea name="prescription" style="width: 100%; height: 60px; padding: 8px; margin-top: 5px;"></textarea>
            </p>

            <p>
                <label style="font-weight: bold; color: red;">ارفع صورة التقرير الطبي أو الأشعة:</label>
                <input type="file" name="medical_image" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p>
                <label>تاريخ الزيارة:</label>
                <input type="date" name="visit_date" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </p>

            <p style="text-align: center;">
                <button type="submit" name="add_record" style="padding: 10px 25px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">حفظ السجل ورفع الصورة</button>
            </p>
        </form>

        <h3>📊 السجلات الطبية المرفوعة والتقارير الحية:</h3>
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; text-align: center; border-collapse: collapse; border: 1px solid #ddd;">
            <thead style="background-color: #f2f2f2;">
                <tr>
                    <th>رقم السجل</th>
                    <th>اسم المريض</th>
                    <th>الطبيب المعالج</th>
                    <th>التشخيص</th>
                    <th>العلاج</th>
                    <th>الصورة المرفوعة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { ?>
                    <?php while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['record_id']; ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['doctor_name']; ?></td>
                            <td><?php echo $row['diagnosis']; ?></td>
                            <td><?php echo $row['prescription']; ?></td>
                            <td>
                                <a href="<?php echo $row['attachment_path']; ?>" target="_blank">
                                    <img src="<?php echo $row['attachment_path']; ?>" width="70" style="border: 1px solid #ccc; padding: 2px; border-radius: 4px;" alt="مستند طبي">
                                </a>
                            </td>
                            <td><?php echo $row['visit_date']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" style="color: red; font-weight: bold;">لا توجد سجلات طبية أو صور مرفوعة حالياً.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</body>
</html>
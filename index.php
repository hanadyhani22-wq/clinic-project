<?php
// 1. بدء الجلسة وتضمين ملف الاتصال بقاعدة البيانات
session_start();
include 'db.php';

// 2. استقبال رسائل النجاح أو الخطأ
$message = "";
if (isset($_GET['error'])) {
    $message = "<div class='alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4' role='alert'>⚠️ الرجاء تعبئة جميع الحقول الإلزامية أو التحقق من الصيغة.<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
}
if (isset($_GET['success'])) {
    $message = "<div class='alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4' role='alert'>✨ تم حفظ بيانات المريض وصورة الحالة بنجاح!<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
}

// 3. جلب قائمة المرضى لعرضهم في الجدول
$sql_select = "SELECT * FROM patients ORDER BY patient_id DESC";
$result = $conn->query($sql_select);

// 4. حساب الإحصائيات لعرضها في الأعلى
$total_patients = $result->num_rows;

$sql_female = "SELECT COUNT(*) as count FROM patients WHERE gender = 'أنثى' OR gender = 'female'";
$res_female = $conn->query($sql_female);
$female_count = $res_female->fetch_assoc()['count'] ?? 0;

$sql_male = "SELECT COUNT(*) as count FROM patients WHERE gender = 'ذكر' OR gender = 'male'";
$res_male = $conn->query($sql_male);
$male_count = $res_male->fetch_assoc()['count'] ?? 0;

// --- استدعاء تقسيمات القالب المطلوبة من الدكتورة ---
include 'includes/head.php';
include 'includes/top.php';
include 'includes/sidebar.php';
?>
<?php include 'includes/head.php'; ?>

<!-- الصقي الكود هنا مباشرة تحت سطر الـ head -->
<style>
    table img, .table img, td img, [class*="table"] img {
        width: 45px !important;
        height: 45px !important;
        max-width: 45px !important;
        max-height: 45px !important;
        object-fit: cover !important;
        border-radius: 10px !important;
    }

    th, td {
        vertical-align: middle !important;
        padding: 12px 10px !important;
    }
    
    table, .table {
        width: 100% !important;
    }
</style>
<!-- عرض رسائل وتنبيهات النظام -->
<?php echo $message; ?>

<!-- قسم الإحصائيات الذكية مع تأثير التحويم المطور stat-card -->
<div class="row g-3 mb-4">
    <!-- كرت إجمالي الحالات -->
    <div class="col-md-4">
        <div class="p-3 bg-white border border-light rounded-4 shadow-sm d-flex align-items-center justify-content-between stat-card">
            <div>
                <p class="text-muted small mb-1 fw-semibold">إجمالي الحالات المسجلة</p>
                <h3 class="mb-0 fw-bold text-dark"><?php echo $total_patients; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-3 text-primary">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
        </div>
    </div>
    
    <!-- كرت حالات الإناث -->
    <div class="col-md-4">
        <div class="p-3 bg-white border border-light rounded-4 shadow-sm d-flex align-items-center justify-content-between stat-card">
            <div>
                <p class="text-muted small mb-1 fw-semibold">حالات الإناث</p>
                <h3 class="mb-0 fw-bold" style="color: #ff7675;"><?php echo $female_count; ?></h3>
            </div>
            <div class="p-3 rounded-3" style="background-color: #fff0f0; color: #ff7675;">
                <i class="bi bi-gender-female fs-4"></i>
            </div>
        </div>
    </div>

    <!-- كرت حالات الذكور -->
    <div class="col-md-4">
        <div class="p-3 bg-white border border-light rounded-4 shadow-sm d-flex align-items-center justify-content-between stat-card">
            <div>
                <p class="text-muted small mb-1 fw-semibold">حالات الذكور</p>
                <h3 class="mb-0 fw-bold text-secondary"><?php echo $male_count; ?></h3>
            </div>
            <div class="bg-light p-3 rounded-3 text-secondary">
                <i class="bi bi-gender-male fs-4"></i>
            </div>
        </div>
    </div>
</div>

<!-- 1. استمارة إضافة المريض -->
<div class="skin-card mb-5">
    <h2 class="main-title">
        <i class="bi bi-plus-circle"></i> تسجيل مريض جديد - عيادة الجلدية
    </h2>
    
    <form action="validate_patient.php" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">الاسم الكامل للمريض</label>
                <input type="text" name="full_name" class="form-control" placeholder="اسم المريض بالكامل" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">الجنس</label>
                <select name="gender" class="form-select" required>
                    <option value="أنثى">أنثى</option>
                    <option value="ذكر">ذكر</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control" placeholder="059XXXXXXX" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary small">تاريخ الميلاد</label>
                <input type="date" name="birth_date" class="form-control" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small">صورة الحالة الطبية</label>
                <input type="file" name="patient_image" class="form-control" accept="image/*" required>
            </div>
        </div>

        <button type="submit" name="submit" class="btn btn-submit w-100 mt-4 shadow-sm">
            <i class="bi bi-check2-circle"></i> حفظ البيانات في قاعدة البيانات
        </button>
    </form>
</div>

<!-- 2. جدول عرض السجلات -->
<div class="skin-card">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h2 class="main-title mb-0" style="color: #6c5ce7;">
            <i class="bi bi-collection"></i> السجلات الحالية في النظام
        </h2>
        <!-- [إضافة ذكية] حقل بحث سريع بالـ JavaScript لتصفية الجدول فوراً -->
        <div style="max-width: 300px;" class="w-100">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="بحث سريع عن مريض..." style="border-radius: 0 12px 12px 0;">
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-skin align-middle text-center mb-0" id="patientsTable">
            <thead>
                <tr class="table-light text-secondary small">
                    <th class="py-3 border-0">الرقم</th>
                    <th class="py-3 border-0">الاسم الكامل</th>
                    <th class="py-3 border-0">الجنس</th>
                    <th class="py-3 border-0">رقم الهاتف</th>
                    <th class="py-3 border-0">تاريخ الميلاد</th>
                    <th class="py-3 border-0">الصورة</th>
                    <th class="py-3 border-0">العمليات</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    $result->data_seek(0); 
                    while($row = $result->fetch_assoc()) {
                ?>
                        <tr style="border-bottom: 1px solid #f8f9fa;">
                            <td class="fw-bold text-muted">#<?php echo $row['patient_id']; ?></td>
                            <td class="fw-semibold text-dark search-target"><?php echo $row['full_name']; ?></td>
                            <td>
                                <?php if($row['gender'] == 'ذكر' || $row['gender'] == 'male'): ?>
                                    <span class="badge badge-m px-3 py-2 rounded-pill">ذكر</span>
                                <?php else: ?>
                                    <span class="badge badge-f px-3 py-2 rounded-pill">أنثى</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary"><?php echo $row['phone']; ?></td>
                            <td class="text-secondary"><?php echo $row['birth_date']; ?></td>
                            <td>
                                <?php if(!empty($row['patient_image']) && file_exists("uploads/" . $row['patient_image'])): ?>
                                    <img src="uploads/<?php echo $row['patient_image']; ?>" alt="الحالة">
                                <?php else: ?>
                                    <span class="text-muted small">لا توجد</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_patient.php?id=<?php echo $row['patient_id']; ?>" class="action-btn text-warning text-decoration-none me-2">
                                    <i class="bi bi-pencil"></i> تعديل
                                </a>
                                <a href="delete.php?id=<?php echo $row['patient_id']; ?>" class="action-btn text-danger text-decoration-none" onclick="return confirm('هل أنت متأكد من حذف هذا المريض؟');">
                                <i class="bi bi-trash"></i> حذف
                                </a>
                            </td>
                        </tr>
                <?php 
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="7" class="text-muted py-5 border-0">لا يوجد سجلات مضافة حتى الآن.</td>
                    </tr>
                <?php 
                } 
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- كود الـ JavaScript الخاص بالبحث السريع تلقائياً -->
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#patientsTable tbody tr');
    
    rows.forEach(row => {
        let nameCell = row.querySelector('.search-target');
        if (nameCell) {
            let textValue = nameCell.textContent || nameCell.innerText;
            if (textValue.toLowerCase().indexOf(filter) > -1) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        }
    });
});
</script>

<?php 
// --- استدعاء الأجزاء السفلية من مجلد includes ---
include 'includes/jsLinks.php'; 
?>
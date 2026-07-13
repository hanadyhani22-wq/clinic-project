</div> <!-- إغلاق وسم الـ row المضاف حديثاً للتقسيم الجانبي -->
</div> <!-- إغلاق الحاوية الرئيسية الأصلية -->

<!-- جافا سكريبت وباقي ميزات التفاعل -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function confirmDelete(id) {
        if (confirm("هل أنتِ متأكدة من حذف السجل؟")) {
            window.location.href = "delete_patient.php?id=" + id;
        }
    }
</script>
</body>
</html>
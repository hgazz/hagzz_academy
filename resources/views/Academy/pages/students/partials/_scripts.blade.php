<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('studentForm');
        const isArabic = @json($isArabic);

        function selectedText(id) {
            const element = document.getElementById(id);
            return element && element.value ? element.options[element.selectedIndex].text.trim() : '-';
        }

        function calculateAge(value) {
            if (!value) return '-';
            const birthDate = new Date(`${value}T00:00:00`);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const beforeBirthday = today.getMonth() < birthDate.getMonth()
                || (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate());
            if (beforeBirthday) age--;
            if (age < 0 || age > 120) return '-';
            return `${age} ${isArabic ? 'سنة' : 'years'}`;
        }

        function updatePreview() {
            const name = document.getElementById('studentName').value.trim();
            const phone = document.getElementById('studentPhone').value.trim();
            const email = document.getElementById('studentEmail').value.trim();
            const guardian = document.getElementById('guardianName').value.trim();
            const guardianPhone = document.getElementById('guardianPhone').value.trim();
            const status = document.getElementById('studentStatus');
            const statusBox = document.getElementById('previewStatus');

            document.getElementById('previewStudentName').textContent = name || (isArabic ? 'طالب جديد' : 'New student');
            document.getElementById('studentAvatar').textContent = (name || (isArabic ? 'ط' : 'S')).charAt(0).toLocaleUpperCase();
            document.getElementById('previewStudentContact').textContent = phone || email || '-';
            document.getElementById('previewAge').textContent = calculateAge(document.getElementById('studentBirthDate').value);
            document.getElementById('previewGender').textContent = selectedText('studentGender');
            document.getElementById('previewGuardian').textContent = guardian || '-';
            document.getElementById('previewGuardianPhone').textContent = guardianPhone || '-';
            statusBox.dataset.status = status.value;
            statusBox.querySelector('span').textContent = status.options[status.selectedIndex].text.trim();
        }

        form.addEventListener('input', updatePreview);
        form.addEventListener('change', updatePreview);
        updatePreview();
        if (window.feather) feather.replace();
    });
</script>

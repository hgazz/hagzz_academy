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

        const csrf = @json(csrf_token());
        const country = document.getElementById('country'), city = document.getElementById('city'), area = document.getElementById('area');
        async function loadOptions(url, payload, target, selected) {
            target.innerHTML = '<option value="">-</option>';
            if (!Object.values(payload)[0]) return;
            const response = await fetch(url, {method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body:JSON.stringify(payload)});
            (await response.json()).forEach(item => target.add(new Option(item.name, item.id, false, String(item.id) === String(selected))));
        }
        async function loadCities(){ await loadOptions(@json(route('academy.training.getCities')), {country_id:country.value}, city, city.dataset.selected); await loadAreas(); }
        async function loadAreas(){ await loadOptions(@json(route('academy.training.getAreaByCity')), {city_id:city.value}, area, area.dataset.selected); }
        country.addEventListener('change', () => { city.dataset.selected=''; area.dataset.selected=''; loadCities(); });
        city.addEventListener('change', () => { area.dataset.selected=''; loadAreas(); });
        loadCities();
    });
</script>

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

        const statusLabels = {
            'active': isArabic ? 'نشط' : 'Active',
            'inactive': isArabic ? 'غير نشط' : 'Inactive',
            'suspended': isArabic ? 'موقوف' : 'Suspended'
        };

        function updatePreview() {
            const name = document.getElementById('studentName').value.trim();
            const phone = document.getElementById('studentPhone').value.trim();
            const email = document.getElementById('studentEmail').value.trim();
            const guardian = document.getElementById('guardianName').value.trim();
            const guardianPhone = document.getElementById('guardianPhone').value.trim();
            const statusInput = document.getElementById('studentStatus');
            const statusBox = document.getElementById('previewStatus');

            document.getElementById('previewStudentName').textContent = name || (isArabic ? 'طالب جديد' : 'New student');
            document.getElementById('studentAvatar').textContent = (name || (isArabic ? 'ط' : 'S')).charAt(0).toLocaleUpperCase();
            document.getElementById('previewStudentContact').textContent = phone || email || '-';
            document.getElementById('previewAge').textContent = calculateAge(document.getElementById('studentBirthDate').value);
            document.getElementById('previewGender').textContent = selectedText('studentGender');
            document.getElementById('previewGuardian').textContent = guardian || '-';
            document.getElementById('previewGuardianPhone').textContent = guardianPhone || '-';
            
            const currentVal = statusInput.value || 'active';
            statusBox.dataset.status = currentVal;
            statusBox.querySelector('span').textContent = statusLabels[currentVal] || currentVal;
        }

        // Status Toggle Pill Selector Click Event
        const statusPills = document.querySelectorAll('.status-toggle-pill');
        const statusInput = document.getElementById('studentStatus');
        statusPills.forEach(pill => {
            pill.addEventListener('click', function () {
                statusPills.forEach(p => p.classList.remove('selected'));
                this.classList.add('selected');
                statusInput.value = this.dataset.value;
                updatePreview();
            });
        });

        // Club Member Toggle Box
        const clubSelect = document.getElementById('clubMemberSelect');
        const clubBox = document.getElementById('clubDetailsBox');
        if (clubSelect && clubBox) {
            clubSelect.addEventListener('change', function () {
                if (this.value === 'yes') {
                    clubBox.style.display = 'block';
                } else {
                    clubBox.style.display = 'none';
                }
            });
        }

        // File Inputs Label Update
        function bindFileInputLabel(inputId, labelId) {
            const fileInput = document.getElementById(inputId);
            const labelSpan = document.getElementById(labelId);
            if (fileInput && labelSpan) {
                fileInput.addEventListener('change', function () {
                    if (this.files && this.files.length > 0) {
                        labelSpan.textContent = this.files[0].name;
                        labelSpan.style.color = '#172033';
                        labelSpan.style.fontWeight = '700';
                    }
                });
            }
        }
        bindFileInputLabel('clubCardFileInput', 'clubCardFileText');
        bindFileInputLabel('medicalCertificateInput', 'medicalCertFileText');

        form.addEventListener('input', updatePreview);
        form.addEventListener('change', updatePreview);
        updatePreview();
        if (window.feather) feather.replace();

        // Dynamic Country -> City -> Area Dropdowns & Auto Country Code
        const csrf = @json(csrf_token());
        const country = document.getElementById('country'), city = document.getElementById('city'), area = document.getElementById('area');
        const countryCodeInput = document.getElementById('countryCodeInput');

        async function loadOptions(url, payload, target, selected) {
            target.innerHTML = '<option value="">-</option>';
            if (!Object.values(payload)[0]) return;
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                data.forEach(item => {
                    let displayName = item.name;
                    if (typeof displayName === 'object' && displayName !== null) {
                        displayName = displayName[isArabic ? 'ar' : 'en'] || Object.values(displayName)[0] || '-';
                    }
                    target.add(new Option(displayName, item.id, false, String(item.id) === String(selected)));
                });
            } catch (e) {
                console.error('Error loading options:', e);
            }
        }

        async function loadCities() {
            await loadOptions(@json(route('academy.training.getCities')), { country_id: country.value }, city, city.dataset.selected);
            await loadAreas();
        }

        async function loadAreas() {
            await loadOptions(@json(route('academy.training.getAreaByCity')), { city_id: city.value }, area, area.dataset.selected);
        }

        country.addEventListener('change', () => {
            city.dataset.selected = '';
            area.dataset.selected = '';
            loadCities();
        });

        city.addEventListener('change', () => {
            area.dataset.selected = '';
            loadAreas();
        });

        if (country && country.value) {
            loadCities();
        }
    });
</script>

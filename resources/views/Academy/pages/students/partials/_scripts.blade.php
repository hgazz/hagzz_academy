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

        const codeMap = {
            'EG': '+20', 'SA': '+966', 'AE': '+971', 'QA': '+974', 'KW': '+965',
            'OM': '+968', 'BH': '+973', 'JO': '+962', 'LB': '+961', 'IQ': '+964',
            'LY': '+218', 'SD': '+249', 'TN': '+216', 'MA': '+212', 'DZ': '+213',
            'YE': '+967', 'SY': '+963', 'PS': '+970', 'TR': '+90', 'GB': '+44',
            'US': '+1', 'CA': '+1', 'FR': '+33', 'DE': '+49', 'ES': '+34', 'IT': '+39'
        };

        const customCityShell = document.getElementById('customCityShell');
        const customCityInput = document.getElementById('customCityInput');
        const customAreaShell = document.getElementById('customAreaShell');
        const customAreaInput = document.getElementById('customAreaInput');

        async function loadOptions(url, payload, target, selected) {
            target.innerHTML = '<option value="">' + (isArabic ? 'اختر...' : 'Select...') + '</option>';
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
                let foundSelected = false;
                data.forEach(item => {
                    let displayName = item.name;
                    if (typeof displayName === 'object' && displayName !== null) {
                        displayName = displayName[isArabic ? 'ar' : 'en'] || Object.values(displayName)[0] || '-';
                    }
                    const optVal = item.id !== undefined && item.id !== null ? item.id : item.name;
                    const isSel = String(optVal) === String(selected) || String(displayName) === String(selected);
                    if (isSel) foundSelected = true;
                    target.add(new Option(displayName, optVal, false, isSel));
                });

                target.add(new Option(isArabic ? '✏️ أخرى (أدخل اسمها يدوياً)' : '✏️ Other (Type custom)', '__custom__', false, String(selected) === '__custom__'));

                if (selected && !foundSelected && String(selected) !== '__custom__') {
                    if (target === city && customCityShell && customCityInput) {
                        customCityShell.style.display = 'flex';
                        customCityInput.value = selected;
                        target.value = '__custom__';
                    } else if (target === area && customAreaShell && customAreaInput) {
                        customAreaShell.style.display = 'flex';
                        customAreaInput.value = selected;
                        target.value = '__custom__';
                    }
                }
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

        country.addEventListener('change', function () {
            const selectedOpt = country.options[country.selectedIndex];
            const iso2 = selectedOpt ? selectedOpt.dataset.iso2 : '';
            if (countryCodeInput && iso2 && codeMap[iso2]) {
                countryCodeInput.value = codeMap[iso2];
            }
            if (customCityShell) customCityShell.style.display = 'none';
            if (customAreaShell) customAreaShell.style.display = 'none';
            city.dataset.selected = '';
            area.dataset.selected = '';
            loadCities();
        });

        city.addEventListener('change', function () {
            if (this.value === '__custom__') {
                if (customCityShell) customCityShell.style.display = 'flex';
                if (customCityInput) customCityInput.focus();
            } else {
                if (customCityShell) customCityShell.style.display = 'none';
            }
            if (customAreaShell) customAreaShell.style.display = 'none';
            area.dataset.selected = '';
            loadAreas();
        });

        area.addEventListener('change', function () {
            if (this.value === '__custom__') {
                if (customAreaShell) customAreaShell.style.display = 'flex';
                if (customAreaInput) customAreaInput.focus();
            } else {
                if (customAreaShell) customAreaShell.style.display = 'none';
            }
        });

        if (country && country.value) {
            const selectedOpt = country.options[country.selectedIndex];
            const iso2 = selectedOpt ? selectedOpt.dataset.iso2 : '';
            if (countryCodeInput && (!countryCodeInput.value || countryCodeInput.value === '+20') && iso2 && codeMap[iso2]) {
                countryCodeInput.value = codeMap[iso2];
            }
            loadCities();
        }
    });
</script>

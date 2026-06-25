<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('groupForm');
        const studentOptions = Array.from(document.querySelectorAll('.group-student-option'));
        const studentSearch = document.getElementById('studentSearch');
        const noResults = document.getElementById('noStudentResults');

        function selectedText(id) {
            const select = document.getElementById(id);
            if (!select || !select.value) return '-';
            return select.options[select.selectedIndex].text.trim();
        }

        function updateStudentCount() {
            const count = form.querySelectorAll('input[name="student_ids[]"]:checked').length;
            document.getElementById('selectedStudentsCount').textContent = count;
            document.getElementById('summaryStudents').textContent = count;
        }

        function updateSummary() {
            const name = document.getElementById('groupName').value.trim();
            const start = document.getElementById('startTime').value;
            const end = document.getElementById('endTime').value;
            const selectedDays = Array.from(form.querySelectorAll('input[name="days[]"]:checked'))
                .map(input => input.nextElementSibling.textContent.trim());
            const status = document.getElementById('groupStatus');
            const statusBox = document.getElementById('summaryStatus');

            document.getElementById('summaryName').textContent = name || @json($isArabic ? 'مجموعة جديدة' : 'New group');
            document.getElementById('summaryTraining').textContent = selectedText('trainingId');
            document.getElementById('summaryCoach').textContent = selectedText('coachId');
            document.getElementById('summaryDays').textContent = selectedDays.join(@json($isArabic ? '، ' : ', ')) || '-';
            document.getElementById('summaryTime').textContent = [start, end].filter(Boolean).join(' - ') || '-';
            statusBox.classList.toggle('is-inactive', status.value === 'inactive');
            statusBox.querySelector('span').textContent = status.options[status.selectedIndex].text.trim();
        }

        form.addEventListener('input', function () {
            updateSummary();
            updateStudentCount();
        });
        form.addEventListener('change', function () {
            updateSummary();
            updateStudentCount();
        });

        if (studentSearch) {
            studentSearch.addEventListener('input', function () {
                const query = studentSearch.value.trim().toLocaleLowerCase();
                let visible = 0;
                studentOptions.forEach(option => {
                    const matches = !query || option.dataset.search.includes(query);
                    option.hidden = !matches;
                    if (matches) visible++;
                });
                noResults.classList.toggle('is-visible', studentOptions.length > 0 && visible === 0);
            });
        }

        updateSummary();
        updateStudentCount();
        if (window.feather) feather.replace();
    });
</script>

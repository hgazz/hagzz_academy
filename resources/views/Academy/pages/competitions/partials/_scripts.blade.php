<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather) {
            feather.replace();
        }

        var status = document.getElementById('competitionStatus');
        var resultFields = document.getElementById('resultFields');
        var playerSearch = document.getElementById('playerSearch');
        var playerItems = Array.prototype.slice.call(document.querySelectorAll('.competition-player'));
        var noResults = document.getElementById('noPlayerResults');
        var selectedCount = document.getElementById('selectedPlayersCount');
        var summaryMatch = document.getElementById('summaryMatch');
        var homeInput = document.querySelector('[name="home_team_name"]');
        var opponentInput = document.querySelector('[name="opponent_name"]');

        function toggleResultFields() {
            if (!resultFields || !status) return;
            resultFields.hidden = status.value !== 'completed';
        }

        function updateSelectedCount() {
            if (!selectedCount) return;
            selectedCount.textContent = document.querySelectorAll('.competition-player input:checked').length;
        }

        function updateSummary() {
            if (!summaryMatch || !homeInput || !opponentInput) return;
            summaryMatch.textContent = (homeInput.value || '-') + ' vs ' + (opponentInput.value || '-');
        }

        if (status) {
            status.addEventListener('change', toggleResultFields);
            toggleResultFields();
        }

        if (playerSearch) {
            playerSearch.addEventListener('input', function () {
                var term = playerSearch.value.trim().toLowerCase();
                var shown = 0;
                playerItems.forEach(function (item) {
                    var visible = !term || (item.dataset.search || '').indexOf(term) !== -1;
                    item.hidden = !visible;
                    if (visible) shown++;
                });
                if (noResults) noResults.style.display = shown ? 'none' : 'block';
            });
        }

        document.querySelectorAll('.competition-player input').forEach(function (checkbox) {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        [homeInput, opponentInput].forEach(function (input) {
            if (input) input.addEventListener('input', updateSummary);
        });

        updateSelectedCount();
        updateSummary();
    });
</script>

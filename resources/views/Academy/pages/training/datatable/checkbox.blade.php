<input type="checkbox" name="bulk[]" class="child-chk" value="{{$training->id}}">


<script>
    let checkboxes = document.querySelectorAll('.child-chk');
    let button = document.querySelector('.dt-button');
    let ids = document.getElementById('ids');
    checkboxes.forEach(check => {
        check.addEventListener('click', function() {
            let checkedValues = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedValues.push(checkbox.value);
                }
            });
            ids.value = JSON.stringify(checkedValues);
            button.classList.toggle('d-none', checkedValues.length === 0);
        });
    });
</script>

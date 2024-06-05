<input type="checkbox" name="publish[]" class="publish" value="{{$training->id}}">


<script>
    let publishCheckboxes = document.querySelectorAll('.publish');
    let pub_button = document.querySelector('.pub-button');
    let pub_ids = document.getElementById('pub_ids');
    publishCheckboxes.forEach(publish=>{
        publish.addEventListener('click', function(){
            let pubValues = [];
            publishCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    pubValues.push(checkbox.value);
                }
            });
            pub_ids.value = JSON.stringify(pubValues);
            pub_button.classList.toggle('d-none', pubValues.length === 0);
        })
    })
</script>



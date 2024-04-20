
@csrf
{{--@dd($gallery)--}}
<div class="row">
    <div class="profile-image">
        <div class="img-uploader-content">
            <input type="file" class="form-control" placeholder
                   name="image" accept="image/png, image/jpeg, image/gif"  onchange="previewImage(event)"/>
            <img id="imagePreview" src="{{isset($gallery) ? $gallery->image: '#'}}" alt="Image Preview" width="400px" height="400px" class="mt-3 @if(isset($gallery)) 'd-block' @else 'd-block' @endif">

        </div>

    </div>
</div>

@push('js')
    <script>
        function previewImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    imagePreview.src = e.target.result;
                    imagePreview.classList.replace('d-none','d-block');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endpush

let lang = $('meta[name="lang"]').attr('content');
translate  ={
    "title_del" : {
        'en': 'Are you sure ?',
        'ar': "هل أنت متأكد ؟",
    },
    "text_del" :{
        'en':'You will not be able to recover this ',
        'ar':'لن تتمكن من استرداد هذا'
    },
    "cancel_btn" :{
        'en':'Cancel' ,
        'ar':'إلغاء'
    },
    'submit_btn':{
        'en':'Submit' ,
        'ar':'إرسال'
    },
    "confirm_del":{
        'en':'Yes, delete it!' ,
        'ar':'نعم , أحذفه !'
    },
    "title_del2" : {
        'en': 'Are you sure you want to delete this record?',
        'ar': "هل أنت متأكد أنك تريد حذف هذا السجل؟",
    },

    "text_del2" : {
        'en': 'If you delete this, it will be gone forever',
        'ar': "إذا حذفت هذا ، فسيختفي إلى الأبد",
    },
    "removed" : {
        'en': 'Removed!',
        'ar': 'إزالة!',
    },
    "messageSuccess" : {
        'en': 'Record Has Been Removed !',
        'ar': '! تم حذف السجل ',
    },

    "messageError" : {
        'en': 'Record Removed Failed',
        'ar': 'فشل حذف السجل',
    },
}
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    $('.table').on('init.dt', function () {

        $(document).on('click', '.show_confirm_two',function (event) {
            var url = $(this).data('href');
            var id = $(this).data('id');
            var name = $(this).data('name');
            var token = $('#token').val();
            var parent = $(this).parent();


            Swal.fire({
                title: translate.title_del2[lang],
                text: translate.text_del2[lang],
                icon: 'warning',
                padding: '3em',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: translate.confirm_del[lang],
                cancelButtonText: translate.cancel_btn[lang],
            }).then((result) => {
                if (result.isConfirmed) {
                    let temp = `#${name}-${id}`;

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { id: id },
                        dataType: 'json'
                        , success: function (res) {
                            if (res.data.status === 'success'){
                                $(temp).remove();
                                Swal.fire(
                                    res.data.model,
                                    res.data.message,
                                    'success'
                                )
                                parent.slideUp(300, function () {
                                    parent.closest("tr").remove();
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    `Error : ${res.message} !`,
                                    'error'
                                )
                            }


                        }, error: function (resp) {
                            Swal.fire(
                                translate.removed[lang],
                                translate.messageError[lang],
                                `error`,
                            )
                        }
                    });
                }
            });
        });

    })
});

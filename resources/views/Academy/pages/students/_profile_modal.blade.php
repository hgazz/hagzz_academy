@php($profileArabic = app()->getLocale() === 'ar')
@once
<style>
    .student-profile-trigger{padding:0;border:0;background:transparent;color:#0f766e;font-weight:800;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px;cursor:pointer}.student-profile-trigger:hover{color:#134e4a}
    .student-profile-modal .modal-dialog{max-width:960px}.student-profile-modal .modal-content{border:0;border-radius:10px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15)}.spm-head{display:flex;align-items:center;gap:15px;padding:22px;background:linear-gradient(135deg,#0f766e,#155e75);color:#fff}.spm-head img{width:86px;height:86px;object-fit:cover;border:3px solid rgba(255,255,255,.75);border-radius:10px;background:#fff}.spm-head h3{color:#fff;margin:0 0 5px;font-weight:700}.spm-head p{margin:0;color:rgba(255,255,255,.85)}.spm-status{margin-inline-start:auto;padding:6px 12px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:800}.spm-body{padding:20px;background:#f8fafc}.spm-loading,.spm-error{padding:45px;text-align:center;color:#64748b}.spm-error{color:#b91c1c}.spm-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}.spm-card{padding:16px;border:1px solid #e2e8f0;border-radius:10px;background:#fff;min-width:0}.spm-card.wide{grid-column:span 3}.spm-card h4{display:flex;align-items:center;gap:8px;margin:0 0 14px;color:#102a43;font-size:15px;font-weight:700}.spm-card h4 svg,.spm-card h4 i{width:18px;color:#0f766e}.spm-facts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.spm-fact span{display:block;color:#64748b;font-size:11px;margin-bottom:3px}.spm-fact strong{display:block;color:#1e293b;font-size:13px;overflow-wrap:anywhere}.spm-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:8px}.spm-stat{text-align:center;padding:10px 5px;border-radius:8px;background:#f1f5f9}.spm-stat b{display:block;color:#102a43;font-size:18px;font-weight:700}.spm-stat small{color:#64748b;font-size:10px}.spm-history{width:100%;border-collapse:collapse}.spm-history th{padding:9px;background:#f8fafc;color:#475569;font-size:12px;font-weight:700;border-bottom:1px solid #e2e8f0;text-align:start}.spm-history td{padding:10px 9px;border-bottom:1px solid #f1f5f9;font-size:12px}.spm-footer{display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:#fff;border-top:1px solid #e2e8f0}.spm-empty{color:#64748b;text-align:center;padding:12px}.spm-notes{white-space:pre-wrap;color:#475569;font-size:13px}
    @media(max-width:767px){.student-profile-modal .modal-dialog{margin:0;max-width:none;min-height:100%}.student-profile-modal .modal-content{min-height:100vh;border-radius:0}.spm-head{align-items:flex-start;flex-wrap:wrap}.spm-head img{width:70px;height:70px}.spm-status{margin-inline-start:0}.spm-grid{grid-template-columns:1fr}.spm-card.wide{grid-column:auto}.spm-stats{grid-template-columns:repeat(2,1fr)}.spm-facts{grid-template-columns:1fr 1fr}.spm-body{padding:12px}}
</style>

<div class="modal fade student-profile-modal" id="studentProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div id="studentProfileLoading" class="spm-loading">
                <i class="fa-solid fa-spinner fa-spin fa-2x mb-2 d-block text-teal"></i>
                {{ $profileArabic ? 'جارٍ تحميل ملف الطالب والبيانات المالية...' : 'Loading student profile & financials...' }}
            </div>
            <div id="studentProfileError" class="spm-error d-none">
                <i class="fa-solid fa-circle-exclamation fa-2x mb-2 d-block"></i>
                {{ $profileArabic ? 'تعذر تحميل بيانات الطالب.' : 'Could not load student data.' }}
            </div>
            <div id="studentProfileContent" class="d-none">
                <header class="spm-head">
                    <img id="spmImage" alt="">
                    <div>
                        <h3 id="spmName"></h3>
                        <p id="spmContact"></p>
                    </div>
                    <span id="spmStatus" class="spm-status"></span>
                </header>
                <div class="spm-body">
                    <div class="spm-grid">
                        <section class="spm-card">
                            <h4><i class="fa-solid fa-user"></i> {{ $profileArabic ? 'البيانات الشخصية' : 'Personal information' }}</h4>
                            <div id="spmPersonal" class="spm-facts"></div>
                        </section>
                        <section class="spm-card">
                            <h4><i class="fa-solid fa-users"></i> {{ $profileArabic ? 'ولي الأمر والمجموعات' : 'Guardian & groups' }}</h4>
                            <div id="spmGuardian" class="spm-facts"></div>
                        </section>
                        <section class="spm-card">
                            <h4><i class="fa-solid fa-wallet"></i> {{ $profileArabic ? 'الملخص المالي الشامل' : 'Financial summary' }}</h4>
                            <div id="spmFinancial" class="spm-facts"></div>
                        </section>
                        <section class="spm-card wide">
                            <h4><i class="fa-solid fa-receipt"></i> {{ $profileArabic ? 'سجل الاشتراكات والمدفوعات والخصومات' : 'Subscriptions & Financial History' }}</h4>
                            <div class="table-responsive">
                                <table class="spm-history">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ $profileArabic ? 'المجموعة / الفترة' : 'Group / Period' }}</th>
                                            <th>{{ $profileArabic ? 'القيمة' : 'Amount' }}</th>
                                            <th>{{ $profileArabic ? 'المدفوع' : 'Paid' }}</th>
                                            <th>{{ $profileArabic ? 'الخصم المعتمد' : 'Discount' }}</th>
                                            <th>{{ $profileArabic ? 'المتبقي' : 'Remaining' }}</th>
                                            <th>{{ $profileArabic ? 'الحالة' : 'Status' }}</th>
                                            <th class="text-center">{{ $profileArabic ? 'الفاتورة' : 'Invoice' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="spmSubscriptionsHistory"></tbody>
                                </table>
                            </div>
                        </section>
                        <section class="spm-card wide">
                            <h4><i class="fa-solid fa-clipboard-user"></i> {{ $profileArabic ? 'إحصائيات الحضور والغياب' : 'Attendance Statistics' }}</h4>
                            <div id="spmAttendance" class="spm-stats"></div>
                        </section>
                        <section class="spm-card wide">
                            <h4><i class="fa-solid fa-clock-rotate-left"></i> {{ $profileArabic ? 'آخر سجلات الحضور' : 'Recent attendance' }}</h4>
                            <div class="table-responsive">
                                <table class="spm-history">
                                    <thead>
                                        <tr>
                                            <th>{{ $profileArabic ? 'التاريخ' : 'Date' }}</th>
                                            <th>{{ $profileArabic ? 'المجموعة' : 'Group' }}</th>
                                            <th>{{ $profileArabic ? 'الحالة' : 'Status' }}</th>
                                            <th>{{ $profileArabic ? 'وقت التسجيل' : 'Check-in' }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="spmHistory"></tbody>
                                </table>
                            </div>
                        </section>
                        <section class="spm-card wide">
                            <h4><i class="fa-solid fa-notes-medical"></i> {{ $profileArabic ? 'الملاحظات الطبية والعامة' : 'Medical & general notes' }}</h4>
                            <div id="spmNotes" class="spm-notes"></div>
                        </section>
                    </div>
                </div>
                <footer class="spm-footer">
                    <div>
                        <a id="spmCardLink" class="btn btn-outline-primary d-inline-flex align-items-center gap-1" target="_blank" href="#">
                            <i class="fa-solid fa-id-card"></i> {{ $profileArabic ? 'عرض / طباعة كارت اللاعب' : 'Member Card' }}
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-light" data-bs-dismiss="modal">{{ $profileArabic ? 'إغلاق' : 'Close' }}</button>
                        <a id="spmEdit" class="btn btn-primary d-inline-flex align-items-center gap-1" href="#">
                            <i class="fa-solid fa-pen-to-square"></i> {{ $profileArabic ? 'تعديل بيانات الطالب' : 'Edit student' }}
                        </a>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const ar=@json($profileArabic), modalElement=document.getElementById('studentProfileModal'), modal=new bootstrap.Modal(modalElement);
 const loading=document.getElementById('studentProfileLoading'),error=document.getElementById('studentProfileError'),content=document.getElementById('studentProfileContent');
 const labels={phone:ar?'الهاتف':'Phone',email:ar?'البريد الإلكتروني':'Email',gender:ar?'النوع':'Gender',birth:ar?'تاريخ الميلاد':'Birth date',age:ar?'العمر':'Age',location:ar?'العنوان':'Location',guardian:ar?'اسم ولي الأمر':'Guardian',guardianPhone:ar?'هاتف ولي الأمر':'Guardian phone',groups:ar?'المجموعات':'Groups',due:ar?'إجمالي المستحق':'Total due',paid:ar?'إجمالي المدفوع':'Total paid',discount:ar?'إجمالي الخصومات':'Total discounts',remaining:ar?'إجمالي المتبقي':'Total remaining',present:ar?'حضور':'Present',late:ar?'تأخير':'Late',absent:ar?'غياب':'Absent',excused:ar?'غياب بعذر':'Excused',total:ar?'الإجمالي':'Total',day:ar?'يوم':'days',year:ar?'سنة':'years',none:ar?'غير مسجل':'Not recorded',paidState:ar?'مدفوع':'Paid',partialState:ar?'جزئي':'Partial',unpaidState:ar?'غير مدفوع':'Unpaid'};
 const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
 const fact=(label,value,extraClass='')=>'<div class="spm-fact"><span>'+esc(label)+'</span><strong class="'+extraClass+'">'+esc(value||labels.none)+'</strong></div>';
 const money=v=>Number(v||0).toLocaleString(ar?'ar-EG':'en-US',{minimumFractionDigits:2,maximumFractionDigits:2}) + ' EGP';
 
 document.querySelectorAll('[data-student-profile-url]').forEach(button=>button.addEventListener('click',async()=>{
   loading.classList.remove('d-none');error.classList.add('d-none');content.classList.add('d-none');modal.show();
   try{
     const response=await fetch(button.dataset.studentProfileUrl,{headers:{Accept:'application/json'}});
     if(!response.ok)throw new Error(response.status);
     const d=await response.json(), a=d.attendance, subs=d.all_subscriptions||[];
     
     const image=document.getElementById('spmImage');
     image.onerror=()=>{image.onerror=null;image.src=d.fallback_image};
     image.src=d.image||d.fallback_image;
     
     document.getElementById('spmName').textContent=d.name;
     document.getElementById('spmContact').textContent=[d.phone,d.email].filter(Boolean).join(' · ');
     document.getElementById('spmStatus').textContent=d.status;
     document.getElementById('spmEdit').href=d.edit_url;
     document.getElementById('spmCardLink').href=d.card_url;
     
     document.getElementById('spmPersonal').innerHTML=fact(labels.phone,d.phone)+fact(labels.email,d.email)+fact(labels.gender,d.gender)+fact(labels.birth,d.birth_date)+fact(labels.age,d.age?d.age+' '+labels.year:null)+fact(labels.location,d.location);
     document.getElementById('spmGuardian').innerHTML=fact(labels.guardian,d.guardian_name)+fact(labels.guardianPhone,d.guardian_phone)+fact(labels.groups,(d.groups||[]).join('، '));
     
     let finHtml = fact(labels.due, money(d.financials.total_due)) + fact(labels.paid, money(d.financials.total_paid), 'text-success');
     if (d.financials.total_discount > 0) {
         finHtml += fact(labels.discount, '- ' + money(d.financials.total_discount), 'text-purple');
     }
     finHtml += fact(labels.remaining, money(d.financials.total_remaining), d.financials.total_remaining > 0 ? 'text-danger' : 'text-success');
     document.getElementById('spmFinancial').innerHTML = finHtml;
     
     if (subs.length > 0) {
         document.getElementById('spmSubscriptionsHistory').innerHTML = subs.map(s => {
             const discText = s.discount > 0 ? '<span class="badge bg-purple bg-opacity-10 text-purple border border-purple" style="color:#7e22ce;" title="' + esc(s.discount_reason || '') + ' (' + esc(s.discount_approved_by || '') + ')">- ' + money(s.discount) + '</span>' : '-';
             const remText = s.remaining > 0 ? '<span class="badge bg-danger bg-opacity-10 text-danger fw-bold">' + money(s.remaining) + '</span>' : '<span class="text-success fw-bold">0.00</span>';
             const pBadge = s.payment_status === 'paid' ? '<span class="badge bg-success">' + labels.paidState + '</span>' : (s.payment_status === 'partial' ? '<span class="badge bg-warning text-dark">' + labels.partialState + '</span>' : '<span class="badge bg-danger">' + labels.unpaidState + '</span>');
             
             return '<tr>'
                 + '<td><b>#' + s.id + '</b></td>'
                 + '<td><strong>' + esc(s.group) + '</strong><small class="d-block text-muted">' + esc(s.starts_on) + ' — ' + esc(s.ends_on) + '</small></td>'
                 + '<td class="fw-bold">' + money(s.amount) + '</td>'
                 + '<td class="text-success fw-bold">' + money(s.paid) + '</td>'
                 + '<td>' + discText + '</td>'
                 + '<td>' + remText + '</td>'
                 + '<td>' + pBadge + '</td>'
                 + '<td class="text-center"><a href="' + s.invoice_url + '" target="_blank" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-receipt"></i></a></td>'
                 + '</tr>';
         }).join('');
     } else {
         document.getElementById('spmSubscriptionsHistory').innerHTML = '<tr><td colspan="8" class="spm-empty">' + labels.none + '</td></tr>';
     }
     
     document.getElementById('spmAttendance').innerHTML=['present','late','absent','excused','total'].map(k=>'<div class="spm-stat"><b>'+Number(a[k]||0).toLocaleString()+'</b><small>'+esc(labels[k])+'</small></div>').join('');
     document.getElementById('spmHistory').innerHTML=(d.recent_attendance||[]).length?(d.recent_attendance||[]).map(r=>'<tr><td>'+esc(r.date)+'</td><td>'+esc(r.group||labels.none)+'</td><td>'+esc(labels[r.status]||r.status)+'</td><td>'+esc(r.check_in||'')+'</td></tr>').join(''):'<tr><td colspan="4" class="spm-empty">'+labels.none+'</td></tr>';
     document.getElementById('spmNotes').textContent=[d.medical_notes,d.notes].filter(Boolean).join('\n\n')||labels.none;
     
     loading.classList.add('d-none');
     content.classList.remove('d-none');
   }catch(e){
     console.error('[Hagzz] Student profile failed',e);
     loading.classList.add('d-none');
     error.classList.remove('d-none');
   }
 }));
});
</script>
@endpush
@endonce

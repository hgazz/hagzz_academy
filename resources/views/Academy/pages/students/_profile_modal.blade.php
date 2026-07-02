@php($profileArabic = app()->getLocale() === 'ar')
@once
<style>
    .student-profile-trigger{padding:0;border:0;background:transparent;color:#0f766e;font-weight:800;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:3px}.student-profile-trigger:hover{color:#134e4a}
    .student-profile-modal .modal-dialog{max-width:920px}.student-profile-modal .modal-content{border:0;border-radius:8px;overflow:hidden}.spm-head{display:flex;align-items:center;gap:15px;padding:22px;background:linear-gradient(135deg,#0f766e,#155e75);color:#fff}.spm-head img{width:86px;height:86px;object-fit:cover;border:3px solid rgba(255,255,255,.75);border-radius:8px;background:#fff}.spm-head h3{color:#fff;margin:0 0 5px}.spm-head p{margin:0;color:rgba(255,255,255,.78)}.spm-status{margin-inline-start:auto;padding:6px 10px;border-radius:999px;background:#dcfce7;color:#166534;font-size:12px;font-weight:800}.spm-body{padding:20px;background:#f8fafc}.spm-loading,.spm-error{padding:45px;text-align:center;color:#64748b}.spm-error{color:#b91c1c}.spm-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:11px}.spm-card{padding:14px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;min-width:0}.spm-card.wide{grid-column:span 3}.spm-card h4{display:flex;align-items:center;gap:7px;margin:0 0 12px;color:#102a43;font-size:15px}.spm-card h4 svg{width:17px;color:#0f766e}.spm-facts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}.spm-fact span{display:block;color:#64748b;font-size:11px;margin-bottom:3px}.spm-fact strong{display:block;color:#1e293b;font-size:13px;overflow-wrap:anywhere}.spm-stats{display:grid;grid-template-columns:repeat(5,1fr);gap:8px}.spm-stat{text-align:center;padding:10px 5px;border-radius:7px;background:#f1f5f9}.spm-stat b{display:block;color:#102a43;font-size:19px}.spm-stat small{color:#64748b;font-size:10px}.spm-history{width:100%;border-collapse:collapse}.spm-history td{padding:8px;border-bottom:1px solid #f1f5f9;font-size:12px}.spm-footer{display:flex;justify-content:flex-end;gap:8px;padding:14px 20px;background:#fff;border-top:1px solid #e2e8f0}.spm-empty{color:#64748b;text-align:center;padding:12px}.spm-notes{white-space:pre-wrap;color:#475569;font-size:13px}
    @media(max-width:767px){.student-profile-modal .modal-dialog{margin:0;max-width:none;min-height:100%}.student-profile-modal .modal-content{min-height:100vh;border-radius:0}.spm-head{align-items:flex-start;flex-wrap:wrap}.spm-head img{width:70px;height:70px}.spm-status{margin-inline-start:0}.spm-grid{grid-template-columns:1fr}.spm-card.wide{grid-column:auto}.spm-stats{grid-template-columns:repeat(2,1fr)}.spm-facts{grid-template-columns:1fr 1fr}.spm-body{padding:12px}}
</style>

<div class="modal fade student-profile-modal" id="studentProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
        <div id="studentProfileLoading" class="spm-loading">{{ $profileArabic ? 'جارٍ تحميل بيانات الطالب...' : 'Loading student profile...' }}</div>
        <div id="studentProfileError" class="spm-error d-none">{{ $profileArabic ? 'تعذر تحميل بيانات الطالب.' : 'Could not load student data.' }}</div>
        <div id="studentProfileContent" class="d-none">
            <header class="spm-head"><img id="spmImage" alt=""><div><h3 id="spmName"></h3><p id="spmContact"></p></div><span id="spmStatus" class="spm-status"></span></header>
            <div class="spm-body"><div class="spm-grid">
                <section class="spm-card"><h4><i data-feather="user"></i>{{ $profileArabic ? 'البيانات الشخصية' : 'Personal information' }}</h4><div id="spmPersonal" class="spm-facts"></div></section>
                <section class="spm-card"><h4><i data-feather="users"></i>{{ $profileArabic ? 'ولي الأمر والمجموعات' : 'Guardian & groups' }}</h4><div id="spmGuardian" class="spm-facts"></div></section>
                <section class="spm-card"><h4><i data-feather="credit-card"></i>{{ $profileArabic ? 'الملخص المالي' : 'Financial summary' }}</h4><div id="spmFinancial" class="spm-facts"></div></section>
                <section class="spm-card wide"><h4><i data-feather="calendar"></i>{{ $profileArabic ? 'الاشتراك الحالي' : 'Current subscription' }}</h4><div id="spmSubscription" class="spm-facts"></div></section>
                <section class="spm-card wide"><h4><i data-feather="check-circle"></i>{{ $profileArabic ? 'الحضور والغياب' : 'Attendance' }}</h4><div id="spmAttendance" class="spm-stats"></div></section>
                <section class="spm-card wide"><h4><i data-feather="clock"></i>{{ $profileArabic ? 'آخر سجلات الحضور' : 'Recent attendance' }}</h4><div class="table-responsive"><table class="spm-history"><tbody id="spmHistory"></tbody></table></div></section>
                <section class="spm-card wide"><h4><i data-feather="file-text"></i>{{ $profileArabic ? 'الملاحظات الطبية والعامة' : 'Medical & general notes' }}</h4><div id="spmNotes" class="spm-notes"></div></section>
            </div></div>
            <footer class="spm-footer"><button class="btn btn-light" data-bs-dismiss="modal">{{ $profileArabic ? 'إغلاق' : 'Close' }}</button><a id="spmEdit" class="btn btn-primary" href="#">{{ $profileArabic ? 'تعديل بيانات الطالب' : 'Edit student' }}</a></footer>
        </div>
    </div></div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const ar=@json($profileArabic), modalElement=document.getElementById('studentProfileModal'), modal=new bootstrap.Modal(modalElement);
 const loading=document.getElementById('studentProfileLoading'),error=document.getElementById('studentProfileError'),content=document.getElementById('studentProfileContent');
 const labels={phone:ar?'الهاتف':'Phone',email:ar?'البريد الإلكتروني':'Email',gender:ar?'النوع':'Gender',birth:ar?'تاريخ الميلاد':'Birth date',age:ar?'العمر':'Age',location:ar?'العنوان':'Location',guardian:ar?'اسم ولي الأمر':'Guardian',guardianPhone:ar?'هاتف ولي الأمر':'Guardian phone',groups:ar?'المجموعات':'Groups',due:ar?'إجمالي المستحق':'Total due',paid:ar?'إجمالي المدفوع':'Total paid',remaining:ar?'إجمالي المتبقي':'Total remaining',start:ar?'بداية الاشتراك':'Starts',end:ar?'نهاية الاشتراك':'Ends',duration:ar?'مدة الاشتراك':'Duration',daysLeft:ar?'الأيام المتبقية':'Days remaining',group:ar?'المجموعة':'Group',amount:ar?'قيمة الاشتراك':'Amount',method:ar?'آخر طريقة دفع':'Last payment method',present:ar?'حضور':'Present',late:ar?'تأخير':'Late',absent:ar?'غياب':'Absent',excused:ar?'غياب بعذر':'Excused',total:ar?'الإجمالي':'Total',day:ar?'يوم':'days',year:ar?'سنة':'years',none:ar?'غير مسجل':'Not recorded'};
 const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));
 const fact=(label,value)=>'<div class="spm-fact"><span>'+esc(label)+'</span><strong>'+esc(value||labels.none)+'</strong></div>';
 const money=v=>Number(v||0).toLocaleString(ar?'ar-EG':'en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
 document.querySelectorAll('[data-student-profile-url]').forEach(button=>button.addEventListener('click',async()=>{
   loading.classList.remove('d-none');error.classList.add('d-none');content.classList.add('d-none');modal.show();
   try{const response=await fetch(button.dataset.studentProfileUrl,{headers:{Accept:'application/json'}});if(!response.ok)throw new Error(response.status);const d=await response.json(),s=d.subscription,a=d.attendance;
     const image=document.getElementById('spmImage');image.onerror=()=>{image.onerror=null;image.src=d.fallback_image};image.src=d.image||d.fallback_image;
     document.getElementById('spmName').textContent=d.name;document.getElementById('spmContact').textContent=[d.phone,d.email].filter(Boolean).join(' · ');document.getElementById('spmStatus').textContent=d.status;document.getElementById('spmEdit').href=d.edit_url;
     document.getElementById('spmPersonal').innerHTML=fact(labels.phone,d.phone)+fact(labels.email,d.email)+fact(labels.gender,d.gender)+fact(labels.birth,d.birth_date)+fact(labels.age,d.age?d.age+' '+labels.year:null)+fact(labels.location,d.location);
     document.getElementById('spmGuardian').innerHTML=fact(labels.guardian,d.guardian_name)+fact(labels.guardianPhone,d.guardian_phone)+fact(labels.groups,(d.groups||[]).join('، '));
     document.getElementById('spmFinancial').innerHTML=fact(labels.due,money(d.financials.total_due))+fact(labels.paid,money(d.financials.total_paid))+fact(labels.remaining,money(d.financials.total_remaining));
     document.getElementById('spmSubscription').innerHTML=s?fact(labels.start,s.starts_on)+fact(labels.end,s.ends_on)+fact(labels.duration,s.duration_days!==null?s.duration_days+' '+labels.day:null)+fact(labels.daysLeft,s.remaining_days+' '+labels.day)+fact(labels.group,s.group)+fact(labels.amount,money(s.amount))+fact(labels.paid,money(s.paid))+fact(labels.remaining,money(s.remaining))+fact(labels.method,s.last_payment_method):'<div class="spm-empty">'+labels.none+'</div>';
     document.getElementById('spmAttendance').innerHTML=['present','late','absent','excused','total'].map(k=>'<div class="spm-stat"><b>'+Number(a[k]||0).toLocaleString()+'</b><small>'+esc(labels[k])+'</small></div>').join('');
     document.getElementById('spmHistory').innerHTML=(d.recent_attendance||[]).length?(d.recent_attendance||[]).map(r=>'<tr><td>'+esc(r.date)+'</td><td>'+esc(r.group||labels.none)+'</td><td>'+esc(labels[r.status]||r.status)+'</td><td>'+esc(r.check_in||'')+'</td></tr>').join(''):'<tr><td class="spm-empty">'+labels.none+'</td></tr>';
     document.getElementById('spmNotes').textContent=[d.medical_notes,d.notes].filter(Boolean).join('\n\n')||labels.none;
     loading.classList.add('d-none');content.classList.remove('d-none');if(window.feather)feather.replace();
   }catch(e){console.error('[Hagzz] Student profile failed',e);loading.classList.add('d-none');error.classList.remove('d-none');}
 }));
});
</script>
@endpush
@endonce

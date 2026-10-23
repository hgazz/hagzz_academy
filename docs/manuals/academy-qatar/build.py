from pathlib import Path
import runpy,re,json
R=Path(__file__).resolve().parent
pages=runpy.run_path(str(R/'content.py'))['pages']
pages[0]['content']='''<img class="cover-art" src="assets/editorial-cover.png"><div class="cover-shade"></div><div class="cover-copy"><div class="edition">QATAR / ACADEMIES / 2026</div><h1>أكاديميتك.<br>تجربة أكثر<br><em>تنظيمًا.</em></h1><p>دليل التهيئة والتشغيل<br>لوحة الشركاء وتطبيقات Hagzz</p><div class="cover-rule"></div><span>من أول دخول، إلى تجربة تستحق ثقة ولي الأمر.</span></div><div class="cover-bottom"><span>دليل مصوّر للأكاديميات في قطر</span><span>الإصدار 02 / أكتوبر 2026</span></div>'''
pages[1]['title']='تجربة منظمة، من البداية.'
pages[1]['sub']='إلى مالك الأكاديمية ومسؤول التشغيل'
pages[1]['content']='''<div class="statement">كل تفصيل في التشغيل<br>يصل أثره إلى <em>ولي الأمر.</em></div><p class="lead">موعد واضح. تسجيل دقيق. تحصيل مفهوم. ومعلومة تجدها عندما تحتاج إليها. صُمم هذا الدليل ليساعد فريقك على بناء هذه التجربة، خطوة بخطوة.</p><div class="value-grid"><article><span>01 / الإدارة</span><h3>رؤية أوضح للعمل</h3><p>رتّب المواقع والمدربين والبرامج والاشتراكات ضمن مسار تشغيلي واحد، وراجع النتائج من مصدرها.</p></article><article><span>02 / الفريق</span><h3>مسؤولية محددة لكل دور</h3><p>كل موظف بحسابه وصلاحياته. خطوات يمكن الرجوع إليها عند بدء العمل أو انضمام عضو جديد.</p></article><article><span>03 / الأسرة</span><h3>متابعة تبدأ بمعلومة صحيحة</h3><p>بيانات لاعب دقيقة، ومواعيد واضحة، وحساب مستقل لولي الأمر في Hagzz Partner بعد تفعيله.</p></article></div><div class="editor-note"><b>طريقة الاستخدام</b><p>اقرأ رحلة التشغيل مرة واحدة، ثم استخدم الصفحات المصوّرة مرجعًا للفريق. ابدأ بتجربة تشغيل على حساب مخصص قبل دعوة العملاء.</p></div>'''
# Refine the grant page, using only owner-provided terms.
pages[3]['title']='دعم للتحول الرقمي. متاح للجميع.'
pages[3]['sub']='مبادرة من Hagzz وواحة قطر للعلوم والتكنولوجيا'
pages[3]['content']='''<div class="grant-hero"><span>SPORTS DIGITAL TRANSFORMATION</span><h1>لأكاديميات قطر،<br><em>بمختلف أحجامها.</em></h1><p>خصومات وشهور مجانية لدعم التحول الرقمي في القطاع الرياضي، دون تمييز بحسب حجم الأكاديمية.</p></div><div class="two-columns"><article><h3>السعر واضح من البداية.</h3><p>الأسعار الأساسية منشورة في صفحة الباقات. يوضح عرض المنحة الخصم، وعدد الأشهر المجانية، ونطاق الخدمة المعتمد لأكاديميتك.</p><a href="https://hagzz.el7lm.com/pricing.html">استعرض الباقات ←</a></article><article><h3>ابدأ بمحادثة عن احتياجك.</h3><p>عرّف فريقنا بأكاديميتك وطريقة تشغيلها، ثم استلم تفاصيل العرض وخطوات التفعيل وروابط التطبيقات المناسبة.</p></article></div><div class="contact"><img src="assets/contact-qr.svg"><div><span>فريق التهيئة / قطر</span><a href="https://wa.me/97470542458" dir="ltr">+974 7054 2458</a><p>امسح الرمز للتواصل عبر واتساب.</p></div></div><p class="fineprint">تحدد مدة الإتاحة ونسبة الخصم والمزايا المشمولة في العرض المعتمد للأكاديمية. هذا الدليل لا يحدد نسبة أو عدد أشهر موحدًا.</p>'''
# Add three editorial chapter dividers to make the operational journey easy to follow.
chapters=[('أضف موقع التدريب أولًا','01','أسّس تجربة\nيمكن البناء عليها.','ابدأ بالموقع والمدرب والبرنامج. حين تكون الأسس واضحة، يصبح التسجيل والعمل اليومي أسهل في المراجعة.',['مواقع التدريب','المدربون والبرامج','الحصص والمواعيد']),('أنشئ ملف اللاعب وولي الأمر','02','من أول تسجيل،\nكل شيء في مكانه.','ملف لاعب واضح، ومجموعة صحيحة، واشتراك يمكن مراجعة قيمته ومدته وحركة سداده.',['اللاعب وولي الأمر','المجموعة والاشتراك','التحصيل والمستندات']),('أنشئ جلسة الحضور','03','شغّل يومك\nبصورة أوضح.','من كشف الحضور إلى التقارير وصلاحيات الفريق؛ أغلق كل خطوة بعد التحقق من نتيجتها.',['الحضور والبطاقات','المراجعة المالية','حسابات الفريق'])]
for before,num,title,desc,items in chapters:
 idx=next(i for i,p in enumerate(pages) if p['title']==before)
 pages.insert(idx,{'title':title.replace('\n',' '),'sub':desc,'tag':'chapter','content':f'<div class="chapter-number">{num}</div><div class="chapter-label">رحلة تشغيل الأكاديمية / {num}</div><h1 class="chapter-title">'+title.replace('\n','<br>')+'</h1><p class="chapter-desc">'+desc+'</p><div class="chapter-path">'+''.join(f'<div><span>{j+1:02}</span><b>{t}</b></div>' for j,t in enumerate(items))+'</div><div class="chapter-bottom">خطوات عملية. صور واضحة. ونقطة تحقق قبل الانتقال.</div>'})
# Sharper client-facing language; keep essential capability limits.
for p in pages:
 p['content']=p['content'].replace('قبل الانتقال</b>','نقطة تحقق</b>')
 p['content']=p['content'].replace('واجهة لوحة الأكاديميات • بيانات تدريبية توضيحية • قد تختلف الحقول بحسب الصلاحيات والإصدار','صورة من لوحة الأكاديميات / بيانات توضيحية. قد تختلف الحقول بحسب إصدارك وصلاحياتك.')
# The closing page is a single calm call to action.
pages[-1]['title']='لنبدأ بأكاديميتك.'
pages[-1]['sub']='Hagzz / Qatar'
pages[-1]['content']='''<div class="statement">خطوتك الأولى،<br><em>محادثة واضحة.</em></div><p class="lead">تواصل معنا لتحديد احتياج أكاديميتك، والتعرف على تفاصيل المنحة، وترتيب التفعيل والتجربة الأولى مع فريقك.</p><div class="closing-contact"><img src="assets/contact-qr.svg"><span>فريق Hagzz / قطر</span><a href="https://wa.me/97470542458" dir="ltr">+974 7054 2458</a><p>واتساب / الاستفسار والتفعيل</p></div><div class="closing-links"><a href="https://hagzz.el7lm.com/pricing.html">الباقات والأسعار ←</a><a href="https://partner.hagzz.el7lm.com/partner/login">دخول الشركاء ←</a><a href="https://play.google.com/store/apps/details?id=com.unlimitedhagzz.hagzz">تطبيق Hagzz ←</a></div><p class="fineprint">هذا الجزء مخصص للأكاديميات. أدلة الجيم والمراكز الصحية، ثم الملاعب والمرافق الرياضية، تصدر في أجزاء مستقلة.</p>'''
toc=[]
for i,p in enumerate(pages):
 if i>2:
  label=p['title']
  toc.append(f'<a class="tocrow" href="#p{i+1}"><span>{label}</span><b>{i+1:02}</b></a>')
pages[2]['content']='<div class="toc-grid">'+''.join(toc)+'</div><div class="editor-note"><b>اختر نقطة البداية المناسبة لك.</b><p>للمالك: التهيئة ثم التسجيل والتشغيل. لولي الأمر: مسار Hagzz Partner. لاختيار التدريبات: تطبيق Hagzz.</p></div>'
css=(R/'premium.css').read_text()
out=['<!doctype html><html lang="ar" dir="rtl"><meta charset="utf-8"><title>Hagzz | Academy Experience Guide — Qatar</title><style>'+css+'</style><body>']
for i,p in enumerate(pages):
 cl='cover' if i==0 else 'chapter' if p['tag']=='chapter' else 'illustrated' if '<figure>' in p['content'] else 'editorial'
 if i==len(pages)-1:cl+=' final-page'
 out.append(f'<section id="p{i+1}" class="{cl}"><header><img src="assets/hagzz.svg"><span>دليل الأكاديميات / قطر</span><small>ACADEMY EXPERIENCE GUIDE</small></header><div class="title"><span class="tag">{p["tag"] if p["tag"]!="chapter" else ""}</span><h2>{p["title"]}</h2><p>{p["sub"]}</p></div><main>{p["content"]}</main><footer><span>HAGZZ / QATAR</span><span dir="ltr">{i+1:02} — {len(pages):02}</span></footer></section>')
out.append('</body></html>');(R/'Hagzz-Academy-Qatar-Manual.html').write_text(''.join(out));(R/'pages.json').write_text(json.dumps([{k:v for k,v in p.items() if k!='content'} for p in pages],ensure_ascii=False,indent=2));print(len(pages))

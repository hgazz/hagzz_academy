@once
@php
    $waDirectArabic = app()->getLocale() === 'ar';
    $waDirectAcademy = auth('academy')->user()?->commercial_name ?: auth('academy')->user()?->name ?: 'Hagzz';
    $waDirectTemplates = $waDirectArabic ? [
        'مرحبًا {name}، نذكّرك بموعد التدريب القادم لدى {academy}. يسعدنا تأكيد حضورك.',
        'مرحبًا {name}، تم تأكيد حجزك لدى {academy}. إذا احتجت إلى أي مساعدة يمكنك الرد على هذه الرسالة.',
        'مرحبًا {name}، نود تذكيرك بوجود دفعة مستحقة لدى {academy}. يرجى التواصل معنا لمعرفة التفاصيل.',
        'مرحبًا {name}، معك فريق {academy}. نتواصل معك للاطمئنان وخدمتك، ويمكنك الرد علينا في أي وقت.',
    ] : [
        'Hello {name}, this is a reminder about your upcoming training at {academy}. Please confirm your attendance.',
        'Hello {name}, your booking at {academy} is confirmed. Reply to this message if you need any help.',
        'Hello {name}, this is a friendly reminder that a payment is due at {academy}. Contact us for the details.',
        'Hello {name}, this is the {academy} team. We are here to help, and you can reply at any time.',
    ];
@endphp

@push('css')
<style>
    .wa-direct-overlay { position: fixed; inset: 0; z-index: 1065; display: none; place-items: center; padding: 18px; background: rgba(15, 23, 42, .58); backdrop-filter: blur(4px); }
    .wa-direct-overlay.is-open { display: grid; }
    .wa-direct-dialog { width: min(620px, 100%); max-height: calc(100vh - 36px); overflow: auto; background: #fff; border-radius: 18px; box-shadow: 0 24px 70px rgba(15, 23, 42, .28); }
    .wa-direct-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 20px 22px 14px; border-bottom: 1px solid #edf0f4; }
    .wa-direct-head h3 { margin: 0; color: #172033; font-size: 19px; font-weight: 800; }
    .wa-direct-close { width: 34px; height: 34px; border: 0; border-radius: 50%; background: #f1f4f7; color: #536074; font-size: 18px; }
    .wa-direct-body { padding: 18px 22px 22px; }
    .wa-direct-contact { display: flex; align-items: center; gap: 10px; margin-bottom: 15px; padding: 11px 13px; border-radius: 12px; background: #edfdf4; color: #116236; font-weight: 700; }
    .wa-direct-contact i { font-size: 22px; color: #20a85a; }
    .wa-direct-label { display: block; margin-bottom: 9px; color: #344054; font-size: 13px; font-weight: 800; }
    .wa-direct-suggestions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; margin-bottom: 14px; }
    .wa-direct-template { min-height: 64px; padding: 10px 12px; border: 1px solid #dfe5ec; border-radius: 11px; background: #fff; color: #344054; text-align: start; font-size: 12px; font-weight: 700; line-height: 1.55; transition: .15s ease; }
    .wa-direct-template:hover, .wa-direct-template.is-selected { border-color: #20a85a; background: #effcf4; color: #13713d; }
    .wa-direct-message { width: 100%; min-height: 125px; resize: vertical; padding: 12px 14px; border: 1px solid #d9e0e8; border-radius: 12px; background: #fff; color: #172033; line-height: 1.7; }
    .wa-direct-message:focus { outline: 0; border-color: #20a85a; box-shadow: 0 0 0 3px rgba(32, 168, 90, .12); }
    .wa-direct-actions { display: flex; justify-content: flex-end; gap: 9px; margin-top: 16px; }
    .wa-direct-open { border: 0; border-radius: 10px; padding: 10px 18px; background: #20a85a; color: #fff; font-weight: 800; }
    .wa-direct-cancel { border: 1px solid #d9e0e8; border-radius: 10px; padding: 10px 16px; background: #fff; color: #475467; font-weight: 700; }
    body.dark .wa-direct-dialog, .dark .wa-direct-dialog { background: #172033; }
    body.dark .wa-direct-head, .dark .wa-direct-head { border-color: #2b374c; }
    body.dark .wa-direct-head h3, body.dark .wa-direct-label, .dark .wa-direct-head h3, .dark .wa-direct-label { color: #edf2f7; }
    body.dark .wa-direct-template, body.dark .wa-direct-message, .dark .wa-direct-template, .dark .wa-direct-message { background: #111827; border-color: #334155; color: #e5e7eb; }
    @media (max-width: 575.98px) { .wa-direct-suggestions { grid-template-columns: 1fr; } .wa-direct-head, .wa-direct-body { padding-inline: 16px; } .wa-direct-actions { flex-direction: column-reverse; } .wa-direct-actions button { width: 100%; } }
</style>
@endpush

<div class="wa-direct-overlay" id="wa-direct-overlay" role="dialog" aria-modal="true" aria-labelledby="wa-direct-title" hidden>
    <div class="wa-direct-dialog">
        <div class="wa-direct-head">
            <h3 id="wa-direct-title"><i class="fa-brands fa-whatsapp text-success"></i> {{ $waDirectArabic ? 'إرسال عبر واتساب' : 'Send via WhatsApp' }}</h3>
            <button type="button" class="wa-direct-close" data-wa-close aria-label="{{ $waDirectArabic ? 'إغلاق' : 'Close' }}">×</button>
        </div>
        <div class="wa-direct-body">
            <div class="wa-direct-contact"><i class="fa-brands fa-whatsapp"></i><span id="wa-direct-contact"></span></div>
            <span class="wa-direct-label">{{ $waDirectArabic ? 'اختر رسالة مقترحة أو اكتب رسالتك' : 'Choose a suggestion or write your message' }}</span>
            <div class="wa-direct-suggestions">
                @foreach($waDirectTemplates as $index => $template)
                    <button type="button" class="wa-direct-template" data-wa-template="{{ $template }}">{{ [$waDirectArabic ? 'تذكير بالتدريب' : 'Training reminder', $waDirectArabic ? 'تأكيد الحجز' : 'Booking confirmation', $waDirectArabic ? 'تذكير بالدفع' : 'Payment reminder', $waDirectArabic ? 'رسالة عامة' : 'General message'][$index] }}</button>
                @endforeach
            </div>
            <textarea class="wa-direct-message" id="wa-direct-message" placeholder="{{ $waDirectArabic ? 'اكتب رسالتك هنا...' : 'Write your message here...' }}"></textarea>
            <div class="wa-direct-actions">
                <button type="button" class="wa-direct-cancel" data-wa-close>{{ $waDirectArabic ? 'إلغاء' : 'Cancel' }}</button>
                <button type="button" class="wa-direct-open" id="wa-direct-open"><i class="fa-brands fa-whatsapp"></i> {{ $waDirectArabic ? 'فتح واتساب' : 'Open WhatsApp' }}</button>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('wa-direct-overlay');
    if (!overlay) return;
    const message = document.getElementById('wa-direct-message');
    const contact = document.getElementById('wa-direct-contact');
    const academy = @json((string) $waDirectAcademy);
    let currentPhone = '';
    let currentName = '';
    const normalizePhone = function (value) {
        let digits = String(value || '').replace(/\D+/g, '');
        if (digits.startsWith('00')) digits = digits.slice(2);
        if (digits.startsWith('0')) digits = '20' + digits.slice(1);
        return digits;
    };
    const personalize = function (value) {
        return String(value || '').replaceAll('{name}', currentName).replaceAll('{academy}', academy);
    };
    const closeModal = function () {
        overlay.classList.remove('is-open');
        overlay.hidden = true;
        document.body.style.overflow = '';
    };
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('.js-whatsapp-direct');
        if (trigger) {
            event.preventDefault();
            currentPhone = normalizePhone(trigger.dataset.phone);
            currentName = trigger.dataset.name || '';
            contact.textContent = currentName + (trigger.dataset.phone ? ' · ' + trigger.dataset.phone : '');
            const firstTemplate = overlay.querySelector('[data-wa-template]');
            overlay.querySelectorAll('[data-wa-template]').forEach(function (button) { button.classList.remove('is-selected'); });
            if (firstTemplate) { firstTemplate.classList.add('is-selected'); message.value = personalize(firstTemplate.dataset.waTemplate); }
            overlay.hidden = false;
            overlay.classList.add('is-open');
            document.body.style.overflow = 'hidden';
            message.focus();
            return;
        }
        const template = event.target.closest('[data-wa-template]');
        if (template) {
            overlay.querySelectorAll('[data-wa-template]').forEach(function (button) { button.classList.remove('is-selected'); });
            template.classList.add('is-selected');
            message.value = personalize(template.dataset.waTemplate);
            message.focus();
            return;
        }
        if (event.target.closest('[data-wa-close]') || event.target === overlay) closeModal();
    });
    document.getElementById('wa-direct-open').addEventListener('click', function () {
        if (!currentPhone) return;
        const url = 'https://wa.me/' + currentPhone + '?text=' + encodeURIComponent(message.value.trim());
        window.open(url, '_blank', 'noopener,noreferrer');
        closeModal();
    });
    document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && !overlay.hidden) closeModal(); });
});
</script>
@endpush
@endonce

# 🔑 دليل المرجعية والاتصال بالسيرفر والتطبيقات (Hagzz Reference & Server Credentials)

تم إعداد هذا الملف ليكون مرجعاً شاملاً لجميع بيانات الاتصال، المفاتيح، كلمات المرور، ومسارات السيرفر وتطبيق الموبايل.

---

## 🌐 1. بيانات الاتصال بالسيرفر (Netcup Server SSH)

| البيان | القيمة |
| :--- | :--- |
| **IP السيرفر** | `159.195.203.217` |
| **Domain السيرفر** | `v2202607375984478685.quicksrv.de` / `partner.hagzz.el7lm.com` |
| **مستخدم SSH الأساسي** | `hagzz` (أو `root`) |
| **منفذ SSH** | `22` |
| **مسار مفتاح SSH المحلّي** | `C:\Users\MeskL\.ssh\hagzz_deploy` |

### 💻 أمر الاتصال المباشر عبر PowerShell:
```powershell
ssh -i "C:\Users\MeskL\.ssh\hagzz_deploy" hagzz@159.195.203.217
```

---

## 📂 2. مسارات وتخليق المجلدات على السيرفر (Server Architecture & Paths)

- **المجلد الرئيسي للتطبيقات**: `/home/hagzz/hagzz/`
- **مجلد مشروع لوحة الشركاء (Academy)**: `/home/hagzz/hagzz/apps/hagzz_academy`
- **مجلد البنية التحتية و Docker**: `/home/hagzz/hagzz/infra`
- **ملف Compose الرئيسي**: `/home/hagzz/hagzz/infra/compose.yaml`
- **اسم حاوية الدوكر لـ Academy**: `hagzz-academy-1`
- **اسم حاوية قاعدة البيانات**: `hagzz-mysql-1` (Port 3306)
- **اسم حاوية Caddy Web Server**: `hagzz-caddy-1` (Ports 80 / 443)

---

## 📱 3. بيانات توقيع وتطبيق الموبايل (Android Release Signing)

| البيان | القيمة |
| :--- | :--- |
| **اسم التطبيق** | `Hagzz Partners Mobile` |
| **رقم الإصدار الحالي** | `1.1.0+2` |
| **ملف التوقيع (Keystore)** | `hagzz_partners_mobile/android/upload-keystore.jks` |
| **Key Alias** | `upload` |
| **Key Password** | `Hagzz2026SecureKey` |
| **Store Password** | `Hagzz2026SecureKey` |
| **مسار ملف APK الناتج** | `hagzz_partners_mobile\build\app\outputs\flutter-apk\app-release.apk` |
| **مسار ملف AAB لـ Google Play** | `hagzz_partners_mobile\build\app\outputs\bundle\release\app-release.aab` |

---

## 🐙 4. مستودع GitHub (Repository Info)

- **رابط المستودع**: `https://github.com/hgazz/hagzz_academy.git`
- **الفرع الرئيسي**: `master`

---

## 🚀 5. أمر التحديث والنشر التلقائي الكامل بنقرة واحدة (One-Line Automated Deployment)

عند إجراء أي تعديلات جديدة ومطالبتك بتطبيقها على السيرفر، يمكنك تشغيل هذا الأمر التلقائي من جهازك المحلي:

```powershell
ssh -i "C:\Users\MeskL\.ssh\hagzz_deploy" hagzz@159.195.203.217 "cd /home/hagzz/hagzz/apps/hagzz_academy && git fetch origin && git reset --hard origin/master && cd /home/hagzz/hagzz/infra && docker compose build academy && docker compose up -d academy && docker exec hagzz-academy-1 php artisan migrate --force && docker exec hagzz-academy-1 php artisan route:clear && docker exec hagzz-academy-1 php artisan route:cache && docker exec hagzz-academy-1 php artisan config:cache"
```

---

## 🛠️ 6. الأوامر المفيدة داخل حاوية الدوكر على السيرفر (Useful Container Commands)

```bash
# ترحيل قاعدة البيانات
docker exec hagzz-academy-1 php artisan migrate --force

# تنظيف وتخزين المسارات
docker exec hagzz-academy-1 php artisan route:clear
docker exec hagzz-academy-1 php artisan route:cache

# تنظيف وتخزين الإعدادات
docker exec hagzz-academy-1 php artisan config:clear
docker exec hagzz-academy-1 php artisan config:cache

# إعادة تشغيل حاوية لوحة الأكاديمية
docker restart hagzz-academy-1
```

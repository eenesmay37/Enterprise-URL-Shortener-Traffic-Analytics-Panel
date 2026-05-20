# 🚀 Enterprise URL Shortener & Traffic Analytics Panel

Bu proje; uzun ve karmaşık URL'leri güvenli, kısa ve isteğe bağlı olarak özelleştirilmiş (custom alias) linklere dönüştüren, aynı zamanda linke tıklayan kullanıcıların cihaz türlerini (Mobil / Masaüstü) anlık olarak ayrıştırıp analiz eden kurumsal düzeyde bir Backend uygulamasıdır.

## 🌟 Öne Çıkan Gelişmiş Özellikler

* **🎯 Özel İsimli Link Desteği (Custom Alias):** Sistem tarafından üretilen rastgele kodların yanı sıra, kullanıcının pazarlama veya marka odaklı kendi belirlediği kelimelerle (`/link/?git=ozel-kampanya`) kısa URL oluşturabilmesi.
* **📊 Gelişmiş Cihaz Analitiği (Traffic Tracking):** Linke tıklayan kullanıcıların cihaz verilerini (User-Agent) Regex tabanlı özel bir algoritmayla inceleyerek Mobil ve PC trafiğini yüzde bazlı ve sayısal olarak canlı raporlama.
* **🔏 Güvenli Kod ve Filtreleme:** Kullanıcı girişlerini siber güvenlik açıklarına karşı koruyan `htmlspecialchars()` ve URL geçerlilik kontrollerini sağlayan `FILTER_VALIDATE_URL` mekanizmaları.
* **🔌 Otomatik QR Kod Entegrasyonu:** Oluşturulan her kısa veya özel link için üçüncü parti API entegrasyonu kullanılarak anlık, dinamik ve indirilebilir QR kod üretimi.
* **📋 Pano Kopyalama Desteği (Clipboard API):** Kullanıcı deneyimini (UX) artırmak adına, oluşturulan kısa linklerin tek tıkla panoya kopyalanmasını sağlayan JavaScript entegrasyonu.

## 🛠️ Kullanılan Teknolojiler ve Mimari

* **Backend:** PHP (PDO Veritabanı Yönetimi, Regex Veri Analizi, HTTP Header Yönlendirmeleri)
* **Veritabanı:** MySQL (İlişkisel Veri Mimarisi, UNIQUE Anahtar Kısıtlamaları)
* **Frontend:** HTML5, CSS3, Bootstrap 5 (Modern & Karanlık Tema Odaklı Dashboard)
* **Harici API:** QR Server REST API

## ⚙️ Kurulum ve Çalıştırma

1. Yerel sunucunuzda (XAMPP/WAMP) `url_db` adında bir veritabanı oluşturun ve proje klasöründeki SQL sorgusunu çalıştırarak `linkler` tablosunu kurun.
2. `baglanti.php` içerisindeki veritabanı bağlantı ayarlarını doğrulayın.
3. Projeyi tarayıcınızda çalıştırarak uzun linklerinizi analitik güvencesiyle kısaltmaya başlayın.

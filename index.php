<?php
require_once 'baglanti.php';

function kodUret($uzunluk = 6) {
    $karakterler = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $kod = '';
    for ($i = 0; $i < $uzunluk; $i++) {
        $kod .= $karakterler[rand(0, strlen($karakterler) - 1)];
    }
    return $kod;
}

// Cihaz türünü tespit eden ufak algoritma
function cihazBul() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $user_agent)) {
        return 'mobil';
    }
    return 'pc';
}

// --- YÖNLENDİRME & ANALİTİK (REDIRECT) ---
if (isset($_GET['git'])) {
    $kod = $_GET['git'];
    $sorgu = $db->prepare("SELECT uzun_url FROM linkler WHERE kisa_kod = ?");
    $sorgu->execute([$kod]);
    $link = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($link) {
        $cihaz = cihazBul();
        if ($cihaz == 'mobil') {
            $db->prepare("UPDATE linkler SET tiklanma = tiklanma + 1, mobil_tiklanma = mobil_tiklanma + 1 WHERE kisa_kod = ?")->execute([$kod]);
        } else {
            $db->prepare("UPDATE linkler SET tiklanma = tiklanma + 1, pc_tiklanma = pc_tiklanma + 1 WHERE kisa_kod = ?")->execute([$kod]);
        }
        
        header("Location: " . $link['uzun_url']);
        exit;
    }
}

// --- LİNK KISALTMA İŞLEMİ ---
$mesaj = ""; $hata = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kisalt'])) {
    $url = $_POST['url'];
    $ozel_isim = trim($_POST['ozel_isim']);
    
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Kullanıcı özel isim girdiyse onu kullan, girmediyse rastgele üret
        $yeni_kod = !empty($ozel_isim) ? preg_replace('/[^a-zA-Z0-9-]/', '', $ozel_isim) : kodUret();
        
        // Bu kod daha önce alınmış mı kontrol et
        $kontrol = $db->prepare("SELECT id FROM linkler WHERE kisa_kod = ?");
        $kontrol->execute([$yeni_kod]);
        
        if ($kontrol->rowCount() > 0) {
            $hata = "Bu özel link veya kod zaten kullanılıyor, lütfen başka bir tane deneyin.";
        } else {
            $sorgu = $db->prepare("INSERT INTO linkler (uzun_url, kisa_kod) VALUES (?, ?)");
            if ($sorgu->execute([$url, $yeni_kod])) {
                $mesaj = "Harika! Link başarıyla kısaltıldı.";
            }
        }
    } else {
        $hata = "Lütfen geçerli bir URL girin (http:// veya https:// dahil)!";
    }
}

$linkler = $db->query("SELECT * FROM linkler ORDER BY tarih DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise URL Shortener | Analitik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', sans-serif; }
        .hero-section { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 70px 0; border-bottom: 5px solid #3b82f6; }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .btn-primary { background: #3b82f6; border: none; padding: 12px 30px; font-weight: bold; }
        .btn-primary:hover { background: #2563eb; }
        .stat-badge { font-size: 0.8rem; padding: 5px 8px; border-radius: 8px; margin-right: 5px; }
        .qr-code { width: 45px; height: 45px; cursor: pointer; transition: transform 0.2s; border: 1px solid #e2e8f0; border-radius: 8px; padding: 2px; }
        .qr-code:hover { transform: scale(1.1); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="hero-section text-center mb-5">
    <div class="container">
        <h1 class="fw-bold">🚀 Enterprise URL Shortener</h1>
        <p class="lead text-white-50">Özel linkler oluşturun, mobil/masaüstü trafik analizini anlık takip edin.</p>
    </div>
</div>

<div class="container" style="margin-top: -60px;">
    <div class="card p-4 mb-5 shadow-lg">
        <form method="POST" class="row g-3 align-items-center">
            <div class="col-md-6">
                <label class="form-label text-muted small fw-bold">Hedef URL (Zorunlu)</label>
                <input type="url" name="url" class="form-control form-control-lg border-0 bg-light" placeholder="https://..." required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Özel Link (Opsiyonel)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-0">/</span>
                    <input type="text" name="ozel_isim" class="form-control form-control-lg border-0 bg-light" placeholder="kampanyam-2026">
                </div>
            </div>
            <div class="col-md-2 mt-auto">
                <button type="submit" name="kisalt" class="btn btn-primary btn-lg w-100 h-100">Oluştur</button>
            </div>
        </form>
        <?php if($mesaj) echo "<div class='alert alert-success mt-4 mb-0 fw-bold'>✅ $mesaj</div>"; ?>
        <?php if($hata) echo "<div class='alert alert-danger mt-4 mb-0 fw-bold'>⚠️ $hata</div>"; ?>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="fw-bold text-dark">📊 Trafik ve Analitik Paneli</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-4">Kısa Link / Orijinal Link</th>
                            <th class="text-center">Toplam Tık</th>
                            <th>Cihaz Analizi</th>
                            <th class="text-end pe-4">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($linkler)): ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">Sistemde henüz veri bulunmuyor.</td></tr>
                        <?php else: ?>
                            <?php foreach($linkler as $l): 
                                $kisa_url = "http://" . $_SERVER['HTTP_HOST'] . "/link/?git=" . $l['kisa_kod'];
                                // Yüzde hesaplama
                                $toplam = $l['tiklanma'];
                                $mobil_yuzde = $toplam > 0 ? round(($l['mobil_tiklanma'] / $toplam) * 100) : 0;
                                $pc_yuzde = $toplam > 0 ? round(($l['pc_tiklanma'] / $toplam) * 100) : 0;
                            ?>
                            <tr>
                                <td class="ps-4 py-3">
                                    <a href="<?php echo $kisa_url; ?>" target="_blank" class="fw-bold text-decoration-none fs-5 mb-1 d-block">
                                        localhost/link/<?php echo $l['kisa_kod']; ?>
                                    </a>
                                    <small class="text-muted d-inline-block text-truncate" style="max-width: 300px;">
                                        <?php echo htmlspecialchars($l['uzun_url']); ?>
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="fs-4 fw-bold text-dark"><?php echo $toplam; ?></span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="stat-badge bg-primary bg-opacity-10 text-primary">📱 Mobil: %<?php echo $mobil_yuzde; ?> (<?php echo $l['mobil_tiklanma']; ?>)</span>
                                        <span class="stat-badge bg-secondary bg-opacity-10 text-secondary">💻 PC: %<?php echo $pc_yuzde; ?> (<?php echo $l['pc_tiklanma']; ?>)</span>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border me-1" onclick="navigator.clipboard.writeText('<?php echo $kisa_url; ?>'); alert('Kopyalandı!');" title="Kopyala">📋</button>
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?php echo urlencode($kisa_url); ?>" class="qr-code" title="QR Kodu İndir/Büyüt" onclick="window.open(this.src)">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
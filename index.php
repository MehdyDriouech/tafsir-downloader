<?php
/**
 * TAFSIR DOWNLOADER v4.0 - My Pocket Imam
 * Avec pdf.js (Mozilla) pour extraction côté client
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

define('BASE_URL_API', 'https://cdn.jsdelivr.net/gh/spa5k/tafsir_api@main/tafsir');
define('BASE_URL_PDF_FR', 'https://tafsir.be');
define('OUTPUT_DIR', __DIR__ . '/output');
define('PDF_DIR', __DIR__ . '/pdf_cache');
define('TIMEOUT', 60);

@ini_set('max_execution_time', 600);
@ini_set('memory_limit', '256M');
@set_time_limit(600);

// Métadonnées des sourates
$SURAHS = [
    ['num' => 1, 'name' => 'Al-Fatiha', 'name_ar' => 'الفاتحة', 'name_fr' => 'Le Prologue', 'verses' => 7],
    ['num' => 2, 'name' => 'Al-Baqarah', 'name_ar' => 'البقرة', 'name_fr' => 'La Vache', 'verses' => 286],
    ['num' => 3, 'name' => "Ali 'Imran", 'name_ar' => 'آل عمران', 'name_fr' => 'La Famille d\'Imran', 'verses' => 200],
    ['num' => 4, 'name' => 'An-Nisa', 'name_ar' => 'النساء', 'name_fr' => 'Les Femmes', 'verses' => 176],
    ['num' => 5, 'name' => "Al-Ma'idah", 'name_ar' => 'المائدة', 'name_fr' => 'La Table Servie', 'verses' => 120],
    ['num' => 6, 'name' => "Al-An'am", 'name_ar' => 'الأنعام', 'name_fr' => 'Les Bestiaux', 'verses' => 165],
    ['num' => 7, 'name' => "Al-A'raf", 'name_ar' => 'الأعراف', 'name_fr' => 'Les Murailles', 'verses' => 206],
    ['num' => 8, 'name' => 'Al-Anfal', 'name_ar' => 'الأنفال', 'name_fr' => 'Le Butin', 'verses' => 75],
    ['num' => 9, 'name' => 'At-Tawbah', 'name_ar' => 'التوبة', 'name_fr' => 'Le Repentir', 'verses' => 129],
    ['num' => 10, 'name' => 'Yunus', 'name_ar' => 'يونس', 'name_fr' => 'Jonas', 'verses' => 109],
    ['num' => 11, 'name' => 'Hud', 'name_ar' => 'هود', 'name_fr' => 'Houd', 'verses' => 123],
    ['num' => 12, 'name' => 'Yusuf', 'name_ar' => 'يوسف', 'name_fr' => 'Joseph', 'verses' => 111],
    ['num' => 13, 'name' => "Ar-Ra'd", 'name_ar' => 'الرعد', 'name_fr' => 'Le Tonnerre', 'verses' => 43],
    ['num' => 14, 'name' => 'Ibrahim', 'name_ar' => 'إبراهيم', 'name_fr' => 'Abraham', 'verses' => 52],
    ['num' => 15, 'name' => 'Al-Hijr', 'name_ar' => 'الحجر', 'name_fr' => 'Al-Hijr', 'verses' => 99],
    ['num' => 16, 'name' => 'An-Nahl', 'name_ar' => 'النحل', 'name_fr' => 'Les Abeilles', 'verses' => 128],
    ['num' => 17, 'name' => 'Al-Isra', 'name_ar' => 'الإسراء', 'name_fr' => 'Le Voyage Nocturne', 'verses' => 111],
    ['num' => 18, 'name' => 'Al-Kahf', 'name_ar' => 'الكهف', 'name_fr' => 'La Caverne', 'verses' => 110],
    ['num' => 19, 'name' => 'Maryam', 'name_ar' => 'مريم', 'name_fr' => 'Marie', 'verses' => 98],
    ['num' => 20, 'name' => 'Ta-Ha', 'name_ar' => 'طه', 'name_fr' => 'Ta-Ha', 'verses' => 135],
    ['num' => 21, 'name' => 'Al-Anbiya', 'name_ar' => 'الأنبياء', 'name_fr' => 'Les Prophètes', 'verses' => 112],
    ['num' => 22, 'name' => 'Al-Hajj', 'name_ar' => 'الحج', 'name_fr' => 'Le Pèlerinage', 'verses' => 78],
    ['num' => 23, 'name' => "Al-Mu'minun", 'name_ar' => 'المؤمنون', 'name_fr' => 'Les Croyants', 'verses' => 118],
    ['num' => 24, 'name' => 'An-Nur', 'name_ar' => 'النور', 'name_fr' => 'La Lumière', 'verses' => 64],
    ['num' => 25, 'name' => 'Al-Furqan', 'name_ar' => 'الفرقان', 'name_fr' => 'Le Discernement', 'verses' => 77],
    ['num' => 26, 'name' => "Ash-Shu'ara", 'name_ar' => 'الشعراء', 'name_fr' => 'Les Poètes', 'verses' => 227],
    ['num' => 27, 'name' => 'An-Naml', 'name_ar' => 'النمل', 'name_fr' => 'Les Fourmis', 'verses' => 93],
    ['num' => 28, 'name' => 'Al-Qasas', 'name_ar' => 'القصص', 'name_fr' => 'Le Récit', 'verses' => 88],
    ['num' => 29, 'name' => 'Al-Ankabut', 'name_ar' => 'العنكبوت', 'name_fr' => 'L\'Araignée', 'verses' => 69],
    ['num' => 30, 'name' => 'Ar-Rum', 'name_ar' => 'الروم', 'name_fr' => 'Les Romains', 'verses' => 60],
    ['num' => 31, 'name' => 'Luqman', 'name_ar' => 'لقمان', 'name_fr' => 'Louqman', 'verses' => 34],
    ['num' => 32, 'name' => 'As-Sajdah', 'name_ar' => 'السجدة', 'name_fr' => 'La Prosternation', 'verses' => 30],
    ['num' => 33, 'name' => 'Al-Ahzab', 'name_ar' => 'الأحزاب', 'name_fr' => 'Les Coalisés', 'verses' => 73],
    ['num' => 34, 'name' => 'Saba', 'name_ar' => 'سبأ', 'name_fr' => 'Saba', 'verses' => 54],
    ['num' => 35, 'name' => 'Fatir', 'name_ar' => 'فاطر', 'name_fr' => 'Le Créateur', 'verses' => 45],
    ['num' => 36, 'name' => 'Ya-Sin', 'name_ar' => 'يس', 'name_fr' => 'Ya-Sin', 'verses' => 83],
    ['num' => 37, 'name' => 'As-Saffat', 'name_ar' => 'الصافات', 'name_fr' => 'Les Rangées', 'verses' => 182],
    ['num' => 38, 'name' => 'Sad', 'name_ar' => 'ص', 'name_fr' => 'Sad', 'verses' => 88],
    ['num' => 39, 'name' => 'Az-Zumar', 'name_ar' => 'الزمر', 'name_fr' => 'Les Groupes', 'verses' => 75],
    ['num' => 40, 'name' => 'Ghafir', 'name_ar' => 'غافر', 'name_fr' => 'Le Pardonneur', 'verses' => 85],
    ['num' => 41, 'name' => 'Fussilat', 'name_ar' => 'فصلت', 'name_fr' => 'Les Versets Détaillés', 'verses' => 54],
    ['num' => 42, 'name' => 'Ash-Shura', 'name_ar' => 'الشورى', 'name_fr' => 'La Consultation', 'verses' => 53],
    ['num' => 43, 'name' => 'Az-Zukhruf', 'name_ar' => 'الزخرف', 'name_fr' => 'L\'Ornement', 'verses' => 89],
    ['num' => 44, 'name' => 'Ad-Dukhan', 'name_ar' => 'الدخان', 'name_fr' => 'La Fumée', 'verses' => 59],
    ['num' => 45, 'name' => 'Al-Jathiyah', 'name_ar' => 'الجاثية', 'name_fr' => 'L\'Agenouillée', 'verses' => 37],
    ['num' => 46, 'name' => 'Al-Ahqaf', 'name_ar' => 'الأحقاف', 'name_fr' => 'Al-Ahqaf', 'verses' => 35],
    ['num' => 47, 'name' => 'Muhammad', 'name_ar' => 'محمد', 'name_fr' => 'Muhammad', 'verses' => 38],
    ['num' => 48, 'name' => 'Al-Fath', 'name_ar' => 'الفتح', 'name_fr' => 'La Victoire', 'verses' => 29],
    ['num' => 49, 'name' => 'Al-Hujurat', 'name_ar' => 'الحجرات', 'name_fr' => 'Les Appartements', 'verses' => 18],
    ['num' => 50, 'name' => 'Qaf', 'name_ar' => 'ق', 'name_fr' => 'Qaf', 'verses' => 45],
    ['num' => 51, 'name' => 'Adh-Dhariyat', 'name_ar' => 'الذاريات', 'name_fr' => 'Qui Éparpillent', 'verses' => 60],
    ['num' => 52, 'name' => 'At-Tur', 'name_ar' => 'الطور', 'name_fr' => 'Le Mont', 'verses' => 49],
    ['num' => 53, 'name' => 'An-Najm', 'name_ar' => 'النجم', 'name_fr' => 'L\'Étoile', 'verses' => 62],
    ['num' => 54, 'name' => 'Al-Qamar', 'name_ar' => 'القمر', 'name_fr' => 'La Lune', 'verses' => 55],
    ['num' => 55, 'name' => 'Ar-Rahman', 'name_ar' => 'الرحمن', 'name_fr' => 'Le Miséricordieux', 'verses' => 78],
    ['num' => 56, 'name' => "Al-Waqi'ah", 'name_ar' => 'الواقعة', 'name_fr' => 'L\'Événement', 'verses' => 96],
    ['num' => 57, 'name' => 'Al-Hadid', 'name_ar' => 'الحديد', 'name_fr' => 'Le Fer', 'verses' => 29],
    ['num' => 58, 'name' => 'Al-Mujadila', 'name_ar' => 'المجادلة', 'name_fr' => 'La Discussion', 'verses' => 22],
    ['num' => 59, 'name' => 'Al-Hashr', 'name_ar' => 'الحشر', 'name_fr' => 'L\'Exode', 'verses' => 24],
    ['num' => 60, 'name' => 'Al-Mumtahanah', 'name_ar' => 'الممتحنة', 'name_fr' => 'L\'Éprouvée', 'verses' => 13],
    ['num' => 61, 'name' => 'As-Saf', 'name_ar' => 'الصف', 'name_fr' => 'Le Rang', 'verses' => 14],
    ['num' => 62, 'name' => "Al-Jumu'ah", 'name_ar' => 'الجمعة', 'name_fr' => 'Le Vendredi', 'verses' => 11],
    ['num' => 63, 'name' => 'Al-Munafiqun', 'name_ar' => 'المنافقون', 'name_fr' => 'Les Hypocrites', 'verses' => 11],
    ['num' => 64, 'name' => 'At-Taghabun', 'name_ar' => 'التغابن', 'name_fr' => 'La Grande Perte', 'verses' => 18],
    ['num' => 65, 'name' => 'At-Talaq', 'name_ar' => 'الطلاق', 'name_fr' => 'Le Divorce', 'verses' => 12],
    ['num' => 66, 'name' => 'At-Tahrim', 'name_ar' => 'التحريم', 'name_fr' => 'L\'Interdiction', 'verses' => 12],
    ['num' => 67, 'name' => 'Al-Mulk', 'name_ar' => 'الملك', 'name_fr' => 'La Royauté', 'verses' => 30],
    ['num' => 68, 'name' => 'Al-Qalam', 'name_ar' => 'القلم', 'name_fr' => 'La Plume', 'verses' => 52],
    ['num' => 69, 'name' => 'Al-Haqqah', 'name_ar' => 'الحاقة', 'name_fr' => 'L\'Inévitable', 'verses' => 52],
    ['num' => 70, 'name' => "Al-Ma'arij", 'name_ar' => 'المعارج', 'name_fr' => 'Les Voies d\'Ascension', 'verses' => 44],
    ['num' => 71, 'name' => 'Nuh', 'name_ar' => 'نوح', 'name_fr' => 'Noé', 'verses' => 28],
    ['num' => 72, 'name' => 'Al-Jinn', 'name_ar' => 'الجن', 'name_fr' => 'Les Djinns', 'verses' => 28],
    ['num' => 73, 'name' => 'Al-Muzzammil', 'name_ar' => 'المزمل', 'name_fr' => 'L\'Enveloppé', 'verses' => 20],
    ['num' => 74, 'name' => 'Al-Muddaththir', 'name_ar' => 'المدثر', 'name_fr' => 'Le Revêtu d\'un Manteau', 'verses' => 56],
    ['num' => 75, 'name' => 'Al-Qiyamah', 'name_ar' => 'القيامة', 'name_fr' => 'La Résurrection', 'verses' => 40],
    ['num' => 76, 'name' => 'Al-Insan', 'name_ar' => 'الإنسان', 'name_fr' => 'L\'Homme', 'verses' => 31],
    ['num' => 77, 'name' => 'Al-Mursalat', 'name_ar' => 'المرسلات', 'name_fr' => 'Les Envoyés', 'verses' => 50],
    ['num' => 78, 'name' => 'An-Naba', 'name_ar' => 'النبأ', 'name_fr' => 'La Nouvelle', 'verses' => 40],
    ['num' => 79, 'name' => "An-Nazi'at", 'name_ar' => 'النازعات', 'name_fr' => 'Les Anges qui Arrachent', 'verses' => 46],
    ['num' => 80, 'name' => 'Abasa', 'name_ar' => 'عبس', 'name_fr' => 'Il s\'est Renfrogné', 'verses' => 42],
    ['num' => 81, 'name' => 'At-Takwir', 'name_ar' => 'التكوير', 'name_fr' => 'L\'Obscurcissement', 'verses' => 29],
    ['num' => 82, 'name' => 'Al-Infitar', 'name_ar' => 'الإنفطار', 'name_fr' => 'La Rupture', 'verses' => 19],
    ['num' => 83, 'name' => 'Al-Mutaffifin', 'name_ar' => 'المطففين', 'name_fr' => 'Les Fraudeurs', 'verses' => 36],
    ['num' => 84, 'name' => 'Al-Inshiqaq', 'name_ar' => 'الإنشقاق', 'name_fr' => 'La Déchirure', 'verses' => 25],
    ['num' => 85, 'name' => 'Al-Buruj', 'name_ar' => 'البروج', 'name_fr' => 'Les Constellations', 'verses' => 22],
    ['num' => 86, 'name' => 'At-Tariq', 'name_ar' => 'الطارق', 'name_fr' => 'L\'Astre Nocturne', 'verses' => 17],
    ['num' => 87, 'name' => "Al-A'la", 'name_ar' => 'الأعلى', 'name_fr' => 'Le Très-Haut', 'verses' => 19],
    ['num' => 88, 'name' => 'Al-Ghashiyah', 'name_ar' => 'الغاشية', 'name_fr' => 'L\'Enveloppante', 'verses' => 26],
    ['num' => 89, 'name' => 'Al-Fajr', 'name_ar' => 'الفجر', 'name_fr' => 'L\'Aube', 'verses' => 30],
    ['num' => 90, 'name' => 'Al-Balad', 'name_ar' => 'البلد', 'name_fr' => 'La Cité', 'verses' => 20],
    ['num' => 91, 'name' => 'Ash-Shams', 'name_ar' => 'الشمس', 'name_fr' => 'Le Soleil', 'verses' => 15],
    ['num' => 92, 'name' => 'Al-Layl', 'name_ar' => 'الليل', 'name_fr' => 'La Nuit', 'verses' => 21],
    ['num' => 93, 'name' => 'Ad-Duhaa', 'name_ar' => 'الضحى', 'name_fr' => 'Le Jour Montant', 'verses' => 11],
    ['num' => 94, 'name' => 'Ash-Sharh', 'name_ar' => 'الشرح', 'name_fr' => 'L\'Ouverture', 'verses' => 8],
    ['num' => 95, 'name' => 'At-Tin', 'name_ar' => 'التين', 'name_fr' => 'Le Figuier', 'verses' => 8],
    ['num' => 96, 'name' => 'Al-Alaq', 'name_ar' => 'العلق', 'name_fr' => 'L\'Adhérence', 'verses' => 19],
    ['num' => 97, 'name' => 'Al-Qadr', 'name_ar' => 'القدر', 'name_fr' => 'La Destinée', 'verses' => 5],
    ['num' => 98, 'name' => 'Al-Bayyinah', 'name_ar' => 'البينة', 'name_fr' => 'La Preuve', 'verses' => 8],
    ['num' => 99, 'name' => 'Az-Zalzalah', 'name_ar' => 'الزلزلة', 'name_fr' => 'La Secousse', 'verses' => 8],
    ['num' => 100, 'name' => 'Al-Adiyat', 'name_ar' => 'العاديات', 'name_fr' => 'Les Coursiers', 'verses' => 11],
    ['num' => 101, 'name' => "Al-Qari'ah", 'name_ar' => 'القارعة', 'name_fr' => 'Le Fracas', 'verses' => 11],
    ['num' => 102, 'name' => 'At-Takathur', 'name_ar' => 'التكاثر', 'name_fr' => 'La Course aux Richesses', 'verses' => 8],
    ['num' => 103, 'name' => 'Al-Asr', 'name_ar' => 'العصر', 'name_fr' => 'Le Temps', 'verses' => 3],
    ['num' => 104, 'name' => 'Al-Humazah', 'name_ar' => 'الهمزة', 'name_fr' => 'Les Calomniateurs', 'verses' => 9],
    ['num' => 105, 'name' => 'Al-Fil', 'name_ar' => 'الفيل', 'name_fr' => 'L\'Éléphant', 'verses' => 5],
    ['num' => 106, 'name' => 'Quraysh', 'name_ar' => 'قريش', 'name_fr' => 'Quraych', 'verses' => 4],
    ['num' => 107, 'name' => "Al-Ma'un", 'name_ar' => 'الماعون', 'name_fr' => 'L\'Ustensile', 'verses' => 7],
    ['num' => 108, 'name' => 'Al-Kawthar', 'name_ar' => 'الكوثر', 'name_fr' => 'L\'Abondance', 'verses' => 3],
    ['num' => 109, 'name' => 'Al-Kafirun', 'name_ar' => 'الكافرون', 'name_fr' => 'Les Infidèles', 'verses' => 6],
    ['num' => 110, 'name' => 'An-Nasr', 'name_ar' => 'النصر', 'name_fr' => 'Le Secours', 'verses' => 3],
    ['num' => 111, 'name' => 'Al-Masad', 'name_ar' => 'المسد', 'name_fr' => 'Les Fibres', 'verses' => 5],
    ['num' => 112, 'name' => 'Al-Ikhlas', 'name_ar' => 'الإخلاص', 'name_fr' => 'Le Monothéisme Pur', 'verses' => 4],
    ['num' => 113, 'name' => 'Al-Falaq', 'name_ar' => 'الفلق', 'name_fr' => 'L\'Aube Naissante', 'verses' => 5],
    ['num' => 114, 'name' => 'An-Nas', 'name_ar' => 'الناس', 'name_fr' => 'Les Hommes', 'verses' => 6]
];

$TAFSIR_SOURCES = [
    'ibn-kathir' => ['ar' => 'ar-tafsir-ibn-kathir', 'en' => 'en-tafisr-ibn-kathir'],
    'tabari' => ['ar' => 'ar-tafsir-al-tabari'],
    'qurtubi' => ['ar' => 'ar-tafseer-al-qurtubi']
];

// Fonctions utilitaires
function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function fetchJson($url) {
    $context = stream_context_create([
        'http' => ['timeout' => TIMEOUT, 'user_agent' => 'TafsirDownloader/4.0'],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);
    $content = @file_get_contents($url, false, $context);
    if ($content === false) return null;
    return json_decode($content, true);
}

function fetchSurahTafsirApi($surahNum, $edition) {
    $data = fetchJson(BASE_URL_API . "/{$edition}/{$surahNum}.json");
    return ($data && isset($data['ayahs'])) ? $data['ayahs'] : [];
}

function parseFrenchText($text, $totalVerses) {
    $tafsirs = [];
    $text = preg_replace('/\r\n|\r/', "\n", $text);
    
    // Nettoyer le texte
    $text = preg_replace('/\s+/', ' ', $text);
    
    // Pattern 1: {1}, {2}, etc. (accolades)
    if (preg_match_all('/\{(\d{1,3})\}\s*([^{}]+?)(?=\{\d{1,3}\}|$)/s', $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $num = intval($match[1]);
            if ($num >= 1 && $num <= $totalVerses && !isset($tafsirs[$num])) {
                $txt = trim($match[2]);
                if (strlen($txt) > 20) $tafsirs[$num] = $txt;
            }
        }
    }
    
    // Pattern 2: (1), (2), etc.
    if (count($tafsirs) < 3 && preg_match_all('/\((\d{1,3})\)\s*([^\(\)]+?)(?=\(\d{1,3}\)|$)/s', $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $num = intval($match[1]);
            if ($num >= 1 && $num <= $totalVerses && !isset($tafsirs[$num])) {
                $txt = trim($match[2]);
                if (strlen($txt) > 20) $tafsirs[$num] = $txt;
            }
        }
    }
    
    // Pattern 3: [1], [2], etc.
    if (count($tafsirs) < 3 && preg_match_all('/\[(\d{1,3})\]\s*([^\[\]]+?)(?=\[\d{1,3}\]|$)/s', $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $num = intval($match[1]);
            if ($num >= 1 && $num <= $totalVerses && !isset($tafsirs[$num])) {
                $txt = trim($match[2]);
                if (strlen($txt) > 20) $tafsirs[$num] = $txt;
            }
        }
    }
    
    // Pattern 4: "Verset 1", "Verset 2", etc.
    if (count($tafsirs) < 3 && preg_match_all('/[Vv]erset\s*(\d{1,3})\s*[:\-]?\s*(.+?)(?=[Vv]erset\s*\d|$)/si', $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $num = intval($match[1]);
            if ($num >= 1 && $num <= $totalVerses && !isset($tafsirs[$num])) {
                $txt = trim($match[2]);
                if (strlen($txt) > 20) $tafsirs[$num] = $txt;
            }
        }
    }
    
    return $tafsirs;
}

function processSurah($surah, $includeTabari, $includeQurtubi, $frenchTexts = []) {
    global $TAFSIR_SOURCES;
    
    $surahNum = $surah['num'];
    $result = [
        'surah' => $surahNum,
        'name' => $surah['name'],
        'name_ar' => $surah['name_ar'],
        'name_fr' => $surah['name_fr'],
        'total_verses' => $surah['verses'],
        'tafsirs' => [],
        'meta' => ['sources' => [], 'generated_at' => date('Y-m-d H:i:s')]
    ];
    
    $ayahMap = [];
    $logs = [];
    
    // Ibn Kathir Arabe
    $data = fetchSurahTafsirApi($surahNum, $TAFSIR_SOURCES['ibn-kathir']['ar']);
    foreach ($data as $a) {
        $n = $a['ayah'] ?? 0;
        $ayahMap[$n] = ['ayah' => $n, 'source' => 'Ibn Kathir', 'text_ar' => $a['text'] ?? ''];
    }
    $logs[] = "Ibn Kathir (ar): " . count($data);
    usleep(300000);
    
    // Ibn Kathir Anglais
    $data = fetchSurahTafsirApi($surahNum, $TAFSIR_SOURCES['ibn-kathir']['en']);
    foreach ($data as $a) {
        $n = $a['ayah'] ?? 0;
        if (!isset($ayahMap[$n])) $ayahMap[$n] = ['ayah' => $n, 'source' => 'Ibn Kathir'];
        $ayahMap[$n]['text_en'] = $a['text'] ?? '';
    }
    $logs[] = "Ibn Kathir (en): " . count($data);
    usleep(300000);
    
    // Français (fourni par le client via pdf.js)
    if (!empty($frenchTexts) && is_array($frenchTexts)) {
        foreach ($frenchTexts as $n => $txt) {
            $n = intval($n);
            if ($n >= 1 && $n <= $surah['verses']) {
                if (!isset($ayahMap[$n])) $ayahMap[$n] = ['ayah' => $n, 'source' => 'Ibn Kathir'];
                $ayahMap[$n]['text_fr'] = $txt;
            }
        }
        $logs[] = "Ibn Kathir (fr): " . count($frenchTexts) . " (pdf.js)";
    }
    
    // Tabari
    if ($includeTabari) {
        $data = fetchSurahTafsirApi($surahNum, $TAFSIR_SOURCES['tabari']['ar']);
        foreach ($data as $a) {
            $n = $a['ayah'] ?? 0;
            $ayahMap["t{$n}"] = ['ayah' => $n, 'source' => 'Al-Tabari', 'text_ar' => $a['text'] ?? ''];
        }
        $logs[] = "Tabari: " . count($data);
        usleep(300000);
    }
    
    // Qurtubi
    if ($includeQurtubi) {
        $data = fetchSurahTafsirApi($surahNum, $TAFSIR_SOURCES['qurtubi']['ar']);
        foreach ($data as $a) {
            $n = $a['ayah'] ?? 0;
            $ayahMap["q{$n}"] = ['ayah' => $n, 'source' => 'Al-Qurtubi', 'text_ar' => $a['text'] ?? ''];
        }
        $logs[] = "Qurtubi: " . count($data);
    }
    
    // Formater
    foreach ($ayahMap as $v) {
        $v['text'] = $v['text_fr'] ?? $v['text_en'] ?? $v['text_ar'] ?? '';
        $result['tafsirs'][] = $v;
    }
    
    usort($result['tafsirs'], fn($a, $b) => $a['ayah'] - $b['ayah'] ?: strcmp($a['source'], $b['source']));
    $result['meta']['logs'] = $logs;
    
    return $result;
}

// Création des dossiers
if (!is_dir(OUTPUT_DIR)) @mkdir(OUTPUT_DIR, 0755, true);
if (!is_dir(PDF_DIR)) @mkdir(PDF_DIR, 0755, true);

// Traitement AJAX
if (isset($_GET['action'])) {
    
    // PROXY PDF - Télécharge et sert le PDF localement pour éviter CORS
    if ($_GET['action'] === 'get_pdf') {
        $surahNum = intval($_GET['surah'] ?? 1);
        if ($surahNum < 1 || $surahNum > 114) {
            http_response_code(400);
            exit('Invalid surah');
        }
        
        $pdfPath = PDF_DIR . "/surah_{$surahNum}.pdf";
        
        // Télécharger si pas en cache
        if (!file_exists($pdfPath)) {
            $pdfUrl = BASE_URL_PDF_FR . "/{$surahNum}.pdf";
            $context = stream_context_create([
                'http' => ['timeout' => 60, 'user_agent' => 'Mozilla/5.0'],
                'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
            ]);
            $pdfContent = @file_get_contents($pdfUrl, false, $context);
            
            if ($pdfContent === false) {
                http_response_code(502);
                exit('Failed to download PDF from tafsir.be');
            }
            
            file_put_contents($pdfPath, $pdfContent);
        }
        
        // Servir le PDF
        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($pdfPath));
        header('Access-Control-Allow-Origin: *');
        readfile($pdfPath);
        exit;
    }
    
    if ($_GET['action'] === 'check_status') {
        jsonResponse([
            'php_version' => PHP_VERSION,
            'allow_url_fopen' => ini_get('allow_url_fopen'),
            'method' => 'pdf.js (client-side)'
        ]);
    }
    
    // Parser le texte français extrait par pdf.js
    if ($_GET['action'] === 'parse_french') {
        $input = json_decode(file_get_contents('php://input'), true);
        $text = $input['text'] ?? '';
        $totalVerses = intval($input['totalVerses'] ?? 7);
        
        $tafsirs = parseFrenchText($text, $totalVerses);
        
        jsonResponse([
            'success' => true,
            'tafsirs_count' => count($tafsirs),
            'tafsirs' => $tafsirs,
            'text_length' => strlen($text),
            'text_preview' => mb_substr($text, 0, 500)
        ]);
    }
    
    // Télécharger une sourate (avec texte français fourni)
    if ($_GET['action'] === 'download') {
        $input = json_decode(file_get_contents('php://input'), true);
        $surahNum = intval($input['surah'] ?? $_GET['surah'] ?? 1);
        $frenchTexts = $input['frenchTexts'] ?? [];
        $includeTabari = isset($input['tabari']) || isset($_GET['tabari']);
        $includeQurtubi = isset($input['qurtubi']) || isset($_GET['qurtubi']);
        
        if ($surahNum < 1 || $surahNum > 114) {
            jsonResponse(['error' => 'Numéro invalide']);
        }
        
        $surah = $SURAHS[$surahNum - 1];
        $result = processSurah($surah, $includeTabari, $includeQurtubi, $frenchTexts);
        
        $filename = "tafsir_surah_{$surahNum}.json";
        file_put_contents(OUTPUT_DIR . "/{$filename}", json_encode([$result], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        jsonResponse([
            'success' => true,
            'surah' => $surahNum,
            'name' => $surah['name'],
            'tafsirs_count' => count($result['tafsirs']),
            'filename' => $filename,
            'logs' => $result['meta']['logs']
        ]);
    }
    
    // Télécharger un lot
    if ($_GET['action'] === 'download_range') {
        $input = json_decode(file_get_contents('php://input'), true);
        $start = max(1, min(114, intval($input['start'] ?? 1)));
        $end = max($start, min(114, intval($input['end'] ?? 10)));
        $allFrenchTexts = $input['allFrenchTexts'] ?? [];
        $includeTabari = isset($input['tabari']);
        $includeQurtubi = isset($input['qurtubi']);
        
        $results = [];
        $logs = [];
        
        for ($i = $start; $i <= $end; $i++) {
            $surah = $SURAHS[$i - 1];
            $frenchTexts = $allFrenchTexts[$i] ?? [];
            $result = processSurah($surah, $includeTabari, $includeQurtubi, $frenchTexts);
            $results[] = $result;
            $logs[] = "Sourate {$i}: " . count($result['tafsirs']) . " tafsirs";
        }
        
        $filename = "tafsir_surahs_{$start}-{$end}.json";
        file_put_contents(OUTPUT_DIR . "/{$filename}", json_encode($results, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        jsonResponse([
            'success' => true,
            'range' => "{$start}-{$end}",
            'surahs_count' => count($results),
            'tafsirs_count' => array_sum(array_map(fn($r) => count($r['tafsirs']), $results)),
            'filename' => $filename,
            'logs' => $logs
        ]);
    }
    
    // Liste fichiers
    if ($_GET['action'] === 'list_files') {
        $files = [];
        foreach (glob(OUTPUT_DIR . '/*.json') as $f) {
            $files[] = ['name' => basename($f), 'size' => filesize($f), 'date' => date('Y-m-d H:i', filemtime($f))];
        }
        jsonResponse($files);
    }
    
    // Télécharger fichier
    if ($_GET['action'] === 'get_file' && isset($_GET['file'])) {
        $file = OUTPUT_DIR . '/' . basename($_GET['file']);
        if (file_exists($file)) {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . basename($file) . '"');
            readfile($file);
            exit;
        }
        jsonResponse(['error' => 'Fichier non trouvé']);
    }
    
    jsonResponse(['error' => 'Action inconnue']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🕌 Tafsir Downloader v4.1 - pdf.js + Tesseract</title>
    <!-- PDF.js de Mozilla via CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <!-- Tesseract.js OCR via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <style>
        :root { --primary: #1a5f4a; --secondary: #c4a35a; --bg: #f5f5f0; --card: #fff; --success: #27ae60; --warning: #f39c12; --danger: #e74c3c; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        header { text-align: center; padding: 25px; background: linear-gradient(135deg, var(--primary), #2d8a6b); color: #fff; border-radius: 12px; margin-bottom: 20px; }
        header small { opacity: 0.8; }
        .card { background: var(--card); border-radius: 12px; padding: 20px; margin-bottom: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        .card h2 { color: var(--primary); border-bottom: 2px solid var(--secondary); padding-bottom: 10px; margin-bottom: 15px; font-size: 1.2em; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .alert-info { background: #e3f2fd; border-left: 4px solid #2196f3; color: #1565c0; }
        .alert-success { background: #e8f5e9; border-left: 4px solid var(--success); color: #2e7d32; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: var(--primary); }
        select, input[type="number"] { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
        .row { display: flex; gap: 15px; }
        .row > * { flex: 1; }
        .checkbox-group { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px; }
        .checkbox-item { display: flex; align-items: center; gap: 5px; padding: 8px 12px; background: var(--bg); border-radius: 6px; cursor: pointer; }
        .checkbox-item.highlight { background: #e8f5e9; border: 1px solid var(--success); }
        .checkbox-item input { cursor: pointer; }
        .btn { display: inline-block; padding: 12px 24px; background: var(--primary); color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; }
        .btn:hover { background: #2d8a6b; }
        .btn:disabled { background: #ccc; cursor: not-allowed; }
        .btn-success { background: var(--success); }
        .btn-small { padding: 8px 14px; font-size: 13px; }
        #log { background: #1a1a2e; color: #16c79a; padding: 15px; border-radius: 8px; font-family: 'Consolas', monospace; font-size: 12px; max-height: 300px; overflow-y: auto; white-space: pre-wrap; line-height: 1.6; }
        .files-list { list-style: none; }
        .files-list li { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
        .progress-bar { height: 8px; background: #e0e0e0; border-radius: 4px; margin: 10px 0; overflow: hidden; }
        .progress-bar .fill { height: 100%; background: var(--success); transition: width 0.3s; }
        .status-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 13px; margin: 3px; }
        .status-ok { background: #e8f5e9; color: #2e7d32; }
        .extraction-selector { display: flex; gap: 15px; margin: 15px 0; flex-wrap: wrap; }
        .radio-card { flex: 1; min-width: 200px; cursor: pointer; }
        .radio-card input { display: none; }
        .radio-content { display: block; padding: 15px; border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.2s; }
        .radio-card input:checked + .radio-content { border-color: var(--primary); background: #e8f5e9; }
        .radio-title { display: block; font-weight: 600; color: var(--primary); margin-bottom: 5px; }
        .radio-desc { display: block; font-size: 12px; color: #666; }
        .tesseract-progress { margin-top: 10px; padding: 10px; background: #fff3e0; border-radius: 8px; font-size: 13px; }
        @media (max-width: 600px) { .row, .checkbox-group, .extraction-selector { flex-direction: column; } }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>🕌 Tafsir Downloader</h1>
        <p>v4.0 - My Pocket Imam</p>
        <small>Extraction PDF avec Mozilla pdf.js</small>
    </header>
    
    <div class="card">
        <h2>⚙️ Configuration</h2>
        <div class="alert alert-info">
            <strong>Méthode d'extraction PDF :</strong>
        </div>
        <div class="extraction-selector">
            <label class="radio-card">
                <input type="radio" name="extractMethod" value="pdfjs" checked>
                <div class="radio-content">
                    <span class="radio-title">📄 pdf.js</span>
                    <span class="radio-desc">Rapide (~1s/page) - Extrait le texte intégré au PDF</span>
                </div>
            </label>
            <label class="radio-card">
                <input type="radio" name="extractMethod" value="tesseract">
                <div class="radio-content">
                    <span class="radio-title">🔍 Tesseract OCR</span>
                    <span class="radio-desc">Lent (~30s/page) - Reconnaissance de caractères sur images</span>
                </div>
            </label>
        </div>
        <div id="status"></div>
        <div id="tesseract-status" style="display:none;"></div>
    </div>
    
    <div class="card">
        <h2>📖 Télécharger une sourate</h2>
        <label>Sourate :</label>
        <select id="surah">
            <?php foreach ($SURAHS as $s): ?>
            <option value="<?= $s['num'] ?>" data-verses="<?= $s['verses'] ?>"><?= $s['num'] ?>. <?= $s['name'] ?> (<?= $s['name_ar'] ?>) - <?= $s['verses'] ?> v.</option>
            <?php endforeach; ?>
        </select>
        <div class="checkbox-group">
            <label class="checkbox-item highlight"><input type="checkbox" id="french" checked> 🇫🇷 Français (PDF tafsir.be)</label>
            <label class="checkbox-item"><input type="checkbox" id="tabari"> Tabari (ar)</label>
            <label class="checkbox-item"><input type="checkbox" id="qurtubi"> Qurtubi (ar)</label>
        </div>
        <button class="btn" onclick="downloadSurah()">⬇️ Télécharger</button>
        <button class="btn btn-small" onclick="testPdfJs()" style="margin-left:10px;">🧪 Tester extraction</button>
    </div>
    
    <div class="card">
        <h2>📚 Télécharger plusieurs sourates</h2>
        <div class="row">
            <div><label>De :</label><input type="number" id="start" value="1" min="1" max="114"></div>
            <div><label>À :</label><input type="number" id="end" value="10" min="1" max="114"></div>
        </div>
        <div class="checkbox-group">
            <label class="checkbox-item highlight"><input type="checkbox" id="frenchRange" checked> 🇫🇷 Français</label>
            <label class="checkbox-item"><input type="checkbox" id="tabariRange"> Tabari</label>
            <label class="checkbox-item"><input type="checkbox" id="qurtubiRange"> Qurtubi</label>
        </div>
        <div class="progress-bar" id="progressBar" style="display:none;"><div class="fill" id="progressFill"></div></div>
        <button class="btn" onclick="downloadRange()">⬇️ Télécharger le lot</button>
    </div>
    
    <div class="card">
        <h2>📋 Journal</h2>
        <div id="log">Prêt. Sélectionnez une sourate et cliquez sur Télécharger.</div>
    </div>
    
    <div class="card">
        <h2>📁 Fichiers générés</h2>
        <ul class="files-list" id="files"><li>Chargement...</li></ul>
        <button class="btn btn-small" onclick="loadFiles()" style="margin-top:10px;">🔄 Rafraîchir</button>
    </div>
</div>

<script>
// Configuration pdf.js
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const log = document.getElementById('log');
// Proxy PHP local pour éviter les problèmes CORS
const PDF_PROXY_URL = '?action=get_pdf&surah=';

const SURAHS_DATA = <?= json_encode(array_map(fn($s) => ['num' => $s['num'], 'verses' => $s['verses']], $SURAHS)) ?>;

// Worker Tesseract (initialisé à la demande)
let tesseractWorker = null;

function addLog(msg, type = 'info') {
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const colors = { success: '#2ecc71', error: '#e74c3c', warning: '#f39c12', info: '#3498db' };
    const time = new Date().toLocaleTimeString();
    log.innerHTML += `<span style="color:${colors[type]}">[${time}] ${icons[type]} ${msg}</span>\n`;
    log.scrollTop = log.scrollHeight;
}

function clearLog() {
    log.innerHTML = '';
}

function getExtractionMethod() {
    return document.querySelector('input[name="extractMethod"]:checked').value;
}

// Initialiser Tesseract Worker
async function initTesseract() {
    if (tesseractWorker) return tesseractWorker;
    
    const statusDiv = document.getElementById('tesseract-status');
    statusDiv.style.display = 'block';
    statusDiv.innerHTML = '<div class="tesseract-progress">⏳ Initialisation de Tesseract (téléchargement modèle français ~15MB)...</div>';
    
    try {
        tesseractWorker = await Tesseract.createWorker('fra', 1, {
            logger: m => {
                if (m.status === 'recognizing text') {
                    statusDiv.innerHTML = `<div class="tesseract-progress">🔍 OCR en cours: ${Math.round(m.progress * 100)}%</div>`;
                } else if (m.status) {
                    statusDiv.innerHTML = `<div class="tesseract-progress">⏳ ${m.status}...</div>`;
                }
            }
        });
        statusDiv.innerHTML = '<div class="tesseract-progress" style="background:#e8f5e9;">✅ Tesseract prêt (modèle français chargé)</div>';
        return tesseractWorker;
    } catch (error) {
        statusDiv.innerHTML = `<div class="tesseract-progress" style="background:#ffebee;">❌ Erreur Tesseract: ${error.message}</div>`;
        throw error;
    }
}

// Extraire le texte avec pdf.js (méthode rapide)
async function extractWithPdfJs(surahNum) {
    try {
        const pdfUrl = PDF_PROXY_URL + surahNum;
        addLog(`[pdf.js] Chargement PDF sourate ${surahNum}...`);
        
        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
            cMapPacked: true
        });
        
        const pdf = await loadingTask.promise;
        addLog(`[pdf.js] PDF chargé: ${pdf.numPages} pages`);
        
        let fullText = '';
        
        for (let i = 1; i <= pdf.numPages; i++) {
            const page = await pdf.getPage(i);
            const textContent = await page.getTextContent();
            const pageText = textContent.items.map(item => item.str).join(' ');
            fullText += pageText + '\n';
        }
        
        addLog(`[pdf.js] Texte extrait: ${fullText.length} caractères`);
        return { text: fullText, method: 'pdf.js' };
        
    } catch (error) {
        addLog(`[pdf.js] Erreur: ${error.message}`, 'error');
        return null;
    }
}

// Extraire le texte avec Tesseract OCR (méthode lente mais précise)
async function extractWithTesseract(surahNum) {
    try {
        const pdfUrl = PDF_PROXY_URL + surahNum;
        addLog(`[Tesseract] Chargement PDF sourate ${surahNum}...`);
        
        // Charger le PDF avec pdf.js pour le rendre en images
        const loadingTask = pdfjsLib.getDocument({
            url: pdfUrl,
            cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/cmaps/',
            cMapPacked: true
        });
        
        const pdf = await loadingTask.promise;
        addLog(`[Tesseract] PDF chargé: ${pdf.numPages} pages - OCR en cours...`, 'warning');
        
        // Initialiser Tesseract
        const worker = await initTesseract();
        
        let fullText = '';
        
        for (let i = 1; i <= pdf.numPages; i++) {
            addLog(`[Tesseract] OCR page ${i}/${pdf.numPages}...`);
            
            const page = await pdf.getPage(i);
            
            // Rendre la page en canvas (haute résolution pour meilleur OCR)
            const viewport = page.getViewport({ scale: 2.0 });
            const canvas = document.createElement('canvas');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            await page.render({
                canvasContext: canvas.getContext('2d'),
                viewport: viewport
            }).promise;
            
            // OCR sur l'image
            const { data: { text } } = await worker.recognize(canvas);
            fullText += text + '\n';
            
            addLog(`[Tesseract] Page ${i}: ${text.length} caractères extraits`);
        }
        
        addLog(`[Tesseract] OCR terminé: ${fullText.length} caractères total`, 'success');
        return { text: fullText, method: 'Tesseract OCR' };
        
    } catch (error) {
        addLog(`[Tesseract] Erreur: ${error.message}`, 'error');
        return null;
    }
}

// Fonction principale d'extraction
async function extractPdfText(surahNum) {
    const method = getExtractionMethod();
    
    if (method === 'tesseract') {
        return extractWithTesseract(surahNum);
    } else {
        return extractWithPdfJs(surahNum);
    }
}

// Parser le texte français pour extraire les versets
function parseFrenchText(text, totalVerses) {
    const tafsirs = {};
    
    // Nettoyer le texte (garder les sauts de ligne pour mieux parser)
    text = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
    
    // Stratégie : trouver les positions de chaque numéro de verset
    // Format typique : "texte (2)." ou "texte(2)" suivi du tafsir
    
    // Construire une regex qui trouve les numéros de versets isolés
    // On cherche (1), (2), etc. mais pas (123) ou des références comme (Rapporté par...)
    const versePositions = [];
    
    for (let v = 1; v <= totalVerses; v++) {
        // Chercher toutes les occurrences de ce numéro de verset
        // Pattern: le numéro entre parenthèses, possiblement avec un point ou espace après
        const pattern = new RegExp(`\\(${v}\\)[.\\s]`, 'g');
        let match;
        while ((match = pattern.exec(text)) !== null) {
            versePositions.push({ verse: v, position: match.index, endPosition: match.index + match[0].length });
        }
    }
    
    // Trier par position dans le texte
    versePositions.sort((a, b) => a.position - b.position);
    
    // Dédupliquer : garder seulement la première occurrence de chaque verset
    const seenVerses = new Set();
    const uniquePositions = versePositions.filter(vp => {
        if (seenVerses.has(vp.verse)) return false;
        seenVerses.add(vp.verse);
        return true;
    });
    
    // Extraire le texte entre chaque paire de versets consécutifs
    for (let i = 0; i < uniquePositions.length; i++) {
        const current = uniquePositions[i];
        const next = uniquePositions[i + 1];
        
        // Le tafsir commence après le numéro du verset
        const startPos = current.endPosition;
        // Et finit au début du prochain numéro, ou à la fin du texte
        const endPos = next ? next.position : text.length;
        
        let tafsirText = text.substring(startPos, endPos).trim();
        
        // Nettoyer le texte
        tafsirText = tafsirText
            .replace(/^\s*[.\-:]\s*/, '') // Enlever ponctuation au début
            .replace(/\s+/g, ' ')          // Normaliser espaces
            .trim();
        
        // Ne garder que si le texte est substantiel (plus de 50 caractères)
        if (tafsirText.length > 50) {
            tafsirs[current.verse] = tafsirText;
        }
    }
    
    return tafsirs;
}

// Test pdf.js sur une sourate
async function testPdfJs() {
    clearLog();
    const surahNum = document.getElementById('surah').value;
    const surahData = SURAHS_DATA.find(s => s.num == surahNum);
    const method = getExtractionMethod();
    
    addLog(`Test ${method} sur sourate ${surahNum} (${surahData.verses} versets)...`);
    
    const result = await extractPdfText(surahNum);
    
    if (result && result.text) {
        addLog(`Méthode utilisée: ${result.method}`, 'success');
        addLog(`--- Aperçu du texte brut (500 premiers caractères) ---`, 'info');
        addLog(result.text.substring(0, 500), 'info');
        
        const tafsirs = parseFrenchText(result.text, surahData.verses);
        const count = Object.keys(tafsirs).length;
        
        addLog(`Versets parsés: ${count}/${surahData.verses}`, count > 0 ? 'success' : 'warning');
        
        // Afficher les longueurs de chaque tafsir
        for (const [num, txt] of Object.entries(tafsirs)) {
            addLog(`  Verset ${num}: ${txt.length} caractères`, 'info');
        }
        
        // Afficher un exemple complet
        if (tafsirs[2]) {
            addLog(`--- Exemple: Tafsir verset 2 (premiers 500 car.) ---`, 'success');
            addLog(tafsirs[2].substring(0, 500) + '...', 'info');
        } else if (tafsirs[1]) {
            addLog(`--- Exemple: Tafsir verset 1 (premiers 500 car.) ---`, 'success');
            addLog(tafsirs[1].substring(0, 500) + '...', 'info');
        }
    }
}

// Télécharger une sourate
async function downloadSurah() {
    clearLog();
    const surahNum = parseInt(document.getElementById('surah').value);
    const surahData = SURAHS_DATA.find(s => s.num === surahNum);
    const includeFrench = document.getElementById('french').checked;
    const includeTabari = document.getElementById('tabari').checked;
    const includeQurtubi = document.getElementById('qurtubi').checked;
    const method = getExtractionMethod();
    
    addLog(`Téléchargement sourate ${surahNum} (méthode: ${method})...`);
    
    let frenchTexts = {};
    
    // Extraire le français si demandé
    if (includeFrench) {
        const result = await extractPdfText(surahNum);
        
        if (result && result.text) {
            frenchTexts = parseFrenchText(result.text, surahData.verses);
            addLog(`Versets français extraits: ${Object.keys(frenchTexts).length} (${result.method})`, 'success');
        } else {
            addLog(`Impossible d'extraire le PDF français`, 'warning');
        }
    }
    
    // Envoyer au serveur
    addLog(`Récupération des tafsirs depuis l'API...`);
    
    try {
        const response = await fetch('?action=download', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                surah: surahNum,
                frenchTexts: frenchTexts,
                tabari: includeTabari,
                qurtubi: includeQurtubi
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            addLog(`Sourate ${data.surah} téléchargée !`, 'success');
            data.logs?.forEach(l => addLog(l));
            addLog(`Fichier: ${data.filename}`, 'success');
            loadFiles();
        } else {
            addLog(data.error || 'Erreur', 'error');
        }
    } catch (e) {
        addLog(`Erreur: ${e.message}`, 'error');
    }
}

// Télécharger un lot de sourates
async function downloadRange() {
    clearLog();
    const start = parseInt(document.getElementById('start').value);
    const end = parseInt(document.getElementById('end').value);
    const includeFrench = document.getElementById('frenchRange').checked;
    const includeTabari = document.getElementById('tabariRange').checked;
    const includeQurtubi = document.getElementById('qurtubiRange').checked;
    
    const progressBar = document.getElementById('progressBar');
    const progressFill = document.getElementById('progressFill');
    progressBar.style.display = 'block';
    
    addLog(`Téléchargement sourates ${start} à ${end} (méthode: ${getExtractionMethod()})...`);
    
    const allFrenchTexts = {};
    const total = end - start + 1;
    
    // Extraire tous les PDFs français si demandé
    if (includeFrench) {
        for (let i = start; i <= end; i++) {
            const surahData = SURAHS_DATA.find(s => s.num === i);
            const progress = ((i - start) / total * 50);
            progressFill.style.width = progress + '%';
            
            addLog(`[${i}/${end}] Extraction PDF sourate ${i}...`);
            
            const result = await extractPdfText(i);
            
            if (result && result.text) {
                allFrenchTexts[i] = parseFrenchText(result.text, surahData.verses);
                addLog(`Sourate ${i}: ${Object.keys(allFrenchTexts[i]).length} versets FR (${result.method})`, 'success');
            }
            
            // Petite pause pour ne pas surcharger
            await new Promise(r => setTimeout(r, 100));
        }
    }
    
    // Envoyer au serveur
    addLog(`Récupération des tafsirs depuis l'API...`);
    progressFill.style.width = '75%';
    
    try {
        const response = await fetch('?action=download_range', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                start: start,
                end: end,
                allFrenchTexts: allFrenchTexts,
                tabari: includeTabari,
                qurtubi: includeQurtubi
            })
        });
        
        const data = await response.json();
        progressFill.style.width = '100%';
        
        if (data.success) {
            addLog(`Lot terminé !`, 'success');
            addLog(`${data.surahs_count} sourates, ${data.tafsirs_count} tafsirs total`);
            addLog(`Fichier: ${data.filename}`, 'success');
            loadFiles();
        } else {
            addLog(data.error || 'Erreur', 'error');
        }
    } catch (e) {
        addLog(`Erreur: ${e.message}`, 'error');
    }
    
    setTimeout(() => { progressBar.style.display = 'none'; }, 2000);
}

// Charger la liste des fichiers
async function loadFiles() {
    try {
        const response = await fetch('?action=list_files');
        const files = await response.json();
        const list = document.getElementById('files');
        
        if (!files.length) {
            list.innerHTML = '<li>Aucun fichier généré</li>';
            return;
        }
        
        list.innerHTML = files.map(f => `
            <li>
                <div>
                    <strong>${f.name}</strong><br>
                    <small>${(f.size/1024).toFixed(1)} KB - ${f.date}</small>
                </div>
                <a href="?action=get_file&file=${encodeURIComponent(f.name)}" class="btn btn-small">⬇️</a>
            </li>
        `).join('');
    } catch (e) {
        console.error(e);
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    loadFiles();
    document.getElementById('status').innerHTML = '<span class="status-badge status-ok">✅ pdf.js prêt</span> <span class="status-badge status-ok">✅ Tesseract.js disponible</span>';
    
    // Afficher un avertissement quand Tesseract est sélectionné
    document.querySelectorAll('input[name="extractMethod"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const statusDiv = document.getElementById('tesseract-status');
            if (e.target.value === 'tesseract') {
                statusDiv.style.display = 'block';
                statusDiv.innerHTML = '<div class="tesseract-progress">⚠️ Tesseract OCR est beaucoup plus lent (~30s/page). Le modèle français (~15MB) sera téléchargé au premier usage.</div>';
            } else {
                statusDiv.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>

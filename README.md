# 🕌 Tafsir downloader - My Pocket Imam

Base de données JSON complète des tafsirs (exégèses) du Coran pour l'application My Pocket Imam.

## 📋 Sources utilisées

| Source | Auteur | Époque | Langues disponibles |
|--------|--------|--------|---------------------|
| **Tafsir Ibn Kathir** | Ibn Kathir (1301-1373) | 14ème siècle | 🇸🇦 Arabe, 🇬🇧 Anglais, 🇫🇷 Français* |
| **Tafsir Al-Tabari** | Al-Tabari (839-923) | 10ème siècle | 🇸🇦 Arabe |
| **Tafsir Al-Qurtubi** | Al-Qurtubi (1214-1273) | 13ème siècle | 🇸🇦 Arabe |

\* Le français est obtenu via traduction ou parsing de PDFs

## 📁 Structure des fichiers

```
tafsir-project/
├── scripts/
│   └── download_tafsirs.py     # Script de téléchargement
├── data/                        # Données brutes (intermédiaires)
├── output/                      # JSON finaux
│   ├── tafsir_ibn_kathir_1-10.json
│   ├── tafsir_ibn_kathir_11-20.json
│   └── ...
└── README.md
```

## 📐 Format JSON

### Format rétro-compatible (comme demandé)

```json
[
  {
    "surah": 1,
    "name": "Al-Fatiha",
    "name_ar": "الفاتحة",
    "name_en": "The Opening",
    "total_verses": 7,
    "tafsirs": [
      {
        "ayah": 1,
        "text": "Au nom d'Allah...",
        "text_ar": "بِسْمِ اللَّهِ...",
        "text_en": "In the Name of Allah...",
        "text_fr": "Au nom d'Allah...",
        "source": "Ibn Kathir"
      }
    ]
  }
]
```

### Champs

| Champ | Type | Description |
|-------|------|-------------|
| `surah` | number | Numéro de la sourate (1-114) |
| `name` | string | Nom translittéré |
| `name_ar` | string | Nom en arabe |
| `name_en` | string | Nom en anglais |
| `total_verses` | number | Nombre de versets |
| `tafsirs` | array | Liste des tafsirs |
| `tafsirs[].ayah` | number | Numéro du verset |
| `tafsirs[].text` | string | Texte (rétro-compatibilité, = text_fr ou text_en) |
| `tafsirs[].text_ar` | string | Tafsir en arabe |
| `tafsirs[].text_en` | string | Tafsir en anglais |
| `tafsirs[].text_fr` | string | Tafsir en français |
| `tafsirs[].source` | string | Source (Ibn Kathir, Al-Tabari, Al-Qurtubi) |

## 🚀 Utilisation du script

### Prérequis

- Python 3.7+
- Connexion Internet

### Téléchargement

```bash
# Toutes les sourates (1-114) - Ibn Kathir uniquement
python download_tafsirs.py

# Sourates 1 à 10
python download_tafsirs.py 1 10

# Sourates 78 à 114 (Juz Amma) avec Tabari et Qurtubi
python download_tafsirs.py 78 114 --tabari --qurtubi

# Une seule sourate
python download_tafsirs.py 36 36
```

### Sortie

Les fichiers sont générés dans `./output/` :
- `tafsir_ibn_kathir_1-10.json`
- `tafsir_ibn_kathir_11-20.json`
- etc.

## 📊 Statistiques

| Donnée | Valeur |
|--------|--------|
| Sourates | 114 |
| Versets totaux | 6236 |
| Tafsirs par verset | 1-3 (selon sources) |
| Taille estimée (Ibn Kathir seul) | ~50 MB |
| Taille estimée (3 sources) | ~150 MB |

## 🔗 APIs utilisées

- **spa5k/tafsir_api** : https://github.com/spa5k/tafsir_api
  - Sans rate limit
  - CDN via jsDelivr
  - 27 tafsirs disponibles

## 📜 Sources françaises (PDFs)

Pour le français, les sources suivantes peuvent être parsées :

1. **tafsir.be** - Tafsir Ibn Kathir complet
   - Format : PDF par sourate
   - URL : `https://tafsir.be/{num}.pdf`

2. **Archive.org** - Tafsir Ibn Kathir 114 sourates
   - Lien : https://archive.org/details/tafsir-ibnkathir-complet-francais

3. **Archive.org** - 4 tomes regroupés
   - Lien : https://archive.org/details/lexegese-du-coran-4-tomes-ibn-kathir

## ⚠️ Notes importantes

1. **Authenticité** : Ces tafsirs proviennent de sources reconnues mais sont des versions abrégées ou traduites. Pour une étude approfondie, référez-vous aux ouvrages originaux en arabe.

2. **Traduction française** : La version française disponible via API est limitée. Les PDFs de tafsir.be ou archive.org peuvent être parsés pour une version complète.

3. **Usage** : Ce projet est destiné à un usage éducatif et personnel dans le cadre de l'application My Pocket Imam.

## 📝 Licence

Données : Domaine public (textes religieux classiques)
Scripts : MIT License

---

Créé pour **My Pocket Imam** par Mehdy
Généré avec l'aide de Claude (Anthropic)

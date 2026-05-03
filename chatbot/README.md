# 🍞 Bakery Chatbot — Panduan Lengkap

Chatbot produk bakery berbasis **NLP lokal** (tanpa API AI eksternal).  
Hanya membaca database MySQL dengan query `SELECT`.

---

## 📁 Struktur Proyek

```
bakery_chatbot/
├── main.py              ← Jalankan chatbot
├── train.py             ← Latih/retrain model
├── config.py            ← Konfigurasi DB & chatbot
├── requirements.txt
├── nlp/
│   ├── classifier.py    ← TF-IDF + cosine similarity (intent classifier)
│   ├── entity_extractor.py ← Ekstrak produk, kategori, harga, sort
│   ├── preprocessor.py  ← Normalisasi, typo koreksi
│   └── synonyms.py      ← Kamus sinonim & keyword
├── db/
│   ├── connection.py    ← Koneksi MySQL (SELECT only)
│   └── query_builder.py ← Bangun query SQL dari intent
├── bot/
│   └── chatbot.py       ← Engine utama
├── response/
│   └── formatter.py     ← Format output bersih
└── data/
    ├── intents.json     ← Data training pola & intent
    └── model/           ← Model tersimpan (auto-generated)
```

---

## ⚡ Quick Start

### 1. Install dependencies
```bash
pip install -r requirements.txt
```

### 2. Konfigurasi database
Edit `config.py`:
```python
DB_CONFIG = {
    "host":     "localhost",
    "user":     "root",
    "password": "password_anda",
    "database": "nama_database_anda",
}
```

### 3. Latih model
```bash
python train.py
```

### 4. Jalankan chatbot
```bash
python main.py
```

---

## 🧠 Cara Melatih Model

Model NLP menggunakan **TF-IDF + cosine similarity** — bukan deep learning,
sehingga training sangat cepat (< 1 detik) dan bisa diulang kapan saja.

### Tambah pola baru di `data/intents.json`

```json
{
  "tag": "best_seller",
  "patterns": [
    "terlaris",
    "paling laku",
    "rekomendasiin dong",
    "yang enak apa ya",      ← tambah pola baru di sini
    "mana yang populer"      ← dan ini
  ]
}
```

Setelah menambah pola, **wajib retrain**:
```bash
python train.py
```

### Tambah intent baru

1. Buka `data/intents.json`
2. Tambah entry baru:
```json
{
  "tag": "promo",
  "patterns": [
    "ada promo", "diskon", "sale", "potongan harga", "promo hari ini"
  ],
  "responses": ["__QUERY__"]
}
```
3. Tambah keyword di `nlp/synonyms.py` → `INTENT_KEYWORDS`:
```python
"promo": ["promo", "diskon", "sale", "potongan"],
```
4. Tambah handler di `bot/chatbot.py` → method `_route()`:
```python
"promo": self._handle_promo,
```
5. Buat method handler:
```python
def _handle_promo(self, entities: dict) -> str:
    rows = self.qb.get_promo_products()  # buat query di query_builder.py
    return fmt.table_products(rows, "Produk Promo")
```
6. Tambah query di `db/query_builder.py`
7. Retrain: `python train.py`

---

## 📊 Cara Kerja NLP

```
Input user
    ↓
[Preprocessor]
  • Lowercase
  • Hapus tanda baca
  • Koreksi typo (TYPO_MAP)
  • Tokenisasi
    ↓
[Intent Classifier]
  • Keyword shortcut (cepat)     → deteksi keyword eksplisit
  • TF-IDF char n-gram (2-4)     → toleran typo
  • Cosine similarity            → skor kemiripan
    ↓
[Entity Extractor]
  • product_name  → fuzzy match vs nama produk DB
  • category      → synonym map
  • sort_by       → keyword sort
  • price_min/max → regex harga
    ↓
[Query Builder]
  → SQL SELECT ke MySQL
    ↓
[Formatter]
  → Output bersih & rapi
```

---

## 🔧 Kustomisasi

### Ubah confidence threshold
Di `config.py`:
```python
"confidence_threshold": 0.30,  # turunkan = lebih permisif
```

### Tambah sinonim kategori
Di `nlp/synonyms.py` → `CATEGORY_SYNONYMS`:
```python
"pastry": ["pastri", "croissant", "eclair", "kue lapis", "pie"],
```

### Tambah koreksi typo
Di `nlp/synonyms.py` → `TYPO_MAP`:
```python
"croisant": "croissant",
"donat": "donut",
```

---

## 💬 Contoh Pertanyaan

| Input User | Intent Terdeteksi |
|---|---|
| "halo" | greeting |
| "produk terlaris" | best_seller |
| "paling enak apa" | best_seller |
| "rekomendasiin dong" | best_seller |
| "yang murah apa" | cheapest |
| "harga di bawah 25rb" | price_range |
| "rating terbaik" | best_rating |
| "ada donat apa saja" | category_search |
| "detail croissant butter" | product_detail |
| "stok donat glazed ada ga" | stock_check |
| "bantuan" | help |
| "bye" | farewell |

---

## ⚠️ Catatan Penting

- AI hanya bisa membaca database (`SELECT` saja)
- Tidak ada koneksi ke API AI eksternal
- Model tersimpan di `data/model/` setelah training
- Wajib `python train.py` ulang setiap edit `intents.json`
- Pastikan user database MySQL hanya punya privilege `SELECT`

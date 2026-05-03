# nlp/synonyms.py — Kamus sinonim untuk chatbot bakery Indonesia (Enhanced)

# ── Kategori produk (expanded) ───────────────────────────────────────────────
CATEGORY_SYNONYMS: dict[str, list[str]] = {
    "donat":      ["donut", "donuts", "donat", "glazed", "donat glazed", "coklat", "cokelat", "bolang baling", "balon", "donat mini", "donat isi"],
    "pastry":     ["pastri", "croissant", "eclair", "pain", "kue lapis", "croissant butter", "almond croissant", "chocolate croissant", "danish", "puff pastry"],
    "roti-manis": ["roti manis", "sweet bread", "roti segar", "muffin", "cupcake", "brioche", "roti sobek", "melon pan", "anpan", "chocolate bread"],
    "roti-tawar": ["roti tawar", "roti putih", "white bread", "bagel", "baguette", "gandum", "pandan", "whole wheat", "multigrain", "oat bread"],
    "roti-gurih": ["roti gurih", "savory bread", "abon", "cream cheese", "garlic bread", "cheese stick", "roti jumbo", "roti isi", "sandwich bread"],
    "kue":        ["cake", "kue kering", "pastri manis", "cupcake", "brownies", "bolu", "sponge cake", "cheese cake", "tiramisu", "red velvet"],
    "lainnya":    ["other", "lain", "misc", "special", "custom"],
    "roti":       ["bread", "brioche", "baguette", "roti", "loaf"],
}

# ── Sort / filter keywords (enhanced) ────────────────────────────────────────
SORT_KEYWORDS: dict[str, list[str]] = {
    "price_asc":    ["termurah", "paling murah", "harga terendah", "murah dulu", "dari murah", "murah ke mahal", "ekonomis", "terjangkau", "hemat", "budget", "harga rendah", "urutkan murah", "low price first"],
    "price_desc":   ["termahal", "paling mahal", "harga tertinggi", "mahal dulu", "dari mahal", "mahal ke murah", "premium", "eksklusif", "harga tinggi", "urutkan mahal", "high price first"],
    "rating_desc":  ["rating terbaik", "rating tertinggi", "paling bagus", "terbagus", "kualitas terbaik", "nilai tertinggi", "bintang tertinggi", "rating tinggi", "paling disukai", "paling berkualitas", "top rated"],
    "stock_desc":    ["terlaris", "paling laris", "paling banyak dibeli", "best seller", "bestseller", "populer", "paling populer", "sering dibeli", "banyak dibeli", "favorit", "paling enak", "paling diminati", "rekomen", "top produk", "trending"],
}

# ── Intent keywords (expanded with Lumine/bakery terms) ───────────────────────
INTENT_KEYWORDS: dict[str, list[str]] = {
    "greeting":     ["halo", "hai", "hi", "hello", "hei", "selamat", "assalamualaikum", "pagi", "siang", "bro", "sist"],
    "farewell":     ["bye", "dadah", "sampai jumpa", "terima kasih", "makasih", "thanks", "keluar", "exit", "quit", "tks", "gas"],
    "help":         ["bantuan", "help", "bisa apa", "cara", "panduan", "fitur", "petunjuk", "menu"],
    "list":         ["semua", "daftar", "list", "katalog", "menu", "tampilkan", "lihat", "etalase"],
    "best_seller":  ["terlaris", "laris", "populer", "best seller", "favorit", "enak", "rekomen", "hits", "trending"],
    "cheapest":     ["murah", "termurah", "terjangkau", "budget", "hemat", "kantong tipis"],
    "expensive":    ["mahal", "termahal", "premium", "luxury", "eksklusif"],
    "rating":       ["rating", "bintang", "bagus", "kualitas", "review", "top rated"],
    "category":     ["kategori", "donat", "pastry", "roti", "kue", "croissant", "muffin", "bolu", "coklat"],
    "price_range":  ["harga", "berapa", "range", "di bawah", "di atas", "max", "min", "budget"],
    "detail":       ["detail", "info", "deskripsi", "bahan", "spesifikasi"],
    "stock":        ["stok", "ada", "ready", "habis", "sold out"],
    "recommendations": ["rekomendasi", "saran", "cocok", "pair", "suggest", "untuk anak", "healthy"],
    "new_arrivals": ["baru", "terbaru", "new", "fresh", "latest"],
    "product_pairing": ["pair", "padan", "cocok", "bundle", "kombinasi"],
    "promotions": ["promo", "diskon", "voucher", "b1g1", "deal"]
}

# ── Typo / informal corrections (expanded) ───────────────────────────────────
TYPO_MAP: dict[str, str] = {
    "brp": "berapa", "gmn": "gimana", "bs": "bisa", "gk": "tidak", "ga": "tidak", "gak": "tidak",
    "ngga": "tidak", "nggak": "tidak", "yg": "yang", "lg": "lagi", "lgi": "lagi", "udh": "sudah",
    "udah": "sudah", "klo": "kalau", "klu": "kalau", "kalo": "kalau",
    "ada ga": "ada tidak", "ada gak": "ada tidak", "donuts": "donat", "donut": "donat",
    "croisant": "croissant", "croissan": "croissant", "roti tawar": "roti-tawar",
    "roti manis": "roti-manis", "roti gurih": "roti-gurih", "paling enak": "terlaris",
    "paling bagus": "rating terbaik", "paling murah": "termurah", "paling mahal": "termahal",
    "coklat": "cokelat", "bolu": "kue", "kueh": "kue", "rotih": "roti", "pastrii": "pastry",
    "rekom": "rekomendasi", "sugest": "rekomendasi", "prono": "promo", "diskon": "promo"
}


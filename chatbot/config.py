# config.py — Konfigurasi utama chatbot (Enhanced)

# ── Database ───────────────────────────────────────────────────────────────────
DB_CONFIG = {
    "host":     "localhost",
    "port":     3306,
    "user":     "chatbot",
    "password": "onlyselect",
    "database": "web_bakery",
    "charset":  "utf8mb4",
}

# ── Chatbot (enhanced) ────────────────────────────────────────────────────────
CHATBOT_CONFIG = {
    "shop_name":            "Lumine Bakery",
    "model_path":           "data/model/",
    "confidence_threshold": 0.35,      # dinaikkan untuk akurasi lebih baik
    "max_results":          12,        # sedikit lebih banyak
    "currency_symbol":      "Rp",
    "promos_active":        True,      # enable promo logic
    "promos": [              # hardcoded Lumine promos (bisa dari DB nanti)
        {"title": "Beli 1 Get 1 Donat Mini", "description": "Setiap pembelian donat glazed", "discount": "50", "valid_until": "30/04"},
        {"title": "Diskon 20% Roti Manis", "description": "Min. beli 6 pcs", "discount": "20", "valid_until": "15/05"},
    ],
}

# ── Kolom tabel products ───────────────────────────────────────────────────────
PRODUCT_COLUMNS = {
    "id":          "id",
    "name":        "name",
    "category":    "category",
    "price":       "price",
    "image":       "image",
    "description": "description",
    "rating":      "rating",
    "stock":       "stock",
    "is_active":   "is_active",
    "created_at":  "created_at",  # untuk new arrivals
}


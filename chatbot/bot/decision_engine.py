#!/usr/bin/env python3
# bot/decision_engine.py — Core decision logic: intent → structured response

import random
from config import CHATBOT_CONFIG

SHOP = CHATBOT_CONFIG["shop_name"]


class DecisionEngine:
    """
    Map intent tag → structured response.
    Returns dict with type, data, reply, confidence.
    """

    def __init__(self, db_query_builder):
        self.qb = db_query_builder

    def handle_intent(self, tag: str, entities: dict, confidence: float = 1.0) -> dict:
        """
        Route to handler based on intent tag.
        Returns: {
            "type": "text | product_list | product_detail | recommendation",
            "data": [...],
            "reply": "...",
            "confidence": 0.0-1.0
        }
        """
        handlers = {
            "greeting": self._handle_greeting,
            "farewell": self._handle_farewell,
            "help": self._handle_help,
            "list_products": self._handle_list,
            "best_seller": self._handle_best_seller,
            "cheapest": self._handle_cheapest,
            "most_expensive": self._handle_expensive,
            "best_rating": self._handle_rating,
            "category_search": self._handle_category,
            "price_range": self._handle_price_range,
            "product_detail": self._handle_detail,
            "stock_check": self._handle_stock,
            "recommendations": self._handle_recommendations,
            "new_arrivals": self._handle_new_arrivals,
            "product_pairing": self._handle_pairing,
            "promotions": self._handle_promotions,
            "store_hours": self._handle_store_hours,
            "location": self._handle_location,
            "how_to_order": self._handle_how_to_order,
            "payment_methods": self._handle_payment,
            "custom_orders": self._handle_custom_orders,
            "dietary_halal": self._handle_dietary,
            "unknown": self._handle_unknown,
            "out_of_scope": self._handle_out_of_scope,
        }

        handler = handlers.get(tag, self._handle_unknown)
        return handler(entities, confidence)

    # ── TEXT RESPONSES ────────────────────────────────────────────────────────

    def _handle_greeting(self, entities: dict, conf: float) -> dict:
        replies = [
            f"Halo! Selamat datang di {SHOP}. Ada yang bisa saya bantu?",
            f"Hai! Saya siap membantu Anda menemukan roti & kue favorit! 😊",
            "Selamat datang! Silakan tanyakan produk, harga, atau rekomendasi kami.",
        ]
        return {
            "type": "text",
            "data": None,
            "reply": random.choice(replies),
            "confidence": conf,
        }

    def _handle_farewell(self, entities: dict, conf: float) -> dict:
        replies = [
            f"Terima kasih sudah berkunjung ke {SHOP}! Sampai jumpa 👋",
            "Sampai jumpa! Semoga hari Anda menyenangkan 😊",
            "Terima kasih! Jangan lupa mampir lagi ya 🍰",
        ]
        return {
            "type": "text",
            "data": None,
            "reply": random.choice(replies),
            "confidence": conf,
        }

    def _handle_help(self, entities: dict, conf: float) -> dict:
        help_text = f"""\
Halo! Saya asisten {SHOP}. 👋
Ini yang bisa saya bantu:

  Produk & Rekomendasi
    "semua produk"  ·  "terlaris"  ·  "rekomendasi murah"
    "produk baru"   ·  "pairing croissant"

  Harga & Filter
    "termurah"  ·  "rating terbaik"
    "harga di bawah 25rb"  ·  "range 15k–30k"

  Kategori
    "donat"  ·  "pastry"  ·  "roti manis"

  Info & Stok
    "info croissant almond"  ·  "stok ada ga"

  Lainnya
    "promo"  ·  "jam buka"  ·  "cara pesan"

Ketik 'keluar' kapan saja untuk berhenti. 😊"""
        return {
            "type": "text",
            "data": None,
            "reply": help_text,
            "confidence": conf,
        }

    def _handle_store_hours(self, entities: dict, conf: float) -> dict:
        reply = "Toko kami buka setiap hari mulai pukul 08:00 hingga 21:00 WIB. Ada yang ingin Anda pesan hari ini?"
        return {
            "type": "text",
            "data": None,
            "reply": reply,
            "confidence": conf,
        }

    def _handle_location(self, entities: dict, conf: float) -> dict:
        reply = f"{SHOP} berlokasi di Jl. Contoh Alamat No. 123. Anda bisa menemukan titik lokasi kami dengan mencari '{SHOP}' di Google Maps!"
        return {
            "type": "text",
            "data": None,
            "reply": reply,
            "confidence": conf,
        }

    def _handle_how_to_order(self, entities: dict, conf: float) -> dict:
        reply = f"Untuk pemesanan, Anda bisa langsung memberitahu saya pesanan Anda di chat ini, atau memesan via GoFood/GrabFood dengan mencari '{SHOP}'."
        return {
            "type": "text",
            "data": None,
            "reply": reply,
            "confidence": conf,
        }

    def _handle_payment(self, entities: dict, conf: float) -> dict:
        reply = "Kami menerima pembayaran melalui QRIS, Transfer Bank (BCA/Mandiri), e-Wallet (GoPay, OVO, Dana), dan uang tunai jika Anda datang langsung ke toko."
        return {
            "type": "text",
            "data": None,
            "reply": reply,
            "confidence": conf,
        }

    def _handle_custom_orders(self, entities: dict, conf: float) -> dict:
        reply = "Tentu! Kami melayani pemesanan kue custom untuk ulang tahun atau acara spesial. Harap lakukan pemesanan maksimal H-2. Anda ingin melihat katalog kue ulang tahun kami?"
        return {
            "type": "text",
            "data": None,
            "reply": reply,
            "confidence": conf,
        }

    def _handle_dietary(self, entities: dict, conf: float) -> dict:
        reply = f"Semua produk {SHOP} 100% Halal dan bebas dari alkohol atau rum. Kami menggunakan bahan-bahan berkualitas premium."
        return {
            "type": "text",
            "data": None,
            "reply": reply,
            "confidence": conf,
        }

    def _handle_unknown(self, entities: dict, conf: float) -> dict:
        replies = [
            "Hmm, saya kurang paham maksudnya. 🤔 Coba ketik 'bantuan' untuk lihat semua yang bisa ditanyakan.",
            "Maaf, bisa diperjelas sedikit? Sebutkan nama produk atau coba 'bantuan' untuk panduan lengkap.",
            "Saya belum mengerti pertanyaan Anda. Coba tanya tentang produk, harga, atau stok kami.",
        ]
        return {
            "type": "text",
            "data": None,
            "reply": random.choice(replies),
            "confidence": conf,
        }

    def _handle_out_of_scope(self, entities: dict, conf: float) -> dict:
        reply = f"Maaf, saya hanya bisa membantu seputar {SHOP} 😊"
        return {
            "type": "text",
            "data": None,
            "reply": reply,
            "confidence": 0.0,
        }

    # ── PRODUCT LIST RESPONSES ────────────────────────────────────────────────

    def _handle_list(self, entities: dict, conf: float) -> dict:
        rows = self.qb.list_all_products()
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Berikut adalah {len(rows)} produk aktif kami.",
            "confidence": conf,
        }

    def _handle_best_seller(self, entities: dict, conf: float) -> dict:
        category = entities.get("category")
        rows = self.qb.best_seller(category=category)
        count = len(rows)
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Ini {count} produk terlaris kami:" if count > 0 else "Belum ada data penjualan.",
            "confidence": conf,
        }

    def _handle_cheapest(self, entities: dict, conf: float) -> dict:
        category = entities.get("category")
        rows = self.qb.sort_by_price_asc(category=category)
        count = len(rows)
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Ini {count} pilihan paling ramah di kantong:" if count > 0 else "Tidak ada produk dalam kategori ini.",
            "confidence": conf,
        }

    def _handle_expensive(self, entities: dict, conf: float) -> dict:
        category = entities.get("category")
        rows = self.qb.sort_by_price_desc(category=category)
        count = len(rows)
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Ini {count} pilihan premium kami:" if count > 0 else "Tidak ada data.",
            "confidence": conf,
        }

    def _handle_rating(self, entities: dict, conf: float) -> dict:
        category = entities.get("category")
        rows = self.qb.sort_by_rating_desc(category=category)
        count = len(rows)
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Ini {count} produk dengan rating terbaik:" if count > 0 else "Tidak ada produk.",
            "confidence": conf,
        }

    def _handle_price_range(self, entities: dict, conf: float) -> dict:
        pmin = entities.get("price_min")
        pmax = entities.get("price_max")
        category = entities.get("category")

        if pmin is None and pmax is None:
            rows = self.qb.sort_by_price_asc(category=category)
            reply = "Berikut produk yang tersedia (dari harga terendah):"
        else:
            rows = self.qb.price_range(pmin, pmax, category=category)
            reply = f"Produk dengan harga antara filter Anda:"

        return {
            "type": "product_list",
            "data": rows,
            "reply": reply if rows else "Tidak ada produk yang sesuai dengan filter harga Anda.",
            "confidence": conf,
        }

    def _handle_category(self, entities: dict, conf: float) -> dict:
        category = entities.get("category")
        if not category:
            reply = "Kategori apa yang ingin Anda cari? (donat, pastry, roti manis, roti tawar, roti gurih, kue)"
            return {
                "type": "text",
                "data": None,
                "reply": reply,
                "confidence": conf,
            }

        rows = self.qb.by_category(category)
        count = len(rows)
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Ketemu {count} produk dalam kategori {category}:" if count > 0 else f"Tidak ada produk dalam kategori {category}.",
            "confidence": conf,
        }

    def _handle_recommendations(self, entities: dict, conf: float) -> dict:
        category = entities.get("category")
        rows = self.qb.recommendations(category)
        count = len(rows)
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Kalau boleh saran, ini {count} produk yang kami rekomendasikan untuk Anda ✨" if count > 0 else "Belum ada rekomendasi saat ini.",
            "confidence": conf,
        }

    def _handle_new_arrivals(self, entities: dict, conf: float) -> dict:
        category = entities.get("category")
        rows = self.qb.new_arrivals(category)
        count = len(rows)
        return {
            "type": "product_list",
            "data": rows,
            "reply": f"Baru masuk {count} produk yang sayang dilewatkan:" if count > 0 else "Belum ada produk baru saat ini.",
            "confidence": conf,
        }

    # ── PRODUCT DETAIL RESPONSE ────────────────────────────────────────────────

    def _handle_detail(self, entities: dict, conf: float) -> dict:
        product_name = entities.get("product_name")
        if not product_name:
            reply = "Nama produk apa yang ingin Anda ketahui? Contoh: 'croissant' atau 'donat glazed'"
            return {
                "type": "text",
                "data": None,
                "reply": reply,
                "confidence": conf,
            }

        rows = self.qb.product_detail(product_name)
        return {
            "type": "product_detail",
            "data": rows,
            "reply": f"Ini detail produk {product_name}:" if rows else f"Produk '{product_name}' tidak ditemukan.",
            "confidence": conf,
        }

    def _handle_stock(self, entities: dict, conf: float) -> dict:
        product_name = entities.get("product_name")
        if not product_name:
            reply = "Stok produk apa yang ingin Anda cek? Contoh: 'stok croissant' atau 'ada donat?'"
            return {
                "type": "text",
                "data": None,
                "reply": reply,
                "confidence": conf,
            }

        rows = self.qb.check_stock(product_name)
        return {
            "type": "product_detail",
            "data": rows,
            "reply": f"Status stok '{product_name}':" if rows else f"Produk '{product_name}' tidak ditemukan.",
            "confidence": conf,
        }

    def _handle_pairing(self, entities: dict, conf: float) -> dict:
        product_name = entities.get("product_name")
        category = entities.get("category", "roti")

        pairs = [
            {"pair_name": "Kopi", "pair_price": 10000, "reason": "Klasik & pas"},
            {"pair_name": "Teh", "pair_price": 8000, "reason": "Menyegarkan"},
        ]

        try:
            complements = self.qb.complementary_products(category)
            for c in complements[:3]:
                pairs.append({
                    "pair_name": c.get("name", "-"),
                    "pair_price": c.get("price", 0) + 15000,
                    "reason": f"Komplemen {c.get('category', '')}"
                })
        except Exception:
            pass

        product = product_name or category
        return {
            "type": "recommendation",
            "data": pairs[:3],
            "reply": f"'{product}' enak banget kalau dipadukan dengan...",
            "confidence": conf,
        }

    def _handle_promotions(self, entities: dict, conf: float) -> dict:
        promos = CHATBOT_CONFIG.get("promos", [])
        if CHATBOT_CONFIG.get("promos_active") and promos:
            return {
                "type": "text",
                "data": promos,
                "reply": "Promo yang lagi jalan nih 🎉",
                "confidence": conf,
            }

        return {
            "type": "text",
            "data": None,
            "reply": "Belum ada promo aktif saat ini. Tapi produk kami tetap worth it, kok! 😊",
            "confidence": conf,
        }

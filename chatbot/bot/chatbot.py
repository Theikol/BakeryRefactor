#!/usr/bin/env python3
# bot/chatbot.py - Engine utama chatbot bakery (Enhanced Lumine)

import random
from config import CHATBOT_CONFIG
from nlp.preprocessor import preprocess
from nlp.classifier import IntentClassifier
from nlp.entity_extractor import EntityExtractor
from db.connection import DatabaseConnection
from db.query_builder import QueryBuilder
from response import formatter as fmt
from nlp.synonyms import CATEGORY_SYNONYMS

THRESHOLD = CHATBOT_CONFIG["confidence_threshold"]
SHOP = CHATBOT_CONFIG["shop_name"]

class BakeryChatbot:
    def __init__(self):
        self.classifier = IntentClassifier(model_path=CHATBOT_CONFIG["model_path"])
        self.db = DatabaseConnection()
        self.qb = None
        self.ext = EntityExtractor()
        self._product_names = []

    def start(self):
        self.db.connect()
        self.qb = QueryBuilder(self.db)
        try:
            self.classifier.load()
        except RuntimeError:
            print("[Bot] Model belum ada, melatih otomatis...")
            self.classifier.train()
        self._refresh_product_names()
        print(f"\n[Bot] {SHOP} siap! (Enhanced - smarter Lumine bot)\n")

    def stop(self):
        self.db.disconnect()

    def _refresh_product_names(self):
        self._product_names = self.qb.all_product_names()
        self.ext.update_products(self._product_names)

    def chat(self, user_input):
        proc = preprocess(user_input, known_words=self._product_names)
        clean = proc["clean"]
        joined = proc["joined"]

        tag, score = self.classifier.predict(clean)
        entities = self.ext.extract(clean)

        return self._route(tag, score, entities, joined)

    def _route(self, tag, score, entities, raw_clean):
        if score < THRESHOLD and tag not in ("greeting", "farewell", "help"):
            return fmt.low_confidence_response(tag, score) if score > 0.2 else fmt.unknown_response()

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
        }

        handler = handlers.get(tag)
        if handler:
            return handler(entities)

        return self._handle_fallback(raw_clean)

    def _handle_fallback(self, query):
        return random.choice([
            fmt.table_products(self.qb.recommendations(), "Rekomendasi Cepat"),
            fmt.table_products(self.qb.best_seller(), "Populer"),
            fmt.help_message()
        ])

    # Original Handlers (full)
    def _handle_greeting(self, _):
        responses = [
            f"Halo! Selamat datang di {SHOP} . Ada yang bisa saya bantu?",
            f"Hai! Saya siap membantu produk favorit di {SHOP} ",
            f"Woy! Selamat datang di {SHOP} , tanya-tanya aja ya!",
            "Halo! Tanyakan produk, harga, rekomendasi!"
        ]
        return random.choice(responses)

    def _handle_farewell(self, _):
        responses = [
            f"Terima kasih! Sampai jumpa {SHOP}!",
            "Sampai jumpa! Selamat hari ",
            "Terima kasih! Mampir lagi ya "
        ]
        return random.choice(responses)

    def _handle_help(self, _):
        return fmt.help_message()

    def _handle_list(self, _):
        rows = self.qb.list_all_products()
        return fmt.table_products(rows, "Semua Produk Aktif")

    def _handle_best_seller(self, entities):
        rows = self.qb.best_seller(category=entities.get("category"))
        title = "Produk Terlaris"
        if entities.get("category"):
            title += f" - {entities['category'].title()}"
        return fmt.table_products(rows, title)

    def _handle_cheapest(self, entities):
        rows = self.qb.sort_by_price_asc(category=entities.get("category"))
        return fmt.table_products(rows, "Termurah -> Termahal")

    def _handle_expensive(self, entities):
        rows = self.qb.sort_by_price_desc(category=entities.get("category"))
        return fmt.table_products(rows, "Termahal -> Termurah")

    def _handle_rating(self, entities):
        rows = self.qb.sort_by_rating_desc(category=entities.get("category"))
        return fmt.table_products(rows, "Rating Terbaik")

    def _handle_category(self, entities):
        cat = entities.get("category")
        if not cat:
            return (
                "Kategori: " + ', '.join(CATEGORY_SYNONYMS.keys()) + 
                "\\nContoh: 'pastry' atau 'donat'"
            )
        rows = self.qb.by_category(cat)
        return fmt.table_products(rows, f"Kategori {cat.title()}")

    def _handle_price_range(self, entities):
        pmin = entities.get("price_min")
        pmax = entities.get("price_max")
        if pmin is None and pmax is None:
            rows = self.qb.sort_by_price_asc()
            return fmt.table_products(rows, "Semua (Terendah)")
        rows = self.qb.price_range(pmin, pmax, entities.get("category"))
        label_min = fmt.fmt_price(pmin) if pmin else "0"
        label_max = fmt.fmt_price(pmax) if pmax else "oo"
        return fmt.table_products(rows, f"Rp {label_min} - {label_max}")

    def _handle_detail(self, entities):
        pname = entities.get("product_name")
        if not pname:
            return "Nama produk? Contoh 'croissant' or 'donat'"
        rows = self.qb.product_detail(pname)
        return fmt.detail_product(rows)

    def _handle_stock(self, entities):
        pname = entities.get("product_name")
        if not pname:
            return random.choice([
                "Nama produk? 'stok croissant?'",
                "Produk mana? 'donat ada?'"
            ])
        rows = self.qb.check_stock(pname)
        return fmt.stock_result(rows)

    # New Handlers
    def _handle_recommendations(self, entities):
        cat = entities.get("category")
        rows = self.qb.recommendations(cat)
        why = "Rating tinggi + harga terjangkau" if not cat else f"untuk {cat}"
        for r in rows:
            r['why_recommended'] = why
        return fmt.recommendations_table(rows)

    def _handle_new_arrivals(self, entities):
        cat = entities.get("category")
        rows = self.qb.new_arrivals(cat)
        return fmt.new_arrivals_table(rows)

    def _handle_pairing(self, entities):
        pname = entities.get("product_name")
        cat = entities.get("category", "roti")
        pairs = [
            {"pair_name": "Kopi", "pair_price": 10000, "reason": "Klasik"},
            {"pair_name": "Teh", "pair_price": 8000, "reason": "Balance"}
        ]
        complements = self.qb.complementary_products(cat)
        for c in complements:
            pairs.append({
                "pair_name": c['name'],
                "pair_price": c['price'] + 15000,
                "reason": f"Komplement {c['category']}"
            })
        product = pname or cat
        return fmt.pairing_suggestion(product, pairs)

    def _handle_promotions(self, entities):
        promos = CHATBOT_CONFIG.get('promos', [])
        if CHATBOT_CONFIG.get('promos_active'):
            return fmt.promotions_list(promos)
        return "Promo off. Cek web!"

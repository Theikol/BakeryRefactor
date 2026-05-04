# nlp/classifier.py — Klasifikasi intent dengan TF-IDF + Cosine Similarity (REFACTORED)

import os
import json
import pickle
import re
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

from nlp.synonyms import INTENT_KEYWORDS


class IntentClassifier:
    """
    Klasifikasi intent dengan confidence threshold & out-of-scope detection.
    - TF-IDF + cosine similarity
    - Keyword shortcut untuk presisi
    - Domain detection untuk bakery queries
    """

    def __init__(self, model_path: str = "data/model/", confidence_threshold: float = 0.55):
        self.model_path = model_path
        self.confidence_threshold = confidence_threshold
        self.vectorizer: TfidfVectorizer | None = None
        self.X_train = None
        self.train_tags: list[str] = []
        self.intents_data: dict = {}
        self._keyword_weights = {tag: kws for tag, kws in INTENT_KEYWORDS.items()}
        self._bakery_domain_keywords = self._build_domain_keywords()

    def _build_domain_keywords(self) -> set[str]:
        """Kata kunci domain bakery untuk deteksi out-of-scope."""
        bakery_terms = {
            "roti", "donat", "kue", "pastry", "croissant", "kopi", "teh",
            "harga", "stok", "promo", "diskon", "order", "pesan", "beli",
            "bakery", "lumineè", "lumine", "toko", "produk", "kategori",
            "rating", "review", "delivery", "gofood", "grabfood",
            "buka", "tutup", "jam", "lokasi", "alamat", "custom",
        }
        return bakery_terms

    def train(self, intents_file: str = "data/intents.json") -> None:
        """Latih model dari intents.json."""
        with open(intents_file, "r", encoding="utf-8") as f:
            data = json.load(f)

        self.intents_data = data
        patterns: list[str] = []
        tags: list[str] = []

        for intent in data["intents"]:
            for pattern in intent["patterns"]:
                patterns.append(pattern.lower())
                tags.append(intent["tag"])

        self.vectorizer = TfidfVectorizer(
            analyzer="char_wb",
            ngram_range=(2, 4),
            min_df=1,
            sublinear_tf=True,
        )
        self.X_train = self.vectorizer.fit_transform(patterns)
        self.train_tags = tags

        os.makedirs(self.model_path, exist_ok=True)
        self._save()
        print(f"[Classifier] Model dilatih: {len(patterns)} pola | {len(set(tags))} intent")

    def predict(self, text: str) -> tuple[str, float]:
        """
        Kembalikan (tag, confidence).
        - Keyword shortcut dulu (presisi tinggi)
        - Jika tidak ada → TF-IDF
        - Jika confidence < threshold → "unknown"
        - Jika out-of-scope → "out_of_scope"
        """
        if self.vectorizer is None:
            self.load()

        text_lower = text.lower()

        # 1) Quick domain check: apakah query tentang bakery?
        if self._is_out_of_scope(text_lower):
            return "out_of_scope", 0.0

        # 2) Keyword shortcut
        kw_tag = self._keyword_shortcut(text_lower)
        if kw_tag:
            return kw_tag, 1.0

        # 3) TF-IDF cosine similarity
        vec = self.vectorizer.transform([text_lower])
        sims = cosine_similarity(vec, self.X_train).flatten()
        best_idx = int(np.argmax(sims))
        best_score = float(sims[best_idx])
        best_tag = self.train_tags[best_idx]

        # 4) Apply threshold
        if best_score < self.confidence_threshold:
            return "unknown", best_score

        return best_tag, best_score

    def _is_out_of_scope(self, text: str) -> bool:
        """Deteksi query out-of-scope (non-bakery)."""
        out_of_scope_patterns = [
            r"siapa (soekarno|soeharto|presiden|orang|dia|anda)",
            r"(berapa|apa) (ibukota|negara|populasi|jumlah)",
            r"(belajar|belajaran|kerjain|latihan|tugas|pr|pekerjaan rumah)",
            r"(resep|cara membuat|tata cara|tutorial) (tanpa|bukan).*(roti|kue|pastry|donat)",
            r"(cuaca|hari ini|besok|sekarang)",
            r"(musik|lagu|film|game|olahraga|sepak bola|bola)",
            r"(politics|politis|pemilu|partai|calon)",
        ]

        for pattern in out_of_scope_patterns:
            if re.search(pattern, text, re.IGNORECASE):
                # Tapi kalau ada kata bakery → tetap in-scope
                if not any(kw in text for kw in ["roti", "donat", "kue", "pastry", "bakery"]):
                    return True

        # Cek: minimal ada 1 kata dari domain
        words = text.split()
        domain_match = sum(1 for w in words if w in self._bakery_domain_keywords)
        if len(words) > 5 and domain_match == 0:
            return True

        return False

    def _keyword_shortcut(self, text: str) -> str | None:
        """
        Keyword matching dengan word boundary (hindari substring false positives).
        Prioritas: greeting/farewell dulu, baru yang lain.
        """
        priority_order = [
            "greeting", "farewell", "help", "detail", "stock",
            "best_seller", "cheapest", "expensive", "rating",
            "price_range", "list", "category",
        ]
        sorted_items = sorted(
            self._keyword_weights.items(),
            key=lambda x: priority_order.index(x[0]) if x[0] in priority_order else 99
        )

        for tag, keywords in sorted_items:
            for kw in keywords:
                # Word boundary check: hindari "roti" match di "fateroti"
                if re.search(r"\b" + re.escape(kw) + r"\b", text):
                    mapping = {
                        "greeting":    "greeting",
                        "farewell":    "farewell",
                        "help":        "help",
                        "list":        "list_products",
                        "best_seller": "best_seller",
                        "cheapest":    "cheapest",
                        "expensive":   "most_expensive",
                        "rating":      "best_rating",
                        "category":    "category_search",
                        "price_range": "price_range",
                        "detail":      "product_detail",
                        "stock":       "stock_check",
                        "recommendations": "recommendations",
                        "new_arrivals": "new_arrivals",
                        "product_pairing": "product_pairing",
                        "promotions": "promotions",
                    }
                    return mapping.get(tag)
        return None

    def get_response_templates(self, tag: str) -> list[str]:
        """Ambil daftar template respons untuk sebuah tag."""
        for intent in self.intents_data.get("intents", []):
            if intent["tag"] == tag:
                return intent.get("responses", [])
        return []

    def _save(self) -> None:
        with open(os.path.join(self.model_path, "vectorizer.pkl"), "wb") as f:
            pickle.dump(self.vectorizer, f)
        with open(os.path.join(self.model_path, "X_train.pkl"), "wb") as f:
            pickle.dump(self.X_train, f)
        with open(os.path.join(self.model_path, "tags.pkl"), "wb") as f:
            pickle.dump(self.train_tags, f)
        with open(os.path.join(self.model_path, "intents_data.pkl"), "wb") as f:
            pickle.dump(self.intents_data, f)
        print(f"[Classifier] Model disimpan ke '{self.model_path}'")

    def load(self) -> None:
        try:
            with open(os.path.join(self.model_path, "vectorizer.pkl"), "rb") as f:
                self.vectorizer = pickle.load(f)
            with open(os.path.join(self.model_path, "X_train.pkl"), "rb") as f:
                self.X_train = pickle.load(f)
            with open(os.path.join(self.model_path, "tags.pkl"), "rb") as f:
                self.train_tags = pickle.load(f)
            with open(os.path.join(self.model_path, "intents_data.pkl"), "rb") as f:
                self.intents_data = pickle.load(f)
        except FileNotFoundError:
            raise RuntimeError("Model belum dilatih! Jalankan: python train.py")

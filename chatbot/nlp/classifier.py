# nlp/classifier.py — Klasifikasi intent dengan TF-IDF + Cosine Similarity

import os
import json
import pickle
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

from nlp.synonyms import INTENT_KEYWORDS


class IntentClassifier:
    """
    Klasifikasi intent berbasis TF-IDF + cosine similarity.
    Bisa di-retrain kapan saja hanya dari intents.json.
    """

    def __init__(self, model_path: str = "data/model/"):
        self.model_path = model_path
        self.vectorizer: TfidfVectorizer | None = None
        self.X_train = None          # matrix TF-IDF training
        self.train_tags: list[str] = []
        self.intents_data: dict = {}
        self._keyword_weights = {tag: kws for tag, kws in INTENT_KEYWORDS.items()}

    # ── Training ──────────────────────────────────────────────────────────────

    def train(self, intents_file: str = "data/intents.json") -> None:
        """Latih model dari file intents.json dan simpan ke disk."""
        with open(intents_file, "r", encoding="utf-8") as f:
            data = json.load(f)

        self.intents_data = data
        patterns: list[str] = []
        tags: list[str] = []

        for intent in data["intents"]:
            for pattern in intent["patterns"]:
                patterns.append(pattern.lower())
                tags.append(intent["tag"])

        # TF-IDF dengan karakter n-gram (lebih toleran typo)
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

    # ── Prediksi ──────────────────────────────────────────────────────────────

    def predict(self, text: str) -> tuple[str, float]:
        """
        Kembalikan (tag, confidence).
        Jika confidence < threshold → 'unknown'.
        """
        if self.vectorizer is None:
            self.load()

        text_lower = text.lower()

        # 1) Keyword shortcut (cepat & akurat untuk kata kunci eksplisit)
        kw_tag = self._keyword_shortcut(text_lower)
        if kw_tag:
            return kw_tag, 1.0

        # 2) TF-IDF cosine similarity
        vec = self.vectorizer.transform([text_lower])
        sims = cosine_similarity(vec, self.X_train).flatten()
        best_idx = int(np.argmax(sims))
        best_score = float(sims[best_idx])
        best_tag = self.train_tags[best_idx]

        return best_tag, best_score

    def _keyword_shortcut(self, text: str) -> str | None:
        """
        Cepat: jika teks mengandung keyword eksplisit → langsung return tag.
        Prioritas berdasarkan urutan INTENT_KEYWORDS.
        """
        # Prioritas: detail & stock dulu, baru category (agar "info donat" → detail bukan category)
        priority_order = ["greeting", "farewell", "help", "detail", "stock",
                          "best_seller", "cheapest", "expensive", "rating",
                          "price_range", "list", "category"]
        sorted_items = sorted(
            self._keyword_weights.items(),
            key=lambda x: priority_order.index(x[0]) if x[0] in priority_order else 99
        )
        for tag, keywords in sorted_items:
            for kw in keywords:
                if kw in text:
                    # Map keyword-group ke intent tag
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
                    }
                    return mapping.get(tag)
        return None

    def get_response_templates(self, tag: str) -> list[str]:
        """Ambil daftar template respons untuk sebuah tag."""
        for intent in self.intents_data.get("intents", []):
            if intent["tag"] == tag:
                return intent["responses"]
        return []

    # ── Persistensi model ─────────────────────────────────────────────────────

    def _save(self) -> None:
        with open(os.path.join(self.model_path, "vectorizer.pkl"), "wb") as f:
            pickle.dump(self.vectorizer, f)
        with open(os.path.join(self.model_path, "X_train.pkl"), "wb") as f:
            pickle.dump(self.X_train, f)
        with open(os.path.join(self.model_path, "tags.pkl"), "wb") as f:
            pickle.dump(self.train_tags, f)
        # simpan juga intents_data supaya respons tersedia
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
            raise RuntimeError(
                "Model belum dilatih! Jalankan: python train.py"
            )

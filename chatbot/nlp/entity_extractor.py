# nlp/entity_extractor.py — Ekstrak entitas dari teks input

import re
import difflib
from nlp.synonyms import CATEGORY_SYNONYMS, SORT_KEYWORDS


class EntityExtractor:
    """
    Ekstrak entitas dari teks:
      - product_name  : nama produk (fuzzy match vs DB)
      - category      : kategori produk
      - sort_by       : price_asc | price_desc | rating_desc | stock_desc
      - price_min     : batas harga bawah
      - price_max     : batas harga atas
    """

    def __init__(self, product_names: list[str] | None = None):
        """
        product_names: list nama produk dari DB, untuk fuzzy matching.
        """
        self.product_names: list[str] = [p.lower() for p in (product_names or [])]

    def update_products(self, product_names: list[str]) -> None:
        self.product_names = [p.lower() for p in product_names]

    # ── Publik ────────────────────────────────────────────────────────────────

    def extract(self, text: str) -> dict:
        text_lower = text.lower()
        return {
            "product_name": self._extract_product(text_lower),
            "category":     self._extract_category(text_lower),
            "sort_by":      self._extract_sort(text_lower),
            "price_min":    self._extract_price_min(text_lower),
            "price_max":    self._extract_price_max(text_lower),
        }

    # ── Private ───────────────────────────────────────────────────────────────

    def _extract_product(self, text: str) -> str | None:
        """Fuzzy match nama produk terhadap daftar produk di DB."""
        if not self.product_names:
            return None

        # Coba exact match dulu
        for name in self.product_names:
            if name in text:
                return name

        # Fuzzy: coba tiap token & bigram
        tokens = text.split()
        candidates = tokens + [" ".join(tokens[i:i+2]) for i in range(len(tokens)-1)]
        candidates += [" ".join(tokens[i:i+3]) for i in range(len(tokens)-2)]

        best_match = None
        best_score = 0.0
        for candidate in candidates:
            matches = difflib.get_close_matches(candidate, self.product_names, n=1, cutoff=0.72)
            if matches:
                score = difflib.SequenceMatcher(None, candidate, matches[0]).ratio()
                if score > best_score:
                    best_score = score
                    best_match = matches[0]

        return best_match if best_score >= 0.72 else None

    def _extract_category(self, text: str) -> str | None:
        """Cocokkan dengan CATEGORY_SYNONYMS."""
        # Cek sinonim terpanjang dulu
        all_syns: list[tuple[str, str]] = []
        for cat, syns in CATEGORY_SYNONYMS.items():
            for syn in syns:
                all_syns.append((syn, cat))
        all_syns.sort(key=lambda x: -len(x[0]))

        for syn, cat in all_syns:
            if syn in text:
                return cat

        # Cek nama kategori langsung
        for cat in CATEGORY_SYNONYMS:
            if cat.replace("-", " ") in text or cat in text:
                return cat

        return None

    def _extract_sort(self, text: str) -> str | None:
        """Deteksi sorting: price_asc | price_desc | rating_desc | stock_desc."""
        for sort_key, phrases in SORT_KEYWORDS.items():
            for phrase in phrases:
                if phrase in text:
                    return sort_key
        return None

    def _extract_price_min(self, text: str) -> int | None:
        """
        Ekstrak harga minimum dari pola seperti:
        'di atas 20000', 'lebih dari 25rb', 'minimum 15000'
        """
        patterns = [
            r"(?:di atas|lebih dari|min(?:imum)?|mulai dari|>\s*)\s*(\d[\d\.]*)\s*(?:rb|ribu|k)?",
        ]
        for pat in patterns:
            m = re.search(pat, text)
            if m:
                return self._parse_price(m.group(1), text[m.end():m.end()+3])
        return None

    def _extract_price_max(self, text: str) -> int | None:
        """
        Ekstrak harga maksimum dari pola seperti:
        'di bawah 30000', 'kurang dari 25rb', 'maximum 20000'
        """
        patterns = [
            r"(?:di bawah|kurang dari|max(?:imum)?|sampai|hingga|<\s*)\s*(\d[\d\.]*)\s*(?:rb|ribu|k)?",
        ]
        for pat in patterns:
            m = re.search(pat, text)
            if m:
                return self._parse_price(m.group(1), text[m.end():m.end()+3])
        return None

    @staticmethod
    def _parse_price(num_str: str, suffix: str) -> int:
        """Konversi string angka + suffix ('rb','k','ribu') ke integer."""
        num = int(num_str.replace(".", ""))
        suffix = suffix.strip().lower()
        if suffix in ("rb", "k", "ribu"):
            num *= 1000
        return num

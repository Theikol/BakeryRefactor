# nlp/entity_extractor.py — Ekstrak entitas dari teks input (ENHANCED)

import re
import difflib
from nlp.synonyms import CATEGORY_SYNONYMS, SORT_KEYWORDS


class EntityExtractor:
    """
    Ekstrak entitas dari teks dengan akurasi tinggi:
      - product_name  : nama produk (fuzzy match vs DB)
      - category      : kategori produk
      - sort_by       : price_asc | price_desc | rating_desc | stock_desc
      - price_min     : batas harga bawah
      - price_max     : batas harga atas
    """

    def __init__(self, product_names: list[str] | None = None):
        self.product_names: list[str] = [p.lower() for p in (product_names or [])]

    def update_products(self, product_names: list[str]) -> None:
        self.product_names = [p.lower() for p in product_names]

    def extract(self, text: str) -> dict:
        """Extract all entities dari teks."""
        text_lower = text.lower()
        return {
            "product_name": self._extract_product(text_lower),
            "category":     self._extract_category(text_lower),
            "sort_by":      self._extract_sort(text_lower),
            "price_min":    self._extract_price_min(text_lower),
            "price_max":    self._extract_price_max(text_lower),
        }

    def _extract_product(self, text: str) -> str | None:
        """Fuzzy match nama produk terhadap daftar produk di DB."""
        if not self.product_names:
            return None

        # Exact match first
        for name in self.product_names:
            if re.search(r"\b" + re.escape(name) + r"\b", text):
                return name

        # Fuzzy: token + bigram + trigram candidates
        tokens = text.split()
        if len(tokens) == 0:
            return None

        candidates = tokens + [
            " ".join(tokens[i:i+2]) for i in range(max(0, len(tokens)-1))
        ] + [
            " ".join(tokens[i:i+3]) for i in range(max(0, len(tokens)-2))
        ]

        best_match = None
        best_score = 0.0

        for candidate in candidates:
            if len(candidate) < 2:
                continue
            matches = difflib.get_close_matches(candidate, self.product_names, n=1, cutoff=0.70)
            if matches:
                score = difflib.SequenceMatcher(None, candidate, matches[0]).ratio()
                if score > best_score:
                    best_score = score
                    best_match = matches[0]

        return best_match if best_score >= 0.70 else None

    def _extract_category(self, text: str) -> str | None:
        """Extract kategori dengan sinonim terpanjang dulu."""
        all_syns: list[tuple[str, str]] = []
        for cat, syns in CATEGORY_SYNONYMS.items():
            for syn in syns:
                all_syns.append((syn, cat))

        all_syns.sort(key=lambda x: -len(x[0]))

        for syn, cat in all_syns:
            if re.search(r"\b" + re.escape(syn) + r"\b", text):
                return cat

        return None

    def _extract_sort(self, text: str) -> str | None:
        """Deteksi sort order dari keywords."""
        for sort_key, phrases in SORT_KEYWORDS.items():
            for phrase in phrases:
                if phrase in text:
                    return sort_key
        return None

    def _extract_price_min(self, text: str) -> int | None:
        """
        Extract minimum price.
        Patterns: 'di atas 20000', 'lebih dari 25rb', 'minimum 15000'
        """
        patterns = [
            r"(?:di\s+atas|lebih\s+dari|min(?:imum)?|mulai\s+dari|>\s*)\s*(\d[\d\.]*)\s*(?:rb|ribu|k)?",
        ]
        for pat in patterns:
            m = re.search(pat, text)
            if m:
                try:
                    return self._parse_price(m.group(1), text[m.end():m.end()+3])
                except (ValueError, IndexError):
                    continue
        return None

    def _extract_price_max(self, text: str) -> int | None:
        """
        Extract maximum price.
        Patterns: 'di bawah 30000', 'kurang dari 25rb', 'maximum 20000'
        """
        patterns = [
            r"(?:di\s+bawah|kurang\s+dari|max(?:imum)?|sampai|hingga|<\s*)\s*(\d[\d\.]*)\s*(?:rb|ribu|k)?",
        ]
        for pat in patterns:
            m = re.search(pat, text)
            if m:
                try:
                    return self._parse_price(m.group(1), text[m.end():m.end()+3])
                except (ValueError, IndexError):
                    continue
        return None

    @staticmethod
    def _parse_price(num_str: str, suffix: str = "") -> int:
        """Parse harga: '25.000' atau '25rb' → 25000."""
        try:
            num = int(num_str.replace(".", "").replace(",", ""))
            suffix = suffix.strip().lower()
            if suffix in ("rb", "k", "ribu"):
                num *= 1000
            return max(0, num)
        except (ValueError, TypeError):
            return None

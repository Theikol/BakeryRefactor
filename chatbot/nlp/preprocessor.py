# nlp/preprocessor.py — Pembersihan & normalisasi teks input

import re
import difflib
from nlp.synonyms import TYPO_MAP

# Stopwords dasar Indonesia
STOPWORDS = {
    "yang", "dan", "di", "ke", "dari", "dengan", "untuk", "ada",
    "adalah", "ini", "itu", "atau", "juga", "sudah", "saya", "kami",
    "kita", "mereka", "dia", "kamu", "anda", "nya", "lah", "pun",
    "nih", "sih", "dong", "deh", "kan", "ya", "dulu", "mau", "ingin",
    "pengen", "please", "tolong", "minta", "coba", "lihat", "tampilkan",
    "kasih", "tau", "tahu", "bantu", "bisa", "kalau", "gimana",
}


def normalize(text: str) -> str:
    """Lowercase, hapus karakter aneh, normalisasi spasi."""
    text = text.lower().strip()
    text = re.sub(r"[^\w\s]", " ", text)   # hapus tanda baca
    text = re.sub(r"\s+", " ", text)        # normalisasi spasi
    return text


def apply_typo_map(text: str) -> str:
    """Koreksi typo & singkatan informal dari TYPO_MAP (terpanjang dulu)."""
    # Urutkan dari phrase terpanjang supaya 'paling murah' dikoreksi sebelum 'murah'
    for wrong, correct in sorted(TYPO_MAP.items(), key=lambda x: -len(x[0])):
        text = re.sub(r"\b" + re.escape(wrong) + r"\b", correct, text)
    return text


def fuzzy_correct_word(word: str, vocab: list[str], cutoff: float = 0.80) -> str:
    """
    Koreksi satu kata dengan fuzzy matching terhadap vocab.
    Hanya mengganti kalau kemiripan >= cutoff.
    """
    if word in vocab:
        return word
    matches = difflib.get_close_matches(word, vocab, n=1, cutoff=cutoff)
    return matches[0] if matches else word


def remove_stopwords(tokens: list[str]) -> list[str]:
    return [t for t in tokens if t not in STOPWORDS]


def tokenize(text: str) -> list[str]:
    return text.split()


def preprocess(text: str, known_words: list[str] | None = None) -> dict:
    """
    Pipeline lengkap preprocessing.

    Returns dict:
        raw       : teks asli
        clean     : setelah normalize + typo map
        tokens    : tokenized (dengan stopwords)
        tokens_sw : tokenized (tanpa stopwords)
    """
    raw = text
    clean = normalize(text)
    clean = apply_typo_map(clean)

    tokens = tokenize(clean)

    # Optional: fuzzy koreksi tiap kata terhadap vocab produk
    if known_words:
        tokens = [fuzzy_correct_word(t, known_words, cutoff=0.82) for t in tokens]

    tokens_sw = remove_stopwords(tokens)

    return {
        "raw":       raw,
        "clean":     clean,
        "tokens":    tokens,
        "tokens_sw": tokens_sw,
        "joined":    " ".join(tokens_sw),
    }

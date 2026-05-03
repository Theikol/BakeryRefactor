#!/usr/bin/env python3
"""
train.py — Latih ulang model NLP chatbot dari intents.json

Jalankan setiap kali Anda menambah pola baru di data/intents.json:
    python train.py
"""

import sys
import os

# Pastikan import dari root project
sys.path.insert(0, os.path.dirname(__file__))

from nlp.classifier import IntentClassifier
from config import CHATBOT_CONFIG


def main():
    print("=" * 50)
    print("  BAKERY CHATBOT — Training NLP Model")
    print("=" * 50)

    intents_file = "data/intents.json"
    model_path   = CHATBOT_CONFIG["model_path"]

    if not os.path.exists(intents_file):
        print(f"[ERROR] File tidak ditemukan: {intents_file}")
        sys.exit(1)

    clf = IntentClassifier(model_path=model_path)
    clf.train(intents_file=intents_file)

    # Validasi quick test
    print("\n─── Quick Test ───")
    test_cases = [
        ("halo",                  "greeting"),
        ("produk terlaris",       "best_seller"),
        ("paling murah berapa",   "cheapest"),
        ("rating terbaik",        "best_rating"),
        ("rekomendasiin dong",    "best_seller"),
        ("harga di bawah 25000",  "price_range"),
        ("info croissant",        "product_detail"),
        ("stok donat ada ga",     "stock_check"),
        ("bantuan",               "help"),
        ("bye",                   "farewell"),
    ]

    correct = 0
    for text, expected in test_cases:
        tag, score = clf.predict(text)
        ok = "✅" if tag == expected else "❌"
        print(f"  {ok}  '{text}' → {tag} ({score:.2f}) [expected: {expected}]")
        if tag == expected:
            correct += 1

    accuracy = correct / len(test_cases) * 100
    print(f"\n  Akurasi Quick Test: {correct}/{len(test_cases)} ({accuracy:.0f}%)")
    print("\n[OK] Model siap digunakan! Jalankan: python main.py")


if __name__ == "__main__":
    main()

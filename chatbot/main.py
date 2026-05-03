#!/usr/bin/env python3
"""
main.py — Jalankan chatbot bakery di terminal

Usage:
    python main.py

Untuk keluar: ketik 'exit', 'quit', atau 'keluar'
"""

import sys
import os

sys.path.insert(0, os.path.dirname(__file__))

try:
    from colorama import Fore, Style, init as colorama_init
    colorama_init(autoreset=True)
    HAS_COLOR = True
except ImportError:
    HAS_COLOR = False

from bot.chatbot import BakeryChatbot
from config import CHATBOT_CONFIG


def cprint(text: str, color: str = "") -> None:
    if HAS_COLOR and color:
        print(color + text + Style.RESET_ALL)
    else:
        print(text)


def main():
    bot = BakeryChatbot()

    try:
        bot.start()
    except ConnectionError as e:
        print(f"\n[ERROR] {e}")
        print("Pastikan MySQL berjalan dan config.py sudah benar.")
        sys.exit(1)

    shop = CHATBOT_CONFIG["shop_name"]

    cprint(f"\n{'=' * 70}", Fore.CYAN if HAS_COLOR else "")
    cprint(f"   {shop} - Chatbot", Fore.CYAN if HAS_COLOR else "")
    cprint(f"  Ketik 'bantuan', 'help', atau 'panduan' untuk panduan lebih lanjut", Fore.CYAN if HAS_COLOR else "")
    cprint(f"{'=' * 70}\n", Fore.CYAN if HAS_COLOR else "")

    EXIT_WORDS = {"exit", "quit", "keluar", "bye", "dadah", "selesai"}

    while True:
        try:
            raw = input("Anda: ").strip()
        except (EOFError, KeyboardInterrupt):
            print("\n")
            break

        if not raw:
            continue

        if raw.lower() in EXIT_WORDS:
            cprint(bot.chat(raw), Fore.GREEN if HAS_COLOR else "")
            break

        response = bot.chat(raw)
        cprint(f"\nBot: {response}\n", Fore.GREEN if HAS_COLOR else "")

    bot.stop()


if __name__ == "__main__":
    main()

#!/usr/bin/env python3
# main.py — Production-ready Flask API for Lumineè Bakery Chatbot

import sys
import os
import logging
from flask import Flask, request, jsonify

sys.path.insert(0, os.path.dirname(__file__))

from bot.chatbot import BakeryChatbot
from config import CHATBOT_CONFIG

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)

# Init bot once
bot = BakeryChatbot()
bot.start()


@app.route("/chatbot", methods=["POST"])
def chatbot():
    """
    Production API endpoint for chatbot.
    
    Request:
        POST /chatbot
        {
            "message": "user text"
        }
    
    Response:
        {
            "reply": "...",
            "type": "text | product_list | product_detail | recommendation",
            "confidence": 0.0-1.0,
            "data": [...] | null
        }
    """
    try:
        data = request.json or {}
        message = data.get("message", "").strip()

        if not message:
            return jsonify({
                "reply": "Pesan kosong. Silakan ketik sesuatu.",
                "type": "text",
                "confidence": 0.0,
                "data": None
            }), 400

        # Process via chatbot
        result = bot.chat(message)

        # Build response
        response = {
            "reply": result.get("formatted_reply", result.get("reply", "")),
            "type": result.get("type", "text"),
            "confidence": result.get("confidence", 0.0),
        }

        # Include data if present
        if result.get("data") is not None:
            response["data"] = result.get("data")

        return jsonify(response), 200

    except Exception as e:
        logger.error(f"[/chatbot] Error: {e}")
        return jsonify({
            "reply": "Maaf, terjadi kesalahan. Coba lagi nanti ya.",
            "type": "text",
            "confidence": 0.0,
            "data": None
        }), 500


# Backward compatibility: legacy /chat endpoint
@app.route("/chat", methods=["POST"])
def chat():
    """Legacy endpoint for backward compatibility."""
    try:
        data = request.json or {}
        message = data.get("message", "").strip()

        if not message:
            return jsonify({"reply": "Pesan kosong"}), 400

        result = bot.chat(message)
        return jsonify({
            "type": result.get("type", "text"),
            "reply": result.get("formatted_reply", result.get("reply", ""))
        }), 200

    except Exception as e:
        logger.error(f"[/chat] Error: {e}")
        return jsonify({"reply": f"Error: {str(e)[:100]}"}), 500


# ================= CLI MODE =================
def run_cli():
    try:
        from colorama import Fore, Style, init as colorama_init
        colorama_init(autoreset=True)
        HAS_COLOR = True
    except ImportError:
        HAS_COLOR = False

    def cprint(text: str, color: str = ""):
        if HAS_COLOR and color:
            print(color + text + Style.RESET_ALL)
        else:
            print(text)

    shop = CHATBOT_CONFIG["shop_name"]
    cprint(f"\n{'=' * 70}")
    cprint(f"   {shop} - Chatbot", Fore.CYAN if HAS_COLOR else "")
    cprint(f"{'=' * 70}\n", Fore.CYAN if HAS_COLOR else "")

    EXIT_WORDS = {"exit", "quit", "keluar"}

    while True:
        try:
            msg = input("Anda: ").strip()
        except (EOFError, KeyboardInterrupt):
            break

        if not msg:
            continue

        if msg.lower() in EXIT_WORDS:
            break

        result = bot.chat(msg)
        formatted_reply = result.get("formatted_reply", result.get("reply", ""))
        cprint(f"\nBot: {formatted_reply}\n", Fore.GREEN if HAS_COLOR else "")


# ================= ENTRY POINT =================
if __name__ == "__main__":
    mode = os.environ.get("MODE", "api")

    if mode == "cli":
        run_cli()
    else:
        app.run(port=5090, debug=os.environ.get("DEBUG", "False").lower() == "true")
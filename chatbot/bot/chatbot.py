#!/usr/bin/env python3
# bot/chatbot.py - Engine utama chatbot bakery (PRODUCTION-READY HYBRID)

import random
import logging
from config import CHATBOT_CONFIG
from nlp.preprocessor import preprocess
from nlp.classifier import IntentClassifier
from nlp.entity_extractor import EntityExtractor
from db.connection import DatabaseConnection
from db.query_builder import QueryBuilder
from bot.decision_engine import DecisionEngine
from response import formatter as fmt

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

THRESHOLD = CHATBOT_CONFIG["confidence_threshold"]
SHOP = CHATBOT_CONFIG["shop_name"]


class BakeryChatbot:
    """Production-ready hybrid AI chatbot untuk Lumineè Bakery."""

    def __init__(self):
        self.classifier = IntentClassifier(
            model_path=CHATBOT_CONFIG["model_path"],
            confidence_threshold=THRESHOLD
        )
        self.db = DatabaseConnection()
        self.qb = None
        self.ext = EntityExtractor()
        self.engine = None
        self._product_names = []

    def start(self):
        """Initialize bot: DB connection, load model, prep data."""
        try:
            self.db.connect()
            self.qb = QueryBuilder(self.db)
            self.engine = DecisionEngine(self.qb)

            try:
                self.classifier.load()
            except RuntimeError:
                logger.info("[Bot] Model belum ada, melatih otomatis...")
                self.classifier.train()

            self._refresh_product_names()
            logger.info(f"[Bot] {SHOP} siap! (Production Hybrid AI)\n")
        except Exception as e:
            logger.error(f"[Bot] Startup error: {e}")
            raise

    def stop(self):
        """Graceful shutdown."""
        if self.db:
            self.db.disconnect()

    def _refresh_product_names(self):
        """Update product list untuk entity extraction."""
        try:
            self._product_names = self.qb.all_product_names()
            self.ext.update_products(self._product_names)
            logger.info(f"[Bot] Loaded {len(self._product_names)} products")
        except Exception as e:
            logger.error(f"[Bot] Error refreshing products: {e}")

    def chat(self, user_input: str) -> dict:
        """
        Main chat handler.
        Returns: {
            "type": "text | product_list | product_detail | recommendation",
            "reply": "...",
            "confidence": 0.0-1.0,
            "data": [...] or None
        }
        """
        try:
            # 1. Preprocess input
            proc = preprocess(user_input, known_words=self._product_names)
            clean = proc["clean"]

            # 2. Classify intent
            tag, score = self.classifier.predict(clean)
            logger.info(f"[Intent] tag={tag} | score={score:.2f}")

            # 3. Extract entities
            entities = self.ext.extract(clean)
            logger.info(f"[Entities] {entities}")

            # 4. Route via decision engine
            result = self.engine.handle_intent(tag, entities, confidence=score)

            # 5. Format based on type
            result["formatted_reply"] = self._format_response(result)

            return result

        except Exception as e:
            logger.error(f"[Chat] Error: {e}")
            return {
                "type": "text",
                "reply": "Maaf, terjadi kesalahan. Coba lagi nanti ya.",
                "confidence": 0.0,
                "data": None,
                "formatted_reply": "Maaf, terjadi kesalahan. Coba lagi nanti ya.",
                "error": str(e)
            }

    def _format_response(self, result: dict) -> str:
        """Format structured result untuk display."""
        resp_type = result.get("type", "text")
        data = result.get("data")
        reply = result.get("reply", "")

        try:
            if resp_type == "text":
                return reply

            elif resp_type == "product_list":
                return fmt.table_products(data or [], reply)

            elif resp_type == "product_detail":
                if data:
                    return fmt.detail_product(data)
                return reply

            elif resp_type == "recommendation":
                return fmt.pairing_suggestion("produk ini", data or [])

            else:
                return reply

        except Exception as e:
            logger.error(f"[Format] Error: {e}")
            return reply


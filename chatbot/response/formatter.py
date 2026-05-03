# response/formatter.py — Format output chatbot agar bersih & mudah dibaca (Enhanced)

from config import CHATBOT_CONFIG

CURRENCY = CHATBOT_CONFIG["currency_symbol"]
SHOP     = CHATBOT_CONFIG["shop_name"]


def fmt_price(price) -> str:
    """Rp 24.000"""
    try:
        return f"{CURRENCY} {int(price):,}".replace(",", ".")
    except (TypeError, ValueError):
        return "-"


def fmt_rating(rating) -> str:
    if rating is None:
        return "Belum ada rating"
    try:
        r = float(rating)
        stars = "★" * int(r) + "☆" * (5 - int(r))
        return f"{stars} ({r:.1f})"
    except (TypeError, ValueError):
        return "-"


def fmt_stock(stock) -> str:
    if stock is None:
        return "0 terjual"
    return f"{int(stock):,} terjual".replace(",", ".")


def fmt_date(date_str) -> str:
    """Format tanggal sederhana, e.g., '2 hari lalu'"""
    try:
        from datetime import datetime
        dt = datetime.fromisoformat(date_str.replace('Z', '+00:00'))
        return dt.strftime("%d/%m/%Y")
    except:
        return "-"


# ── Tabel produk (existing) ───────────────────────────────────────────────────

def table_products(rows: list[dict], title: str = "Produk") -> str:
    if not rows:
        return "😔 Tidak ada produk yang ditemukan."

    lines = [f"\n📋 {title} ({len(rows)} produk)\n"]
    lines.append("─" * 60)

    for i, r in enumerate(rows, 1):
        name     = r.get("name", "-")
        price    = fmt_price(r.get("price"))
        rating   = fmt_rating(r.get("rating"))
        category = r.get("category", "-")
        total_stock = r.get("total_stock")
        stock       = fmt_stock(total_stock if total_stock is not None else r.get("stock"))

        lines.append(f"{i:>2}. {name}")
        lines.append(f"     💰 {price}  |  📂 {category}")
        lines.append(f"     {rating}  |  🛒 {stock}")
        lines.append("")

    lines.append("─" * 60)
    return "\n".join(lines)


def detail_product(rows: list[dict]) -> str:
    if not rows:
        return "😔 Produk tidak ditemukan. Coba cek ejaan nama produk."

    lines = []
    for r in rows:
        lines.append(f"\n🍞 {r.get('name', '-')}")
        lines.append("─" * 40)
        lines.append(f"📂 Kategori  : {r.get('category', '-')}")
        lines.append(f"💰 Harga     : {fmt_price(r.get('price'))}")
        lines.append(f"⭐ Rating    : {fmt_rating(r.get('rating'))}")
        lines.append(f"🛒 Terjual   : {fmt_stock(r.get('stock'))}")
        lines.append(f"📝 Deskripsi : {r.get('description', '-')}")
        lines.append("")
    return "\n".join(lines)


def stock_result(rows: list[dict]) -> str:
    if not rows:
        return "😔 Produk tidak ditemukan di database."
    lines = ["\n📦 Status Stok\n" + "─" * 40]
    for r in rows:
        status_icon = "✅" if r.get("status") == "Tersedia" else "❌"
        lines.append(f"{status_icon} {r['name']} — {r.get('status','?')}")
        lines.append(f"   💰 {fmt_price(r.get('price'))}")
    return "\n".join(lines)


# ── New formatters for enhanced intents ───────────────────────────────────────

def recommendations_table(rows: list[dict]) -> str:
    if not rows:
        return "😔 Belum ada rekomendasi saat ini."
    lines = [f"\n🎯 Rekomendasi Lumine Bakery ({len(rows)} pilihan)\n"]
    lines.append("─" * 70)
    for i, r in enumerate(rows, 1):
        lines.append(f"{i:>2}. 🎁 {r.get('name', '-')}")
        lines.append(f"     💰 {fmt_price(r.get('price'))} | ⭐ {fmt_rating(r.get('rating'))}")
        lines.append(f"     Cocok untuk: {r.get('category', '-')} | {r.get('why_recommended', 'Pilihan favorit!')}")
        lines.append("")
    lines.append("─" * 70)
    return "\n".join(lines)


def new_arrivals_table(rows: list[dict]) -> str:
    lines = [f"\n✨ Produk Baru Masuk ({len(rows)} item)\n"]
    lines.append("─" * 60)
    for i, r in enumerate(rows, 1):
        lines.append(f"{i:>2}. 🆕 {r.get('name', '-')}")
        lines.append(f"     💰 {fmt_price(r.get('price'))} | 📂 {r.get('category', '-')}")
        lines.append(f"     Masuk: {fmt_date(r.get('created_at', '-'))}")
        lines.append("")
    return "\n".join(lines) if rows else "😴 Belum ada produk baru."


def pairing_suggestion(product_name: str, pairs: list[dict]) -> str:
    lines = [f"\n💑 Saran Pairing untuk '{product_name}'\n"]
    lines.append("─" * 50)
    for pair in pairs[:3]:
        lines.append(f"🍞 {pair['pair_name']} + {product_name}")
        lines.append(f"💰 Total: {fmt_price(pair['pair_price'])}")
        lines.append(f"✨ {pair['reason']}")
        lines.append("")
    lines.append("Coba kombinasi ini untuk pengalaman terbaik! 🥐")
    return "\n".join(lines)


def promotions_list(promos: list[dict]) -> str:
    if not promos:
        return "📢 Tidak ada promo aktif saat ini. Cek lagi besok!"
    lines = ["\n🎉 Promo Lumine Bakery Saat Ini\n"]
    lines.append("─" * 50)
    for promo in promos:
        lines.append(f"🔥 {promo['title']}")
        lines.append(f"   {promo['description']} — Hemat {promo['discount']}%")
        lines.append(f"   Berlaku: {promo['valid_until']}")
        lines.append("")
    return "\n".join(lines)


# ── Respons teks (enhanced help) ─────────────────────────────────────────────

def help_message() -> str:
    return f"""
╔══════════════════════════════════════╗
║  {SHOP:^36} ║
║  🤖 Panduan Chatbot LENGKAP          ║
╚══════════════════════════════════════╝

🍞 PRODUK & RECOMMENDATION
  • "semua produk" / "etalase"
  • "terlaris" / "rekomendasi" / "rekomendasi murah"
  • "produk baru" / "new arrival"
  • "pairing croissant" / "cocok sama apa donat"

💰 HARGA & FILTER
  • "termurah" / "termahal"
  • "rating terbaik"
  • "harga di bawah 25rb" / "range 15k-30k"

📂 KATEGORI
  • "donat apa saja" / "pastry" / "roti manis"

🔍 DETAIL & STOK
  • "info croissant almond" / "stok ada ga"

🎉 LAINNYA
  • "promo" / "diskon"
  • "jam buka" / "lokasi" / "cara pesan"

Ketik 'exit' atau 'keluar' untuk berhenti.
Sekarang lebih pintar dengan 22+ intents! 🚀
"""


def unknown_response() -> str:
    return (
        "🤔 Maaf, saya belum paham sepenuhnya.\n"
        "Coba: 'rekomendasi', 'promo', 'donat terlaris',\n"
        "atau 'bantuan' untuk panduan terbaru!"
    )


def low_confidence_response(tag: str, score: float) -> str:
    return (
        f"🤔 Kurang yakin ({score:.0%}). Mungkin '{tag}'?\n"
        "Coba 'bantuan' atau sebut produk spesifik."
    )


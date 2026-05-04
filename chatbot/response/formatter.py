# response/formatter.py — Format output chatbot yang bersih, ramah, dan berkarakter

from config import CHATBOT_CONFIG

CURRENCY = CHATBOT_CONFIG["currency_symbol"]
SHOP     = CHATBOT_CONFIG["shop_name"]


# ── Helpers dasar ─────────────────────────────────────────────────────────────

def fmt_price(price) -> str:
    try:
        return f"{CURRENCY} {int(price):,}".replace(",", ".")
    except (TypeError, ValueError):
        return "-"


def fmt_date(date_str) -> str:
    try:
        from datetime import datetime
        dt = datetime.fromisoformat(date_str.replace("Z", "+00:00"))
        return dt.strftime("%d %b %Y")
    except Exception:
        return "-"


# ── Helpers kontekstual (untuk narasi natural) ────────────────────────────────

def _rating_label(rating) -> str:
    """Rating jadi kata yang terasa natural, bukan angka mentah."""
    if rating is None:
        return ""
    try:
        r = float(rating)
        if r >= 4.8: return "rating sempurna ⭐"
        if r >= 4.5: return "rating tinggi ⭐"
        if r >= 4.0: return f"disukai pelanggan ({r:.1f})"
        return f"rating {r:.1f}"
    except (TypeError, ValueError):
        return ""


def _sold_label(stock) -> str:
    """Label terjual yang lebih bernyawa."""
    if stock is None or stock == 0:
        return ""
    n = int(stock)
    if n >= 50:  return f"laris banget 🔥 {n} terjual"
    if n >= 20:  return f"cukup laris — {n} terjual"
    if n >= 5:   return f"{n} terjual"
    return f"baru {n} terjual"


def _stock_status(status: str) -> str:
    return "✓  Tersedia" if status == "Tersedia" else "✗  Habis"


# ── Produk ────────────────────────────────────────────────────────────────────

def table_products(rows: list[dict], title: str = "Produk") -> str:
    if not rows:
        return (
            "Hmm, tidak ada produk yang cocok saat ini.\n"
            "Coba kata lain, atau ketik 'semua produk' untuk lihat katalog lengkap."
        )

    # Intro yang berbeda-beda tergantung konteks
    t = title.lower()
    if "terlaris" in t:
        intro = f"Ini {len(rows)} produk yang paling sering dipesan pelanggan kami:"
    elif "termurah" in t:
        intro = f"Ini {len(rows)} pilihan paling ramah di kantong:"
    elif "termahal" in t or "premium" in t:
        intro = f"Ini {len(rows)} pilihan premium dari {SHOP}:"
    elif "baru" in t or "new" in t:
        intro = f"Ada {len(rows)} produk baru yang bisa kamu coba:"
    elif "rating" in t:
        intro = f"Ini {len(rows)} produk dengan rating terbaik:"
    else:
        intro = f"Ketemu {len(rows)} produk, nih:"

    lines = [intro, ""]

    for i, r in enumerate(rows, 1):
        name   = r.get("name", "-")
        price  = fmt_price(r.get("price"))
        total  = r.get("total_stock")
        sold   = total if total is not None else r.get("stock")

        rating_lbl = _rating_label(r.get("rating"))
        sold_lbl   = _sold_label(sold)

        # Top 3 → spotlight dengan label kontekstual
        if i <= 3:
            medals = {1: "🥇", 2: "🥈", 3: "🥉"}
            extras = "  ·  ".join(filter(None, [rating_lbl, sold_lbl]))
            lines.append(f"{medals[i]}  {name}  —  {price}")
            if extras:
                lines.append(f"    {extras}")
            lines.append("")

        # Selebihnya → satu baris ringkas
        else:
            tag = sold_lbl or rating_lbl
            suffix = f"  ({tag})" if tag else ""
            lines.append(f"    {i}.  {name}  —  {price}{suffix}")

    lines += ["", "Mau tahu lebih lanjut? Sebutkan nama produknya. 😊"]
    return "\n".join(lines)


def detail_product(rows: list[dict]) -> str:
    if not rows:
        return (
            "Produk itu sepertinya belum ada di katalog kami. 🤔\n"
            "Cek ejaannya, atau ketik 'semua produk' untuk lihat semua pilihan."
        )

    lines = []
    for r in rows:
        name       = r.get("name", "-")
        price      = fmt_price(r.get("price"))
        cat        = r.get("category", "-")
        desc       = r.get("description") or "Belum ada deskripsi."
        rating_lbl = _rating_label(r.get("rating"))
        sold_lbl   = _sold_label(r.get("stock"))

        lines.append(name)
        lines.append(f"{price}  ·  {cat}")
        lines.append("")
        lines.append(desc)

        meta = "  ·  ".join(filter(None, [rating_lbl, sold_lbl]))
        if meta:
            lines += ["", meta]

        lines.append("")

    lines.append("Ada yang mau ditanyakan lagi? 🍞")
    return "\n".join(lines)


def stock_result(rows: list[dict]) -> str:
    if not rows:
        return (
            "Produk tidak ditemukan di katalog kami.\n"
            "Coba ketik nama yang lebih spesifik, ya!"
        )

    lines = []
    for r in rows:
        status = _stock_status(r.get("status", ""))
        lines.append(f"{status}  {r['name']}  ({fmt_price(r.get('price'))})")

    if any(r.get("status") == "Tersedia" for r in rows):
        lines += ["", "Mau pesan? Ketik 'cara pesan' untuk panduan. 🛒"]

    return "\n".join(lines)


# ── Rekomendasi & Fitur ───────────────────────────────────────────────────────

def recommendations_table(rows: list[dict]) -> str:
    if not rows:
        return "Belum ada rekomendasi saat ini. Cek lagi nanti ya! 🌟"

    lines = ["Kalau boleh saran, ini yang kami rekomendasikan ✨", ""]

    for i, r in enumerate(rows, 1):
        name  = r.get("name", "-")
        price = fmt_price(r.get("price"))
        note  = r.get("why_recommended", "Favorit pelanggan!")

        if i == 1:
            lines.append(f"🌟  {name}  —  {price}")
            lines.append(f'    "{note}"')
            lines.append("")
        elif i <= 3:
            lines.append(f"    {name}  —  {price}")
            lines.append(f"    {note}")
            lines.append("")
        else:
            lines.append(f"    {i}.  {name}  —  {price}")

    lines += ["", "Tertarik salah satu? Sebutkan namanya untuk info lebih lanjut. 🍰"]
    return "\n".join(lines)


def new_arrivals_table(rows: list[dict]) -> str:
    if not rows:
        return "Belum ada produk baru saat ini — pantau terus ya! ✨"

    lines = [f"Baru masuk {len(rows)} produk yang sayang dilewatkan:", ""]

    for i, r in enumerate(rows, 1):
        name  = r.get("name", "-")
        price = fmt_price(r.get("price"))
        cat   = r.get("category", "-")
        date  = fmt_date(r.get("created_at", ""))

        if i == 1:
            lines.append(f"🆕  {name}  —  {price}")
            lines.append(f"    {cat}  ·  hadir sejak {date}")
            lines.append("")
        else:
            lines.append(f"    {i}.  {name}  —  {price}  ({cat})")

    lines += ["", "Penasaran salah satunya? Ketik namanya! 😋"]
    return "\n".join(lines)


def pairing_suggestion(product_name: str, pairs: list[dict]) -> str:
    lines = [
        f"'{product_name}' enak banget kalau dipadukan dengan...",
        "",
    ]

    for pair in pairs[:3]:
        lines.append(f"  +  {pair['pair_name']}")
        lines.append(f"     {pair['reason']}")
        lines.append(f"     Total: {fmt_price(pair['pair_price'])}")
        lines.append("")

    lines.append("Cobain deh — dijamin nagih! 🥐☕")
    return "\n".join(lines)


def promotions_list(promos: list[dict]) -> str:
    if not promos:
        return (
            "Belum ada promo aktif saat ini.\n"
            "Tapi produk kami tetap worth it, kok! Cek rekomendasi terbaru yuk. 😊"
        )

    lines = ["Promo yang lagi jalan nih 🎉", ""]

    for promo in promos:
        lines.append(f"  {promo['title']}")
        lines.append(f"  {promo['description']}  —  hemat {promo['discount']}%")
        lines.append(f"  Berlaku s/d {promo['valid_until']}")
        lines.append("")

    lines.append("Buruan sebelum habis! 🔥")
    return "\n".join(lines)


# ── Pesan Umum ────────────────────────────────────────────────────────────────

def help_message() -> str:
    return f"""\
Halo! Saya asisten {SHOP}. 👋
Ini yang bisa saya bantu:

  Produk & Rekomendasi
    "semua produk"  ·  "terlaris"  ·  "rekomendasi murah"
    "produk baru"   ·  "pairing croissant"

  Harga & Filter
    "termurah"  ·  "rating terbaik"
    "harga di bawah 25rb"  ·  "range 15k–30k"

  Kategori
    "donat"  ·  "pastry"  ·  "roti manis"

  Info & Stok
    "info croissant almond"  ·  "stok ada ga"

  Lainnya
    "promo"  ·  "jam buka"  ·  "cara pesan"

Ketik 'keluar' kapan saja untuk berhenti.
Ada yang bisa saya bantu? 😊"""


def unknown_response() -> str:
    return (
        "Hmm, saya kurang paham maksudnya. 🤔\n"
        "Coba ketik 'bantuan' untuk lihat semua yang bisa ditanyakan,\n"
        "atau sebutkan nama produk yang kamu cari!"
    )


def low_confidence_response(tag: str, score: float) -> str:
    return (
        f"Sepertinya kamu menanyakan soal '{tag}', tapi saya kurang yakin ({score:.0%}).\n"
        "Bisa diperjelas sedikit? Atau ketik 'bantuan' untuk panduan lengkap."
    )
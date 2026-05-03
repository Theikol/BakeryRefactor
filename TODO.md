# Chatbot Enhancement TODO for Lumine Bakery
Status: [IN PROGRESS] 

## Steps (Logical Order)

- [✅] **Step 1:** Create this TODO.md 
- [✅] **Step 2:** Update data/intents.json (add patterns + 4 new intents: recommendations, new_arrivals, product_pairing, promotions)
- [✅] **Step 3:** Update nlp/synonyms.py (expand synonyms/keywords/typos with 50+ bakery/Indo terms)
- [✅] **Step 4:** Update response/formatter.py (new formatters + help examples)
- [✅] **Step 5:** Update config.py (threshold 0.35 + promo config)
- [ ] **Step 6:** Update db/query_builder.py (new methods: recommendations, new_arrivals, pairings_query, promos_query)
- [ ] **Step 7:** Update bot/chatbot.py (new handlers + routing + fallbacks)
- [ ] **Step 8:** Update train.py (minor logging)
- [ ] **Step 9:** Run `cd chatbot && pip install -r requirements.txt` (if needed)
- [ ] **Step 10:** Run `cd chatbot && python train.py` (re-train, check accuracy >90%)
- [ ] **Step 11:** Test bot: `cd chatbot && python main.py` with new queries (e.g., \"rekomendasi murah\", \"promo\", \"pairing roti\")
- [ ] **Step 12:** Final validation + attempt_completion

**Next:** Fix lint errors in query_builder.py & chatbot.py (em-dash, SQL escapes), then Step 8-12.

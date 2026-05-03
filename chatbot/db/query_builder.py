#!/usr/bin/env python3
# db/query_builder.py - Query builder for Lumine Bakery bot (enhanced)

from config import CHATBOT_CONFIG
from db.connection import DatabaseConnection

MAX = CHATBOT_CONFIG['max_results']
T = 'products'
OI = 'order_items'

class QueryBuilder:
    'Query builder - read-only SELECT for bakery products'

    def __init__(self, db):
        self.db = db

    def list_all_products(self):
        sql = '''
            SELECT id, name, category, price, rating, stock, description
            FROM {} 
            WHERE is_active = 1
            ORDER BY name
            LIMIT {}
        '''.format(T, MAX)
        return self.db.execute_select(sql)

    def product_detail(self, product_name):
        sql = '''
            SELECT id, name, category, price, rating, stock, description, image
            FROM {}
            WHERE is_active = 1
              AND LOWER(name) LIKE %s
            LIMIT 5
        '''.format(T)
        return self.db.execute_select(sql, ('%' + product_name.lower() + '%',))

    def sort_by_price_asc(self, category=None):
        base, params = self._base_active(category)
        sql = base + ' ORDER BY price ASC LIMIT {} '.format(MAX)
        return self.db.execute_select(sql, params)

    def sort_by_price_desc(self, category=None):
        base, params = self._base_active(category)
        sql = base + ' ORDER BY price DESC LIMIT {} '.format(MAX)
        return self.db.execute_select(sql, params)

    def sort_by_rating_desc(self, category=None):
        base, params = self._base_active(category)
        sql = base + ' AND rating IS NOT NULL ORDER BY rating DESC LIMIT {} '.format(MAX)
        return self.db.execute_select(sql, params)

    def best_seller(self, category=None):
        cat_filter = ' AND p.category = %s' if category else ''
        params = (category,) if category else ()
        sql = '''
            SELECT p.id, p.name, p.category, p.price, p.rating,
                   COALESCE(SUM(oi.quantity), 0) AS total_stock,
                   p.stock, p.description
            FROM {} p
            LEFT JOIN {} oi ON p.id = oi.product_id
            WHERE p.is_active = 1 {}
            GROUP BY p.id
            ORDER BY total_stock DESC
            LIMIT {}
        '''.format(T, OI, cat_filter, MAX)
        return self.db.execute_select(sql, params)

    def price_range(self, price_min=None, price_max=None, category=None):
        conditions = ['is_active = 1']
        params = []
        if price_min is not None:
            conditions.append('price >= %s')
            params.append(price_min)
        if price_max is not None:
            conditions.append('price <= %s')
            params.append(price_max)
        if category:
            conditions.append('LOWER(category) = %s')
            params.append(category.lower())
        where = ' AND '.join(conditions)
        sql = '''
            SELECT id, name, category, price, rating, stock, description
            FROM {}
            WHERE {}
            ORDER BY price ASC
            LIMIT {}
        '''.format(T, where, MAX)
        return self.db.execute_select(sql, tuple(params))

    def by_category(self, category):
        sql = '''
            SELECT id, name, category, price, rating, stock, description
            FROM {}
            WHERE is_active = 1
              AND LOWER(category) = %s
            ORDER BY name
            LIMIT {}
        '''.format(T, MAX)
        return self.db.execute_select(sql, (category.lower(),))

    def all_product_names(self):
        sql = 'SELECT name FROM {} WHERE is_active = 1'.format(T)
        rows = self.db.execute_select(sql)
        return [r['name'] for r in rows]

    def check_stock(self, product_name):
        sql = '''
            SELECT id, name, category, price,
                   IF(is_active = 1, 'Tersedia', 'Tidak Tersedia') AS status
            FROM {}
            WHERE LOWER(name) LIKE %s
            LIMIT 5
        '''.format(T)
        return self.db.execute_select(sql, ('%' + product_name.lower() + '%',))

    # New enhanced methods
    def recommendations(self, category=None):
        base, params = self._base_active(category)
        sql = base + ' AND rating >= 4.0 AND price <= 30000 ORDER BY rating DESC, price ASC LIMIT {} '.format(MAX)
        return self.db.execute_select(sql, params)

    def new_arrivals(self, category=None):
        base, params = self._base_active(category)
        sql = base + ' ORDER BY COALESCE(created_at, id) DESC LIMIT {} '.format(MAX)
        return self.db.execute_select(sql, params)

    def complementary_products(self, product_category, max_price=25000):
        sql = '''
            SELECT id, name, category, price, rating
            FROM {}
            WHERE is_active = 1
              AND category != %s
              AND price <= %s
            ORDER BY rating DESC
            LIMIT 3
        '''.format(T)
        return self.db.execute_select(sql, (product_category, max_price))

    def promotions(self):
        return []

    def _base_active(self, category):
        base = 'SELECT id, name, category, price, rating, stock, description FROM {} WHERE is_active = 1'.format(T)
        if category:
            base += ' AND LOWER(category) = %s'
            return base, (category.lower(),)
        return base, ()


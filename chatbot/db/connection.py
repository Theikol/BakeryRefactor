# db/connection.py — Koneksi MySQL (READ-ONLY / SELECT saja)

import mysql.connector
from mysql.connector import Error
from config import DB_CONFIG


class DatabaseConnection:
    """Wrapper koneksi MySQL dengan SELECT-only access."""

    def __init__(self):
        self._conn = None

    def connect(self) -> None:
        try:
            self._conn = mysql.connector.connect(**DB_CONFIG)
            if self._conn.is_connected():
                print("[DB] Koneksi berhasil ke database.")
        except Error as e:
            raise ConnectionError(f"[DB] Gagal koneksi: {e}")

    def disconnect(self) -> None:
        if self._conn and self._conn.is_connected():
            self._conn.close()
            print("[DB] Koneksi ditutup.")

    def execute_select(self, query: str, params: tuple = ()) -> list[dict]:
        """
        Jalankan query SELECT dan kembalikan list of dict.
        Hanya menerima query yang diawali SELECT (keamanan).
        """
        q = query.strip().upper()
        if not q.startswith("SELECT"):
            raise PermissionError("Hanya query SELECT yang diperbolehkan!")

        if self._conn is None or not self._conn.is_connected():
            self.connect()

        try:
            cursor = self._conn.cursor(dictionary=True)
            cursor.execute(query, params)
            rows = cursor.fetchall()
            cursor.close()
            return rows
        except Error as e:
            print(f"[DB] Query error: {e}")
            return []

    def __enter__(self):
        self.connect()
        return self

    def __exit__(self, *_):
        self.disconnect()

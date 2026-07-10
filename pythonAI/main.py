import os
import json
import re
import time
from typing import List
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import pandas as pd
import numpy as np
from scipy import stats
from google import genai
from google.genai import types
from dotenv import load_dotenv

load_dotenv()

app = FastAPI()

GOOGLE_API_KEY = os.getenv("GEMINI_API_KEY")

client = None
if not GOOGLE_API_KEY:
    print("Peringatan: GEMINI_API_KEY tidak ditemukan di file .env!")
else:
    client = genai.Client(api_key=GOOGLE_API_KEY)

# Pakai model lite: quota gratis jauh lebih besar (ratusan-ribuan request/hari)
# dibanding gemini-flash-latest yang cuma ~20/hari untuk generasi terbaru
GEMINI_MODEL = "gemini-flash-lite-latest"

class SensorItem(BaseModel):
    ph: float
    suhu: float
    ec_tds: float

class HistoricalPayload(BaseModel):
    id_sensor: int
    history: List[SensorItem]

def analyze_parameter(series, low_th, high_th, total_readings):
    avg_val = float(series.mean())
    min_val = float(series.min())
    max_val = float(series.max())
    median_val = float(series.median())

    std_val = float(series.std()) if len(series) > 1 else 0.0
    if np.isnan(std_val):
        std_val = 0.0

    in_range_count = int(((series >= low_th) & (series <= high_th)).sum())
    stability_pct = round((in_range_count / total_readings) * 100, 2) if total_readings > 0 else 0.0

    x = np.arange(len(series))
    if len(series) > 1 and std_val > 0:
        slope, intercept, r_value, p_value, std_err = stats.linregress(x, series)
        r_squared = float(r_value**2) if not np.isnan(r_value) else 0.0
        if p_value < 0.05 and abs(slope) > 1e-4:
            direction = "increasing" if slope > 0 else "decreasing"
        else:
            direction = "stable"
    else:
        slope, r_squared, direction = 0.0, 0.0, "stable"

    return {
        "average": round(avg_val, 3),
        "minimum": round(min_val, 2),
        "maximum": round(max_val, 2),
        "median": round(median_val, 2),
        "std_dev": round(std_val, 3),
        "stability_percentage": stability_pct,
        "trend": {
            "direction": direction,
            "slope_per_reading": float(slope),
            "r_squared": round(r_squared, 3)
        }
    }

def extract_json(raw_text):
    """
    Gemini kadang membungkus JSON dengan markdown fences (```json ... ```)
    atau menambahkan teks ekstra walau sudah diminta response_mime_type=json.
    Fungsi ini membersihkan itu sebelum parsing.
    """
    text = raw_text.strip()

    text = re.sub(r"^```(?:json)?\s*", "", text)
    text = re.sub(r"\s*```$", "", text)
    text = text.strip()

    try:
        return json.loads(text)
    except json.JSONDecodeError:
        pass

    start = text.find("{")
    end = text.rfind("}")
    if start != -1 and end != -1 and end > start:
        candidate = text[start:end + 1]
        return json.loads(candidate)

    raise ValueError(f"Tidak bisa parse JSON dari respons Gemini. Raw text: {raw_text[:300]}")

def call_gemini_with_retry(prompt, max_retries=3):
    last_error = None
    for attempt in range(max_retries):
        try:
            return client.models.generate_content(
                model=GEMINI_MODEL,
                contents=prompt,
                config=types.GenerateContentConfig(
                    response_mime_type="application/json"
                )
            )
        except Exception as e:
            last_error = e
            error_text = str(e)

            if "429" in error_text or "RESOURCE_EXHAUSTED" in error_text:
                raise HTTPException(
                    status_code=429,
                    detail="Quota Gemini API harian/menit sudah habis. Tunggu beberapa saat atau cek AI Studio untuk reset quota."
                )

            if ("503" in error_text or "UNAVAILABLE" in error_text) and attempt < max_retries - 1:
                wait_time = 3 * (attempt + 1)
                print(f"Gemini sibuk (percobaan {attempt + 1}/{max_retries}), tunggu {wait_time}s...")
                time.sleep(wait_time)
                continue
            raise e
    raise last_error

@app.post("/historical-insight")
def process_historical_insight(payload: HistoricalPayload):
    try:
        if not client:
            raise HTTPException(status_code=500, detail="Gemini Client belum terinisialisasi.")

        if not payload.history:
            raise HTTPException(status_code=400, detail="Data historis kosong.")

        raw_data = [{"ph": item.ph, "suhu": item.suhu, "ec_tds": item.ec_tds} for item in payload.history]
        df = pd.DataFrame(raw_data)
        total_readings = len(df)

        ph_warn = ((df['ph'] < 6.0) | (df['ph'] > 8.0)).sum()
        tds_warn = ((df['ec_tds'] < 400) | (df['ec_tds'] > 1200)).sum()
        suhu_warn = ((df['suhu'] < 20) | (df['suhu'] > 28)).sum()
        total_warnings = int(ph_warn + tds_warn + suhu_warn)

        stats_ph = analyze_parameter(df['ph'], 6.0, 8.0, total_readings)
        stats_tds = analyze_parameter(df['ec_tds'], 400, 1200, total_readings)
        stats_suhu = analyze_parameter(df['suhu'], 20, 28, total_readings)

        corr_ph_tds = float(df['ph'].corr(df['ec_tds'])) if total_readings > 1 and df['ph'].std() > 0 and df['ec_tds'].std() > 0 else 0.0
        corr_suhu_ph = float(df['suhu'].corr(df['ph'])) if total_readings > 1 and df['suhu'].std() > 0 and df['ph'].std() > 0 else 0.0
        corr_suhu_tds = float(df['suhu'].corr(df['ec_tds'])) if total_readings > 1 and df['suhu'].std() > 0 and df['ec_tds'].std() > 0 else 0.0

        corr_ph_tds = 0.0 if np.isnan(corr_ph_tds) else corr_ph_tds
        corr_suhu_ph = 0.0 if np.isnan(corr_suhu_ph) else corr_suhu_ph
        corr_suhu_tds = 0.0 if np.isnan(corr_suhu_tds) else corr_suhu_tds

        avg_stability = (stats_ph['stability_percentage'] + stats_tds['stability_percentage'] + stats_suhu['stability_percentage']) / 3
        if avg_stability >= 75:
            risk_level = "low"
        elif avg_stability >= 45:
            risk_level = "medium"
        else:
            risk_level = "high"

        analytics_summary = {
            "meta": {"total_readings_used": total_readings},
            "total_warnings_detected": total_warnings,
            "risk_level": risk_level,
            "ph": stats_ph,
            "tds": stats_tds,
            "suhu": stats_suhu,
            "correlations": {
                "ph_and_tds": round(corr_ph_tds, 3),
                "suhu_and_ph": round(corr_suhu_ph, 3),
                "suhu_and_tds": round(corr_suhu_tds, 3)
            }
        }

        prompt = f"""
        Kamu adalah asisten AI yang membantu petani hidroponik Sawi Putih memahami kondisi tanaman mereka.

        THRESHOLD IDEAL YANG WAJIB KAMU PAKAI (jangan buat angka sendiri):
        - pH ideal: 6.0 - 8.0
        - TDS ideal: 400 - 1200 ppm
        - Suhu ideal: 20 - 28°C

        DATA STATISTIK HASIL PEMBACAAN SENSOR:
        {json.dumps(analytics_summary, indent=2)}

        ATURAN BAHASA:
        - Tulis dengan bahasa Indonesia yang santai dan mudah dipahami petani awam, seperti menjelaskan ke teman, BUKAN bahasa laporan ilmiah/akademik.
        - Hindari istilah teknis statistik (jangan sebut "slope", "r_squared", "std_dev", dsb). Kalau perlu jelaskan tren, cukup bilang "naik", "turun", atau "stabil".
        - Kalimat pendek-pendek, langsung ke inti.
        - SELALU pakai angka threshold di atas kalau menyebut batas ideal — jangan buat angka baru.

        ATURAN OUTPUT:
        - Balas HANYA dengan objek JSON murni, TANPA markdown code fence, TANPA teks tambahan sebelum/sesudahnya.
        - Wajib menghasilkan objek JSON dengan key berikut:
        - "summary": string (2-3 kalimat, kondisi sistem secara umum, bahasa santai)
        - "trend_analysis": string (arah tren tiap parameter dalam bahasa awam, sebut hanya naik/turun/stabil dan seberapa stabil)
        - "pattern_analysis": string (hubungan antar parameter dalam bahasa awam, contoh: "waktu suhu naik, biasanya pH ikut turun")
        - "recommendation": array of string (tindakan konkret, singkat, actionable, urut dari prioritas tertinggi)
        - "risk": string (Hanya bernilai "low", "medium", atau "high" — WAJIB salah satu dari tiga ini persis, jangan diterjemahkan)
        """

        response = call_gemini_with_retry(prompt)
        ai_json = extract_json(response.text)

        ai_json["statistics"] = analytics_summary
        ai_json["id_sensor"] = payload.id_sensor

        return ai_json

    except HTTPException:
        raise
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

if __name__ == '__main__':
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8001, reload=True)
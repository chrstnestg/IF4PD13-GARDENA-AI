import os
import json
import re
import time
from typing import List, Optional
from datetime import datetime
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

GEMINI_MODEL = "gemini-flash-lite-latest"

# Dipakai kalau payload tidak menyertakan timestamp (fallback),
# sesuai requirement non-fungsional: ESP32 kirim data tiap 1 menit.
ASSUMED_INTERVAL_MINUTES = 1.0


class SensorItem(BaseModel):
    ph: float
    suhu: float
    ec_tds: float
    timestamp: Optional[str] = None  # ISO 8601, misal "2026-07-11T10:15:00"


class HistoricalPayload(BaseModel):
    id_sensor: int
    history: List[SensorItem]


def compute_duration_minutes(df: pd.DataFrame, total_readings: int):
    """Hitung durasi asli data historis dari timestamp kalau tersedia,
    kalau tidak, fallback ke asumsi interval tetap (dan kasih tahu itu estimasi)."""
    if "timestamp" in df.columns and df["timestamp"].notna().all():
        try:
            ts = pd.to_datetime(df["timestamp"])
            duration = (ts.max() - ts.min()).total_seconds() / 60.0
            if duration > 0:
                return round(duration, 1), False
        except Exception:
            pass
    estimated = round((total_readings - 1) * ASSUMED_INTERVAL_MINUTES, 1)
    return max(estimated, 0.0), True


def analyze_parameter(series, low_th, high_th, total_readings, duration_minutes):
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

    total_change = round(float(series.iloc[-1] - series.iloc[0]), 3) if len(series) > 1 else 0.0
    rate_per_minute = round(total_change / duration_minutes, 4) if duration_minutes > 0 else 0.0

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
            "r_squared": round(r_squared, 3),
            "total_change": total_change,
            "rate_per_minute": rate_per_minute,
            "duration_minutes": duration_minutes
        }
    }


def classify_correlation(r):
    """Klasifikasi arah & kekuatan korelasi Pearson secara deterministik,
    supaya Gemini tidak perlu (dan tidak boleh) menebak sendiri."""
    abs_r = abs(r)
    if abs_r >= 0.7:
        strength = "kuat"
    elif abs_r >= 0.3:
        strength = "sedang"
    else:
        strength = "lemah"

    if r > 0.05:
        direction = "searah (kalau satu naik, yang lain cenderung ikut naik)"
    elif r < -0.05:
        direction = "berlawanan arah (kalau satu naik, yang lain cenderung turun)"
    else:
        direction = "tidak ada hubungan yang jelas"

    return {"r": round(float(r), 3), "strength": strength, "direction": direction}


def build_risk_context(risk_level, avg_stability, duration_minutes, is_estimated):
    """Definisi risiko yang eksplisit: rentang waktu observasi + konsekuensi,
    supaya Gemini punya angka konkret untuk dikutip, bukan sekadar label."""
    duration_note = "(estimasi, berdasarkan asumsi 1 pembacaan/menit)" if is_estimated else "(berdasarkan waktu pembacaan aktual)"

    consequences = {
        "low": "Kondisi stabil, tidak perlu tindakan segera. Cukup lanjutkan pemantauan rutin.",
        "medium": "Sebagian parameter mulai keluar rentang ideal. Kalau dibiarkan tanpa koreksi dalam waktu dekat, berisiko menghambat pertumbuhan sawi putih (misalnya penyerapan nutrisi tidak optimal).",
        "high": "Mayoritas pembacaan berada di luar rentang ideal dalam periode observasi ini. Risiko stres/kerusakan pada tanaman meningkat apabila tidak segera dikoreksi."
    }

    return {
        "risk_level": risk_level,
        "observed_over_minutes": duration_minutes,
        "duration_note": duration_note,
        "stability_percentage": round(avg_stability, 2),
        "consequence_if_unaddressed": consequences[risk_level]
    }


def build_example_sentences(stats_ph, stats_tds, stats_suhu):
    """Bikin contoh kalimat nyata dari data aktual, buat 'ngajarin' Gemini
    format yang diharapkan lewat few-shot, bukan cuma instruksi abstrak."""
    def trend_sentence(label, s):
        t = s["trend"]
        if abs(t["total_change"]) < 0.01:
            return f"{label} stabil di angka {s['average']} selama {t['duration_minutes']} menit terakhir"
        arah = "naik" if t["total_change"] > 0 else "turun"
        nilai_awal = round(s['average'] - t['total_change'], 2)
        return f"{label} {arah} dari {nilai_awal} ke {s['average']} dalam {t['duration_minutes']} menit terakhir"

    return {
        "ph": trend_sentence("pH", stats_ph),
        "tds": trend_sentence("TDS", stats_tds),
        "suhu": trend_sentence("Suhu", stats_suhu),
    }


def extract_json(raw_text):
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

        raw_data = [
            {"ph": item.ph, "suhu": item.suhu, "ec_tds": item.ec_tds, "timestamp": item.timestamp}
            for item in payload.history
        ]
        df = pd.DataFrame(raw_data)
        total_readings = len(df)

        duration_minutes, is_estimated = compute_duration_minutes(df, total_readings)

        ph_warn = ((df['ph'] < 6.0) | (df['ph'] > 8.0)).sum()
        tds_warn = ((df['ec_tds'] < 400) | (df['ec_tds'] > 1200)).sum()
        suhu_warn = ((df['suhu'] < 20) | (df['suhu'] > 28)).sum()
        total_warnings = int(ph_warn + tds_warn + suhu_warn)

        stats_ph = analyze_parameter(df['ph'], 6.0, 8.0, total_readings, duration_minutes)
        stats_tds = analyze_parameter(df['ec_tds'], 400, 1200, total_readings, duration_minutes)
        stats_suhu = analyze_parameter(df['suhu'], 20, 28, total_readings, duration_minutes)

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

        risk_context = build_risk_context(risk_level, avg_stability, duration_minutes, is_estimated)
        example_sentences = build_example_sentences(stats_ph, stats_tds, stats_suhu)

        analytics_summary = {
            "meta": {
                "total_readings_used": total_readings,
                "duration_minutes": duration_minutes,
                "duration_is_estimated": is_estimated
            },
            "total_warnings_detected": total_warnings,
            "risk_context": risk_context,
            "ph": stats_ph,
            "tds": stats_tds,
            "suhu": stats_suhu,
            "correlations": {
                "ph_and_tds": classify_correlation(corr_ph_tds),
                "suhu_and_ph": classify_correlation(corr_suhu_ph),
                "suhu_and_tds": classify_correlation(corr_suhu_tds)
            }
        }

        prompt = f"""
        Kamu adalah asisten AI yang membantu petani hidroponik Sawi Putih memahami kondisi tanaman mereka.

        THRESHOLD IDEAL YANG WAJIB KAMU PAKAI (jangan buat angka sendiri):
        - pH ideal: 6.0 - 8.0
        - TDS ideal: 400 - 1200 ppm
        - Suhu ideal: 20 - 28°C

        DATA STATISTIK HASIL PEMBACAAN SENSOR (semua angka sudah dihitung pasti, JANGAN dihitung ulang atau ditebak):
        {json.dumps(analytics_summary, indent=2)}

        CONTOH KALIMAT TREN YANG BENAR UNTUK DATA INI (pakai persis pola kalimat ini, angka boleh disusun ulang tapi WAJIB tetap ada nilai + satuan menit):
        - {example_sentences['ph']}
        - {example_sentences['tds']}
        - {example_sentences['suhu']}

        KATA-KATA YANG DILARANG DIPAKAI (ganti dengan angka/waktu konkret dari data di atas):
        - "beberapa waktu", "kadang", "cenderung bergeser", "fluktuasi", "fluktuatif", "cukup lebar", "belakangan ini"
        - Kalau mau bilang stabil, sebut angka rata-rata dan persen stabil (stability_percentage), jangan cuma kata "stabil" polos.

        ATURAN WAJIB SAAT MENULIS NARASI:
        - "summary" WAJIB menyebut nilai rata-rata aktual pH, TDS, dan Suhu (angka pastinya, bukan cuma kata "aman").
        - "trend_analysis" WAJIB pakai format seperti 3 contoh kalimat di atas untuk masing-masing parameter — sebutkan angka awal, angka akhir, dan durasi dalam menit.
        - "pattern_analysis" WAJIB sebut kekuatan hubungan (lemah/sedang/kuat) dan arahnya (searah/berlawanan arah) dari field correlations di atas — bukan cuma kata "biasanya".
        - Saat menjelaskan risiko, WAJIB sebut consequence_if_unaddressed dan observed_over_minutes dari risk_context di atas, jangan menambah konsekuensi baru yang tidak ada di data.
        - Kalau duration_is_estimated bernilai true, boleh tambahkan kata "sekitar" di depan angka durasi.

        ATURAN BAHASA:
        - Bahasa Indonesia santai, seperti menjelaskan ke teman, BUKAN bahasa laporan ilmiah.
        - Hindari istilah teknis mentah (slope, r_squared, std_dev, Pearson, stability_percentage) — tapi angka aktualnya WAJIB tetap muncul dalam kalimat.
        - Kalimat pendek-pendek, langsung ke inti.

        ATURAN OUTPUT:
        - Balas HANYA dengan objek JSON murni, TANPA markdown code fence, TANPA teks tambahan sebelum/sesudahnya.
        - Wajib menghasilkan objek JSON dengan key berikut:
        - "summary": string (2-3 kalimat, kondisi sistem secara umum, bahasa santai)
        - "trend_analysis": string (arah tren tiap parameter + angka rentang waktu, bahasa awam)
        - "pattern_analysis": string (hubungan antar parameter + kekuatan/arah korelasi, bahasa awam)
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
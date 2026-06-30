from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import numpy as np
import pickle
from tensorflow.keras.models import load_model

app = FastAPI()

try:
    model = load_model('model_gardena_mlp.h5')
    with open('scaler_mlp.pkl', 'rb') as f:
        scaler = pickle.load(f)
    with open('label_encoder_mlp.pkl', 'rb') as f:
        label_encoder = pickle.load(f)
except Exception as e:
    print(f"Error saat memuat model/scaler: {str(e)}")

# ════════ MASTER DATA REKOMENDASI SATUAN (THRESHOLD SAWI PUTIH BARU) ════════
REKOMENDASI_BASE = {
    'Normal': [
        'Kondisi nutrisi optimal, pertahankan kadar TDS saat ini.',
        'Lakukan pengecekan rutin setiap hari.',
    ],
    'Nutrisi Kurang': [
        'Tambahkan larutan nutrisi AB Mix ke dalam tangki.',
        'Cek TDS hingga mencapai minimal 400 ppm (Ideal: 400-1200 ppm).',
    ],
    'Nutrisi Berlebih': [
        'Encerkan larutan dengan menambahkan air bersih ke tandon.',
        'Cek TDS hingga berada di bawah 1200 ppm.',
    ],
    'pH Rendah': [
        'Tambahkan larutan pH Up secara bertahap.',
        'Target pH ideal Sawi Putih antara 6.0 hingga 8.0.',
    ],
    'pH Tinggi': [
        'Tambahkan larutan pH Down secara bertahap.',
        'Target pH ideal Sawi Putih antara 6.0 hingga 8.0.',
    ],
    'Suhu Tidak Ideal': [
        'Periksa sirkulasi air tandon dan ventilasi area tanam.',
        'Pastikan suhu air berada di kisaran hangat ideal sawi yaitu 20–28°C.',
    ],
}

class SensorInput(BaseModel):
    ph: float
    tds: float
    suhu: float

@app.post("/predict")
def predict(input: SensorInput):
    try:
        # 1. Prediksi Kelas Utama Menggunakan AI MLP 96%
        raw_features = np.array([[input.ph, input.tds, input.suhu]])
        scaled_features = scaler.transform(raw_features)
        
        prediction = model.predict(scaled_features)
        predicted_class_index = np.argmax(prediction, axis=1)[0]
        kondisi_utama = label_encoder.inverse_transform([predicted_class_index])[0]
        
        # 2. Logika Hybrid & Perhitungan Health Score Berdasarkan Kondisi Riil Sawi Putih
        rekomendasi_gabungan = []
        status_terdeteksi = []
        skor_kesehatan = 100  # Mulai dari poin sempurna

        # Cek Masalah pH (Threshold Baru: 6.0 - 8.0)
        if input.ph < 6.0:
            rekomendasi_gabungan.extend(REKOMENDASI_BASE['pH Rendah'])
            status_terdeteksi.append('pH Rendah')
            skor_kesehatan -= 20
        elif input.ph > 8.0:
            rekomendasi_gabungan.extend(REKOMENDASI_BASE['pH Tinggi'])
            status_terdeteksi.append('pH Tinggi')
            skor_kesehatan -= 15

        # Cek Masalah TDS (Threshold Baru: 400 - 1200 ppm)
        if input.tds < 400:
            rekomendasi_gabungan.extend(REKOMENDASI_BASE['Nutrisi Kurang'])
            status_terdeteksi.append('Nutrisi Kurang')
            skor_kesehatan -= 25
        elif input.tds > 1200:
            rekomendasi_gabungan.extend(REKOMENDASI_BASE['Nutrisi Berlebih'])
            status_terdeteksi.append('Nutrisi Berlebih')
            skor_kesehatan -= 15

        # Cek Masalah Suhu (Threshold Baru: 20 - 28 °C)
        if input.suhu < 20 or input.suhu > 28:
            rekomendasi_gabungan.extend(REKOMENDASI_BASE['Suhu Tidak Ideal'])
            status_terdeteksi.append('Suhu Tidak Ideal')
            skor_kesehatan -= 20

        # Kunci skor kesehatan agar tidak minus
        health_score_final = max(0, skor_kesehatan)

        # 3. Finalisasi Status Kondisi
        if not status_terdeteksi:
            kondisi_final = "Normal"
            rekomendasi_gabungan = REKOMENDASI_BASE['Normal']
            status_tanaman = "Optimal"
        else:
            kondisi_final = " + ".join(status_terdeteksi)
            status_tanaman = "Sedang" if health_score_final >= 50 else "Buruk"

        # 4. Return Output (Sekarang menyertakan data Health Score untuk Laravel)
        return {
            "status": "success",
            "kondisi": kondisi_final,
            "confidence": round(float(np.max(prediction)) * 100, 2),
            "rekomendasi": rekomendasi_gabungan,
            "health_score": health_score_final,      # Amunisi baru biar web gak stuck!
            "status_tanaman": status_tanaman         # Status Teks: Optimal / Sedang / Buruk
        }
    except Exception as e:
        raise HTTPException(status_code=400, detail=str(e))

if __name__ == '__main__':
    import os
    import uvicorn
    port = int(os.environ.get("PORT", 8001))
    uvicorn.run("main:app", host="0.0.0.0", port=port, reload=False)
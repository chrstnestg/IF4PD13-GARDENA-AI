from fastapi import FastAPI
from pydantic import BaseModel
import numpy as np
import joblib
from tensorflow.keras.models import load_model

app = FastAPI()

model  = load_model('model_lstm.h5')
scaler = joblib.load('scaler.pkl')

LABELS = [
    'Normal',            # 0
    'Nutrisi Berlebih',  # 1
    'Nutrisi Kurang',    # 2
    'Suhu Tidak Ideal',  # 3
    'pH Rendah',         # 4
    'pH Tinggi'          # 5
]

REKOMENDASI = {
    'Normal': [
        'Kondisi nutrisi optimal, pertahankan kadar TDS saat ini.',
        'Lakukan pengecekan rutin setiap hari.',
    ],
    'Nutrisi Kurang': [
        'Tambahkan larutan nutrisi AB Mix ke dalam tangki.',
        'Cek TDS hingga mencapai minimal 1000 ppm.',
    ],
    'Nutrisi Berlebih': [
        'Encerkan larutan dengan menambahkan air bersih.',
        'Cek TDS hingga di bawah 1500 ppm.',
    ],
    'pH Rendah': [
        'Tambahkan larutan pH Up secara bertahap.',
        'Target pH antara 5.5 hingga 6.5.',
    ],
    'pH Tinggi': [
        'Tambahkan larutan pH Down secara bertahap.',
        'Target pH antara 5.5 hingga 6.5.',
    ],
    'Suhu Tidak Ideal': [
        'Periksa sirkulasi air dan ventilasi area tanam.',
        'Pastikan suhu air berada di antara 18–25°C.',
    ],
}

class SensorInput(BaseModel):
    data: list  # [[ph, tds, suhu], ...] 20 baris

@app.post("/predict")
def predict(input: SensorInput):
    arr        = np.array(input.data, dtype=float)
    arr_scaled = scaler.transform(arr)
    arr_scaled = arr_scaled.reshape(1, arr_scaled.shape[0], arr_scaled.shape[1])

    pred      = model.predict(arr_scaled)
    label_idx = int(np.argmax(pred))
    kondisi   = LABELS[label_idx]

    return {
        "kondisi"    : kondisi,
        "confidence" : round(float(np.max(pred)) * 100, 2),
        "rekomendasi": REKOMENDASI[kondisi]
    }
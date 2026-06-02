# Kontrak API Mobile

## Register
POST /api/mobile/auth/register

```json
{
  "display_name": "Reza Efendi",
  "username": "rezaef",
  "email": "reza@example.com",
  "password": "secret123",
  "password_confirmation": "secret123"
}
```

## Login
POST /api/mobile/auth/login

```json
{
  "identifier": "rezaef",
  "password": "secret123"
}
```

## Authorization header
Authorization: Bearer {token}
Accept: application/json

## Simpan klasifikasi
POST /api/mobile/classifications

```json
{
  "image_path": "classifications/17123.jpg",
  "category": "organik",
  "confidence": 0.964,
  "organic_score": 0.964,
  "anorganic_score": 0.031,
  "unknown_score": 0.005,
  "engine": "tensorflow-lite",
  "latency_ms": 127,
  "detected_at": "2026-04-22T09:10:00+07:00"
}
```

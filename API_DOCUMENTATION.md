# API Documentation - LensHub

Dokumentasi lengkap untuk semua API endpoint LensHub project.

## Base URL
```
http://your-domain.com/api
```

## Response Format

Semua response menggunakan format JSON:

```json
{
  "status": "success|error",
  "message": "Deskripsi hasil operasi",
  "data": {}
}
```

## Authentication

API menggunakan Sanctum untuk authentication. 

### Authorization Header
```
Authorization: Bearer YOUR_API_TOKEN
```

---

## 1. AUTHENTICATION ENDPOINTS

### Register User
**POST** `/auth/register`

**Public** - Tidak perlu token

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user|admin|owner"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "User berhasil terdaftar",
  "data": {
    "user": {...},
    "token": "API_TOKEN_HERE"
  }
}
```

---

### Login
**POST** `/auth/login`

**Public** - Tidak perlu token

**Request:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Login berhasil",
  "data": {
    "user": {...},
    "token": "API_TOKEN_HERE"
  }
}
```

---

### Verify Token
**POST** `/auth/verify-token`

**Public** - Tidak perlu token

**Response:**
```json
{
  "status": "success",
  "message": "Token valid",
  "data": {...user...}
}
```

---

### Get Current User
**GET** `/auth/me`

**Protected**

**Response:**
```json
{
  "status": "success",
  "message": "Data user berhasil diambil",
  "data": {...user...}
}
```

---

### Logout
**POST** `/auth/logout`

**Protected**

**Response:**
```json
{
  "status": "success",
  "message": "Logout berhasil"
}
```

---

### Logout All Devices
**POST** `/auth/logout-all`

**Protected** - Menghapus semua token aktif

**Response:**
```json
{
  "status": "success",
  "message": "Logout dari semua device berhasil"
}
```

---

### Refresh Token
**POST** `/auth/refresh-token`

**Protected**

**Response:**
```json
{
  "status": "success",
  "message": "Token berhasil diperbarui",
  "data": {
    "token": "NEW_TOKEN_HERE"
  }
}
```

---

## 2. USER ENDPOINTS

### Get All Users
**GET** `/users`

**Protected** - Admin only

**Query Parameters:**
- `role` (optional): Filter by role (admin|owner|user)
- `search` (optional): Search by name or email
- `per_page` (optional): Default 15

**Response:**
```json
{
  "status": "success",
  "message": "Daftar user berhasil diambil",
  "data": {
    "data": [...],
    "current_page": 1,
    "per_page": 15,
    "total": 50
  }
}
```

---

### Get User by ID
**GET** `/users/{id}`

**Protected**

**Response:**
```json
{
  "status": "success",
  "message": "Detail user berhasil diambil",
  "data": {...user...}
}
```

---

### Get Users by Role
**GET** `/users/role/{role}`

**Protected**

**URL Parameters:**
- `role`: admin|owner|user

**Response:**
```json
{
  "status": "success",
  "message": "User dengan role admin berhasil diambil",
  "data": [...]
}
```

---

### Update User
**PUT** `/users/{id}`

**Protected**

**Request:**
```json
{
  "name": "New Name",
  "email": "newemail@example.com",
  "phone": "08123456789",
  "address": "Jl. Contoh No. 123"
}
```

---

### Update Password
**PATCH** `/users/{id}/password`

**Protected**

**Request:**
```json
{
  "current_password": "oldpassword",
  "password": "newpassword",
  "password_confirmation": "newpassword"
}
```

---

### Delete User
**DELETE** `/users/{id}`

**Protected** - Admin only

---

## 3. CATEGORY ENDPOINTS

### Get All Categories
**GET** `/categories`

**Protected**

**Query Parameters:**
- `search` (optional): Search by name
- `per_page` (optional): Default 15

---

### Get Category Detail
**GET** `/categories/{id}`

**Protected**

**Response includes:** Semua gear dalam kategori

---

### Get Gears in Category
**GET** `/categories/{id}/gears`

**Protected**

---

### Create Category
**POST** `/categories`

**Protected** - Admin only

**Request:**
```json
{
  "name": "Camera",
  "prefix": "CAM",
  "description": "Photography cameras"
}
```

---

### Update Category
**PUT** `/categories/{id}`

**Protected** - Admin only

---

### Delete Category
**DELETE** `/categories/{id}`

**Protected** - Admin only (Hanya jika tidak ada gear)

---

## 4. GEAR ENDPOINTS

### Get All Gears
**GET** `/gears`

**Protected**

**Query Parameters:**
- `category_id` (optional): Filter by category
- `status` (optional): available|rented|maintenance
- `condition_status` (optional): baik|rusak|hilang
- `per_page` (optional): Default 15

---

### Get Gear Detail
**GET** `/gears/{id}`

**Protected**

**Response includes:** Category, rental history

---

### Get Gear Condition History
**GET** `/gears/{id}/condition-history`

**Protected**

**Response:**
```json
{
  "status": "success",
  "message": "Riwayat kondisi gear berhasil diambil",
  "data": [
    {
      "id": 1,
      "gear_id": 5,
      "old_condition": "baik",
      "new_condition": "rusak",
      "notes": "Lensa retak",
      "changed_by": {...user...},
      "created_at": "2026-05-13T10:00:00Z"
    }
  ]
}
```

---

### Create Gear
**POST** `/gears`

**Protected** - Admin only

**Request (multipart/form-data):**
```json
{
  "category_id": 1,
  "name": "Canon EOS R6",
  "rent_price": 50000,
  "penalty_fee": 10000,
  "description": "Professional DSLR",
  "photo": <file>
}
```

**Response:** Auto-generate unit_code berdasarkan kategori

---

### Update Gear
**PUT** `/gears/{id}`

**Protected** - Admin only

**Request:**
```json
{
  "name": "Updated Name",
  "rent_price": 60000,
  "penalty_fee": 12000,
  "description": "New description",
  "photo": <file optional>
}
```

---

### Update Gear Status
**PATCH** `/gears/{id}/status`

**Protected** - Admin only

**Request:**
```json
{
  "status": "available|rented|maintenance"
}
```

---

### Update Gear Condition
**PATCH** `/gears/{id}/condition`

**Protected**

**Request:**
```json
{
  "condition": "baik|rusak|hilang",
  "notes": "Lensa baret di bagian depan"
}
```

**Behavior:**
- Auto-update gear status ke 'maintenance' jika kondisi tidak 'baik'
- Catat history ke GearConditionLog

---

### Duplicate Gear
**POST** `/gears/{id}/duplicate`

**Protected** - Admin only

**Response:** 
- Generate unit_code baru
- Set status ke 'available'
- Copy semua field dari gear original

---

### Delete Gear
**DELETE** `/gears/{id}`

**Protected** - Admin only

**Note:** Menggunakan Soft Delete (tidak permanent dihapus)

---

## 5. RENTAL ENDPOINTS

### Get All Rentals
**GET** `/rentals`

**Protected**

**Query Parameters:**
- `status` (optional): booking|active|completed|cancelled
- `user_id` (optional): Filter by user
- `gear_id` (optional): Filter by gear
- `start_date` (optional): Format: YYYY-MM-DD
- `end_date` (optional): Format: YYYY-MM-DD
- `per_page` (optional): Default 15

---

### Get Rental Detail
**GET** `/rentals/{id}`

**Protected**

**Response includes:**
```json
{
  "data": {...},
  "penalty_details": {
    "is_late": true|false,
    "days": 2,
    "total": 20000
  },
  "grand_total": 120000
}
```

---

### Get Dashboard Statistics
**GET** `/rentals/stats/dashboard`

**Protected**

**Response:**
```json
{
  "status": "success",
  "data": {
    "booking_today": 5,
    "return_today": 3,
    "overdue": 2,
    "total_booking": 15,
    "total_active": 8,
    "total_completed": 45,
    "total_cancelled": 2
  }
}
```

---

### Get User Rentals
**GET** `/rentals/user/{userId}`

**Protected**

**Response:** Array of rental data untuk user spesifik

---

### Get Gear Rentals
**GET** `/rentals/gear/{gearId}`

**Protected**

**Response:** Array of rental yang booking/active untuk gear spesifik

---

### Create Rental (Booking)
**POST** `/rentals`

**Protected** - User (penyewa)

**Request (multipart/form-data):**
```json
{
  "gear_id": 5,
  "start_date": "2026-05-15",
  "start_time": "09:00",
  "duration": 3,
  "whatsapp": "08123456789",
  "alamat": "Jl. Contoh No. 123",
  "purpose": "Dokumentasi Event",
  "payment_method": "cash|transfer|card",
  "foto_ktp": <file>
}
```

**Behavior:**
- Auto-check overlapping bookings
- Auto-calculate end_date
- Auto-calculate total_price
- Set status ke 'booking'
- Auto-generate booking_code

---

### Update Rental
**PUT** `/rentals/{id}`

**Protected** - Owner & Admin

**Note:** Hanya rental dengan status 'booking' yang bisa diupdate

**Request:**
```json
{
  "start_date": "2026-05-15",
  "duration": 4,
  "whatsapp": "08123456789",
  "alamat": "Jl. Baru No. 456",
  "purpose": "Fotografi Pernikahan"
}
```

---

### Confirm Payment
**PATCH** `/rentals/{id}/confirm-payment`

**Protected** - Admin only

**Behavior:**
- Change status dari 'booking' ke 'active'
- Update gear status ke 'rented'
- Add note dengan info admin

---

### Complete Rental
**PATCH** `/rentals/{id}/complete`

**Protected** - Admin only

**Behavior:**
- Change status ke 'completed'
- Auto-calculate penalty jika ada keterlambatan
- Calculate final_amount
- Update gear status ke 'available'
- Return penalty_info dalam response

**Response:**
```json
{
  "status": "success",
  "data": {...rental...},
  "penalty_info": {
    "is_late": true,
    "days_late": 2,
    "penalty_fee": 20000,
    "final_amount": 120000
  }
}
```

---

### Cancel Rental
**PATCH** `/rentals/{id}/cancel`

**Protected** - Admin only

**Note:** Hanya rental dengan status 'booking' atau 'active'

---

### Download KTP
**GET** `/rentals/{id}/ktp`

**Protected**

**Response:** File download (image)

---

### Delete Rental
**DELETE** `/rentals/{id}`

**Protected** - Admin only

---

## Error Responses

### Validation Error (422)
```json
{
  "status": "error",
  "message": "Validasi gagal",
  "errors": {
    "field_name": ["Error message"]
  }
}
```

### Unauthorized (401)
```json
{
  "status": "error",
  "message": "Unauthorized"
}
```

### Forbidden (403)
```json
{
  "status": "error",
  "message": "Forbidden - Anda tidak memiliki akses"
}
```

### Not Found (404)
```json
{
  "status": "error",
  "message": "Resource tidak ditemukan"
}
```

### Server Error (500)
```json
{
  "status": "error",
  "message": "Server error: Deskripsi error"
}
```

---

## Rate Limiting

Tidak ada rate limiting di production (silakan implement sesuai kebutuhan).

---

## Pagination

Endpoints yang return collection akan di-paginate dengan default 15 items per page.

**Query parameter:** `per_page`

**Response includes:**
- `data`: Array of items
- `current_page`: Page number
- `per_page`: Items per page
- `total`: Total items
- `last_page`: Last page number

---

## Example Requests

### cURL - Register
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "user"
  }'
```

### cURL - Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

### cURL - Get Gears with Token
```bash
curl -X GET http://localhost:8000/api/gears \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### cURL - Create Rental
```bash
curl -X POST http://localhost:8000/api/rentals \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "gear_id=5" \
  -F "start_date=2026-05-15" \
  -F "start_time=09:00" \
  -F "duration=3" \
  -F "whatsapp=08123456789" \
  -F "alamat=Jl. Contoh No. 123" \
  -F "purpose=Dokumentasi" \
  -F "payment_method=transfer" \
  -F "foto_ktp=@/path/to/ktp.jpg"
```

---

## Notes

- Semua timestamp dalam format ISO 8601 UTC
- Token expiry dapat dikonfigurasi di `.env` (SANCTUM_EXPIRATION)
- Soft delete berlaku untuk gear, user dapat restore jika perlu
- Penalty otomatis dihitung dengan grace period 2 jam setelah end_date
- KTP disimpan di storage/app/private untuk keamanan

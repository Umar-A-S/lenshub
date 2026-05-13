# LensHub API Testing & Examples

## Environment Variables Setup

### .env Configuration
```env
SANCTUM_EXPIRATION=525600  # Token expiry in minutes (optional)
API_RATE_LIMIT=60          # Requests per minute (optional)
```

---

## Testing Flow

### Step 1: Register New User
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john.doe@example.com",
    "password": "SecurePassword123",
    "password_confirmation": "SecurePassword123",
    "role": "user"
  }'
```

**Success Response:**
```json
{
  "status": "success",
  "message": "User berhasil terdaftar",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "role": "user",
      "created_at": "2026-05-13T10:00:00Z"
    },
    "token": "1|abc123xyz..."
  }
}
```

Save the `token` for subsequent requests.

---

### Step 2: Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john.doe@example.com",
    "password": "SecurePassword123"
  }'
```

---

### Step 3: Get Current User Data
```bash
curl -X GET http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer 1|abc123xyz..."
```

---

## Gear Management Examples

### Get All Available Gears
```bash
curl -X GET "http://localhost:8000/api/gears?status=available&condition_status=baik&per_page=10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Specific Gear Details
```bash
curl -X GET http://localhost:8000/api/gears/5 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create New Gear (Admin Only)
```bash
curl -X POST http://localhost:8000/api/gears \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -F "category_id=1" \
  -F "name=Canon EOS R6" \
  -F "rent_price=50000" \
  -F "penalty_fee=10000" \
  -F "description=Professional DSLR Camera" \
  -F "photo=@/path/to/camera.jpg"
```

### Update Gear
```bash
curl -X PUT http://localhost:8000/api/gears/5 \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Canon EOS R5",
    "rent_price": 60000,
    "penalty_fee": 12000
  }'
```

### Update Gear Status
```bash
curl -X PATCH http://localhost:8000/api/gears/5/status \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "maintenance"
  }'
```

### Update Gear Condition
```bash
curl -X PATCH http://localhost:8000/api/gears/5/condition \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "condition": "rusak",
    "notes": "Lensa depan baret, CF card slot rusak"
  }'
```

### Get Gear Condition History
```bash
curl -X GET http://localhost:8000/api/gears/5/condition-history \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Duplicate Gear (Add Stock)
```bash
curl -X POST http://localhost:8000/api/gears/5/duplicate \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

---

## Category Management Examples

### Get All Categories
```bash
curl -X GET http://localhost:8000/api/categories \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Create Category (Admin Only)
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Photography Cameras",
    "prefix": "CAM",
    "description": "Professional and amateur cameras"
  }'
```

---

## Rental Booking Examples

### Get Available Gears for Booking
```bash
curl -X GET "http://localhost:8000/api/gears?status=available&condition_status=baik" \
  -H "Authorization: Bearer USER_TOKEN"
```

### Create Rental Booking
```bash
curl -X POST http://localhost:8000/api/rentals \
  -H "Authorization: Bearer USER_TOKEN" \
  -F "gear_id=5" \
  -F "start_date=2026-05-20" \
  -F "start_time=09:00" \
  -F "duration=3" \
  -F "whatsapp=08123456789" \
  -F "alamat=Jl. Sudirman No. 123, Jakarta" \
  -F "purpose=Dokumentasi Acara Pernikahan" \
  -F "payment_method=transfer" \
  -F "foto_ktp=@/path/to/ktp.jpg"
```

**Response:**
```json
{
  "status": "success",
  "message": "Rental berhasil dibuat dengan status booking",
  "data": {
    "id": 1,
    "booking_code": "RENT-20260513-ABC1",
    "gear_id": 5,
    "user_id": 1,
    "start_date": "2026-05-20T09:00:00Z",
    "end_date": "2026-05-23T09:00:00Z",
    "duration": 3,
    "total_price": 150000,
    "status": "booking",
    "created_at": "2026-05-13T10:00:00Z"
  }
}
```

### Get Rental Details
```bash
curl -X GET http://localhost:8000/api/rentals/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get User's Rentals
```bash
curl -X GET http://localhost:8000/api/rentals/user/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Rental Schedule for Gear
```bash
curl -X GET http://localhost:8000/api/rentals/gear/5 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Update Rental (Before Activated)
```bash
curl -X PUT http://localhost:8000/api/rentals/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "duration": 4,
    "whatsapp": "08123456789",
    "purpose": "Dokumentasi + Cinematic Video"
  }'
```

### Admin: Confirm Payment & Activate Rental
```bash
curl -X PATCH http://localhost:8000/api/rentals/1/confirm-payment \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Admin: Complete Rental (Customer Returns Gear)
```bash
curl -X PATCH http://localhost:8000/api/rentals/1/complete \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

**Response (with penalty calculation):**
```json
{
  "status": "success",
  "message": "Rental berhasil diselesaikan",
  "data": {...rental details...},
  "penalty_info": {
    "is_late": true,
    "days_late": 2,
    "penalty_fee": 20000,
    "final_amount": 170000
  }
}
```

### Admin: Cancel Rental
```bash
curl -X PATCH http://localhost:8000/api/rentals/1/cancel \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Download KTP from Rental
```bash
curl -X GET http://localhost:8000/api/rentals/1/ktp \
  -H "Authorization: Bearer ADMIN_TOKEN" \
  -o ktp_rental_1.jpg
```

### Get Dashboard Statistics
```bash
curl -X GET http://localhost:8000/api/rentals/stats/dashboard \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

**Response:**
```json
{
  "status": "success",
  "message": "Statistik dashboard berhasil diambil",
  "data": {
    "booking_today": 5,
    "return_today": 3,
    "overdue": 1,
    "total_booking": 15,
    "total_active": 8,
    "total_completed": 145,
    "total_cancelled": 3
  }
}
```

---

## User Management Examples (Admin Only)

### Get All Users
```bash
curl -X GET "http://localhost:8000/api/users?role=user&per_page=20" \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Get Users by Role
```bash
curl -X GET http://localhost:8000/api/users/role/admin \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Get Specific User
```bash
curl -X GET http://localhost:8000/api/users/5 \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

### Update User Profile
```bash
curl -X PUT http://localhost:8000/api/users/5 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Updated",
    "phone": "08123456789",
    "address": "Jl. Baru No. 456"
  }'
```

### Change Password
```bash
curl -X PATCH http://localhost:8000/api/users/5/password \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "OldPassword123",
    "password": "NewPassword123",
    "password_confirmation": "NewPassword123"
  }'
```

### Delete User (Admin Only)
```bash
curl -X DELETE http://localhost:8000/api/users/5 \
  -H "Authorization: Bearer ADMIN_TOKEN"
```

---

## Token Management Examples

### Refresh Token
```bash
curl -X POST http://localhost:8000/api/auth/refresh-token \
  -H "Authorization: Bearer YOUR_OLD_TOKEN"
```

### Logout (Single Device)
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Logout All Devices
```bash
curl -X POST http://localhost:8000/api/auth/logout-all \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Verify Token
```bash
curl -X POST http://localhost:8000/api/auth/verify-token \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## PHP/Laravel Code Examples

### Using Guzzle Client
```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'http://localhost:8000/api']);

// Login
$response = $client->post('auth/login', [
    'json' => [
        'email' => 'john@example.com',
        'password' => 'password123'
    ]
]);

$data = json_decode($response->getBody(), true);
$token = $data['data']['token'];

// Get gears
$response = $client->get('gears', [
    'headers' => ['Authorization' => "Bearer $token"]
]);

$gears = json_decode($response->getBody(), true);
```

### Using Laravel HTTP Client
```php
use Illuminate\Support\Facades\Http;

// Login
$response = Http::post('http://localhost:8000/api/auth/login', [
    'email' => 'john@example.com',
    'password' => 'password123'
]);

$token = $response['data']['token'];

// Get gears
$gears = Http::withToken($token)
    ->get('http://localhost:8000/api/gears')
    ->json();

// Create rental
$rental = Http::withToken($token)
    ->attach('foto_ktp', fopen('ktp.jpg', 'r'))
    ->post('http://localhost:8000/api/rentals', [
        'gear_id' => 5,
        'start_date' => '2026-05-20',
        'start_time' => '09:00',
        'duration' => 3,
        'whatsapp' => '08123456789',
        'alamat' => 'Jl. Contoh',
        'purpose' => 'Dokumentasi',
        'payment_method' => 'transfer'
    ])
    ->json();
```

### Using axios (JavaScript)
```javascript
const axios = require('axios');

const api = axios.create({
  baseURL: 'http://localhost:8000/api'
});

// Login
const loginRes = await api.post('/auth/login', {
  email: 'john@example.com',
  password: 'password123'
});

const token = loginRes.data.data.token;

// Set default auth header
api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

// Get gears
const gearsRes = await api.get('/gears?status=available');
console.log(gearsRes.data.data);

// Create rental
const formData = new FormData();
formData.append('gear_id', 5);
formData.append('start_date', '2026-05-20');
formData.append('start_time', '09:00');
formData.append('duration', 3);
formData.append('whatsapp', '08123456789');
formData.append('alamat', 'Jl. Contoh');
formData.append('purpose', 'Dokumentasi');
formData.append('payment_method', 'transfer');
formData.append('foto_ktp', fileInput.files[0]);

const rentalRes = await api.post('/rentals', formData, {
  headers: { 'Content-Type': 'multipart/form-data' }
});
```

---

## Common Error Responses

### 401 Unauthorized
```json
{
  "status": "error",
  "message": "Email atau password salah"
}
```

### 422 Validation Error
```json
{
  "status": "error",
  "message": "Validasi gagal",
  "errors": {
    "start_date": ["The start date field is required."],
    "duration": ["The duration must be at least 1."]
  }
}
```

### 400 Bad Request
```json
{
  "status": "error",
  "message": "Jadwal alat sudah terisi pada tanggal tersebut"
}
```

### 403 Forbidden
```json
{
  "status": "error",
  "message": "Unauthorized - hanya admin yang dapat mengakses"
}
```

---

## Development Tips

1. **Use Postman** for testing - Import endpoints and organize by resources
2. **Log tokens** during development for easier testing
3. **Use environment variables** for base_url and token
4. **Test error scenarios** - Wrong data, missing fields, invalid IDs
5. **Check timestamps** - All times are in UTC/ISO 8601 format
6. **Monitor file uploads** - Max size 2MB, check MIME types
7. **Validate role-based access** - Test with different user roles

---

## Performance Optimization

- Use `per_page` parameter to limit results
- Add appropriate filters to reduce data transfer
- Use pagination for large datasets
- Cache responses in client when appropriate
- Use conditional requests with ETags (future enhancement)

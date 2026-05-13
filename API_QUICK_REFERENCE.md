# LensHub API Quick Reference

## Environment Setup

1. **Install Sanctum** (jika belum):
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

2. **Generate API Token** (untuk testing):
   ```bash
   php artisan tinker
   > $user = User::first();
   > $token = $user->createToken('api_token')->plainTextToken;
   > echo $token;
   ```

---

## Base URL
```
http://localhost:8000/api
```

---

## Public Endpoints (Tidak perlu token)

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/auth/register` | Register user baru |
| POST | `/auth/login` | Login & dapatkan token |
| POST | `/auth/verify-token` | Verifikasi token valid |

---

## Protected Endpoints (Perlu token di header)

### Authentication
| Method | Endpoint | Akses |
|--------|----------|-------|
| GET | `/auth/me` | Public (User) |
| POST | `/auth/logout` | Public (User) |
| POST | `/auth/logout-all` | Public (User) |
| POST | `/auth/refresh-token` | Public (User) |

### Users
| Method | Endpoint | Akses |
|--------|----------|-------|
| GET | `/users` | Admin |
| GET | `/users/me` | Public |
| GET | `/users/{id}` | Own Profile / Admin |
| GET | `/users/role/{role}` | Public |
| PUT | `/users/{id}` | Own Profile / Admin |
| PATCH | `/users/{id}/password` | Own Profile |
| DELETE | `/users/{id}` | Admin |

### Categories
| Method | Endpoint | Akses |
|--------|----------|-------|
| GET | `/categories` | Public |
| GET | `/categories/{id}` | Public |
| GET | `/categories/{id}/gears` | Public |
| POST | `/categories` | Admin |
| PUT | `/categories/{id}` | Admin |
| DELETE | `/categories/{id}` | Admin |

### Gears
| Method | Endpoint | Akses |
|--------|----------|-------|
| GET | `/gears` | Public |
| GET | `/gears/{id}` | Public |
| GET | `/gears/{id}/condition-history` | Public |
| POST | `/gears` | Admin |
| PUT | `/gears/{id}` | Admin |
| DELETE | `/gears/{id}` | Admin |
| PATCH | `/gears/{id}/status` | Admin |
| PATCH | `/gears/{id}/condition` | Public |
| POST | `/gears/{id}/duplicate` | Admin |

### Rentals
| Method | Endpoint | Akses |
|--------|----------|-------|
| GET | `/rentals` | Admin |
| GET | `/rentals/{id}` | Own Rental / Admin |
| GET | `/rentals/stats/dashboard` | Admin |
| GET | `/rentals/user/{userId}` | Own Rentals / Admin |
| GET | `/rentals/gear/{gearId}` | Public |
| GET | `/rentals/{id}/ktp` | Admin |
| POST | `/rentals` | User (Booking) |
| PUT | `/rentals/{id}` | Owner / Admin (Booking only) |
| PATCH | `/rentals/{id}/confirm-payment` | Admin |
| PATCH | `/rentals/{id}/complete` | Admin |
| PATCH | `/rentals/{id}/cancel` | Admin |
| DELETE | `/rentals/{id}` | Admin |

---

## Request Header Template

```
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json
```

---

## Response Format

**Success:**
```json
{
  "status": "success",
  "message": "Deskripsi berhasil",
  "data": {...}
}
```

**Error:**
```json
{
  "status": "error",
  "message": "Deskripsi error",
  "errors": {...}
}
```

---

## Status Codes

| Code | Meaning |
|------|---------|
| 200 | OK - Request successful |
| 201 | Created - Resource created |
| 400 | Bad Request - Invalid input |
| 401 | Unauthorized - Invalid/missing token |
| 403 | Forbidden - No permission |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 500 | Server Error |

---

## Common Workflows

### 1. User Registration & Login
```bash
# Register
POST /api/auth/register
{
  "name": "John",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user"
}

# Login
POST /api/auth/login
{
  "email": "john@example.com",
  "password": "password123"
}
# Response includes token
```

### 2. Viewing Available Gears
```bash
GET /api/gears?status=available&condition_status=baik
Headers: Authorization: Bearer TOKEN
```

### 3. Creating a Rental Booking
```bash
POST /api/rentals
Headers: Authorization: Bearer TOKEN
Body (multipart/form-data):
{
  "gear_id": 5,
  "start_date": "2026-05-20",
  "start_time": "09:00",
  "duration": 3,
  "whatsapp": "08123456789",
  "alamat": "Jl. Contoh",
  "purpose": "Dokumentasi",
  "payment_method": "transfer",
  "foto_ktp": <file>
}
```

### 4. Admin Confirming Rental Payment
```bash
PATCH /api/rentals/123/confirm-payment
Headers: Authorization: Bearer ADMIN_TOKEN
# Changes status from 'booking' to 'active'
```

### 5. Admin Completing Rental
```bash
PATCH /api/rentals/123/complete
Headers: Authorization: Bearer ADMIN_TOKEN
# Auto-calculates penalty & finalizes
```

### 6. Update Gear Condition
```bash
PATCH /api/gears/5/condition
Headers: Authorization: Bearer TOKEN
Body:
{
  "condition": "rusak",
  "notes": "Lensa retak di bagian depan"
}
# Auto logs to GearConditionLog
```

### 7. Getting Dashboard Stats
```bash
GET /api/rentals/stats/dashboard
Headers: Authorization: Bearer ADMIN_TOKEN
# Returns booking, active, overdue counts, etc
```

---

## Useful Query Parameters

### Pagination
```
?per_page=20  # Default: 15
```

### Filtering Gears
```
?category_id=1
?status=available
?condition_status=baik
```

### Filtering Rentals
```
?status=active
?user_id=5
?gear_id=3
?start_date=2026-05-01&end_date=2026-05-31
```

### Search Users
```
?search=john
```

---

## Testing Tools

### Postman Collection
Create collection with:
- Environment: `{{"base_url": "http://localhost:8000/api"}}`
- Variables: `{{token}}` - Set after login

### cURL Examples

**Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

**Get Gears:**
```bash
curl -X GET http://localhost:8000/api/gears \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Create Rental:**
```bash
curl -X POST http://localhost:8000/api/rentals \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -F "gear_id=5" \
  -F "start_date=2026-05-20" \
  -F "start_time=09:00" \
  -F "duration=3" \
  -F "whatsapp=08123456789" \
  -F "alamat=Jl. Contoh" \
  -F "purpose=Dokumentasi" \
  -F "payment_method=transfer" \
  -F "foto_ktp=@ktp.jpg"
```

---

## Important Notes

1. **Token Expiry**: Tokens don't expire by default in Sanctum. Configure in `.env` if needed.

2. **File Uploads**: Use `multipart/form-data` for endpoints with file uploads (gears, rentals, etc)

3. **Timezone**: All timestamps in UTC (ISO 8601 format)

4. **Soft Delete**: Gears use soft delete - can be restored if needed

5. **Penalty Calculation**: 
   - Grace period: 2 hours after end_date
   - Calculated daily in complete endpoint
   - formula: (days_late × gear.penalty_fee)

6. **Authorization by Role**:
   - Admin: Full access
   - Owner: Limited access to own resources
   - User: Can only book rentals

---

## Troubleshooting

### 401 Unauthorized
- Token expired or invalid
- Token not in Authorization header
- Wrong token format

### 403 Forbidden
- User doesn't have permission for action
- Check user role

### 422 Validation Error
- Check required fields
- Check field formats
- Check enum values (status, role, etc)

### File Upload Issues
- Use multipart/form-data
- File size < 2MB
- Supported formats: jpg, jpeg, png, gif, webp

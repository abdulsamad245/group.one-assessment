#!/bin/bash

# Test API Script for License Service
# This script tests ALL uncommented API endpoints
# Uses seeded data for license operations (license CRUD routes are commented out)

BASE_URL="http://localhost/api/v1"

# Helper function to extract JSON values (simple grep-based)
extract_json() {
  local key=$1
  local json=$2
  echo "$json" | grep -o "\"$key\":\"[^\"]*\"" | head -1 | sed "s/\"$key\":\"\([^\"]*\)\"/\1/"
}

echo "=============================================="
echo "   License Service API Tests"
echo "   Testing ALL Uncommented Endpoints"
echo "=============================================="
echo ""

# ==========================================
# PUBLIC AUTH ROUTES
# ==========================================

# 1. Register (public) - Creates a new user and brand
RANDOM_SUFFIX=$RANDOM
echo "1. REGISTER NEW USER"
echo "   POST /v1/auth/register"
echo "   -------------------------------------------"
REGISTER_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{
    \"name\": \"Test User $RANDOM_SUFFIX\",
    \"email\": \"testuser$RANDOM_SUFFIX@mailinator.com\",
    \"password\": \"password123\",
    \"password_confirmation\": \"password123\",
    \"brand_name\": \"Test Brand $RANDOM_SUFFIX\"
  }")
echo "$REGISTER_RESPONSE"
echo ""

# 2. Login to get auth token
echo "2. LOGIN"
echo "   POST /v1/auth/login"
echo "   -------------------------------------------"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@wp-rocket.me", "password": "password"}')
echo "$LOGIN_RESPONSE"
TOKEN=$(extract_json "token" "$LOGIN_RESPONSE")
echo ""
echo "   ✓ Token: ${TOKEN:0:40}..."
echo ""

# ==========================================
# SANCTUM AUTHENTICATED ROUTES
# ==========================================

# 3. Create API Key (requires Sanctum token)
echo "3. CREATE API KEY"
echo "   POST /v1/api-keys"
echo "   -------------------------------------------"
API_KEY_RESPONSE=$(curl -s -X POST "$BASE_URL/api-keys" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Test API Key"}')
echo "$API_KEY_RESPONSE"
API_KEY=$(extract_json "plain_key" "$API_KEY_RESPONSE")
if [ -z "$API_KEY" ]; then
  API_KEY=$(extract_json "key" "$API_KEY_RESPONSE")
fi
echo ""
echo "   ✓ API Key: ${API_KEY:0:30}..."
echo ""

# ==========================================
# PUBLIC LICENSE KEY ROUTES
# ==========================================

# 4. Get License Key by Key String (public endpoint)
# Use a seeded license key from LicenseSeeder (john@mailinator.com)
echo "4. GET LICENSE KEY BY KEY (using seeded data)"
echo "   GET /v1/license-keys/key/{key}"
echo "   -------------------------------------------"
# First, get a license key from customer licenses endpoint
CUSTOMER_RESPONSE=$(curl -s -X GET "$BASE_URL/customers/licenses?email=john@mailinator.com" \
  -H "X-API-Key: $API_KEY" \
  -H "Content-Type: application/json")
LICENSE_KEY=$(echo "$CUSTOMER_RESPONSE" | grep -o '"key":"[A-Z0-9-]*"' | head -1 | sed 's/"key":"\([^"]*\)"/\1/')
# Extract the license ID from the licenses array inside license_key (not the license_key id)
LICENSE_ID=$(echo "$CUSTOMER_RESPONSE" | grep -o '"licenses":\[{"id":"[^"]*"' | head -1 | sed 's/.*"id":"\([^"]*\)".*/\1/')
echo "   Using seeded license key: $LICENSE_KEY"
echo "   Using license ID: $LICENSE_ID"
if [ -n "$LICENSE_KEY" ]; then
  LICENSE_KEY_RESPONSE=$(curl -s -X GET "$BASE_URL/license-keys/key/$LICENSE_KEY" \
    -H "Content-Type: application/json")
  echo "$LICENSE_KEY_RESPONSE"
  echo ""
  echo "   ✓ License key details retrieved"
else
  echo "   ✗ Skipping - No license key available"
fi
echo ""

# ==========================================
# PUBLIC ACTIVATION ROUTES
# ==========================================

# 5. Activate License (public endpoint)
echo "5. ACTIVATE LICENSE"
echo "   POST /v1/activations"
echo "   -------------------------------------------"
if [ -n "$LICENSE_KEY" ]; then
  ACTIVATE_RESPONSE=$(curl -s -X POST "$BASE_URL/activations" \
    -H "Content-Type: application/json" \
    -d "{
      \"license_key\": \"$LICENSE_KEY\",
      \"product_slug\": \"wp-rocket-pro\",
      \"instance_type\": \"site_url\",
      \"instance_value\": \"https://newsite.mailinator.com\"
    }")
  echo "$ACTIVATE_RESPONSE"
  ACTIVATION_ID=$(extract_json "id" "$ACTIVATE_RESPONSE")
  echo ""
  echo "   ✓ Activation ID: $ACTIVATION_ID"
else
  echo "   ✗ Skipping - No license key available"
fi
echo ""

# 6. Check Activation Status (public endpoint)
echo "6. CHECK ACTIVATION STATUS"
echo "   GET /v1/activations/status"
echo "   -------------------------------------------"
if [ -n "$LICENSE_KEY" ]; then
  curl -s -X GET "$BASE_URL/activations/status?license_key=$LICENSE_KEY&product_slug=wp-rocket-pro" \
    -H "Content-Type: application/json"
  echo ""
  echo "   ✓ Status checked"
else
  echo "   ✗ Skipping - No license key available"
fi
echo ""

# 7. Deactivate License (public endpoint)
echo "7. DEACTIVATE LICENSE"
echo "   POST /v1/deactivations"
echo "   -------------------------------------------"
if [ -n "$ACTIVATION_ID" ]; then
  DEACTIVATE_RESPONSE=$(curl -s -X POST "$BASE_URL/deactivations" \
    -H "Content-Type: application/json" \
    -d "{
      \"license_key\": \"$LICENSE_KEY\",
      \"activation_id\": \"$ACTIVATION_ID\"
    }")
  echo "$DEACTIVATE_RESPONSE"
  echo ""
  echo "   ✓ License deactivated"
else
  echo "   ✗ Skipping - No activation ID available"
fi
echo ""

# ==========================================
# API KEY AUTHENTICATED ROUTES
# ==========================================

# 8. Get Customer Licenses (requires API key)
echo "8. GET CUSTOMER LICENSES"
echo "   GET /v1/customers/licenses"
echo "   -------------------------------------------"
if [ -n "$API_KEY" ]; then
  curl -s -X GET "$BASE_URL/customers/licenses?email=john@mailinator.com" \
    -H "X-API-Key: $API_KEY" \
    -H "Content-Type: application/json"
  echo ""
  echo "   ✓ Customer licenses retrieved"
else
  echo "   ✗ Skipping - No API key available"
fi
echo ""

# 9. Renew License (requires API key)
echo "9. RENEW LICENSE"
echo "   POST /v1/licenses/{id}/renew"
echo "   -------------------------------------------"
if [ -n "$API_KEY" ] && [ -n "$LICENSE_ID" ]; then
  RENEW_RESPONSE=$(curl -s -X POST "$BASE_URL/licenses/$LICENSE_ID/renew" \
    -H "X-API-Key: $API_KEY" \
    -H "Content-Type: application/json" \
    -d '{"days": 365}')
  echo "$RENEW_RESPONSE"
  echo ""
  echo "   ✓ License renewed"
else
  echo "   ✗ Skipping - No API key or License ID available"
fi
echo ""

# 10. Suspend License (requires API key)
echo "10. SUSPEND LICENSE"
echo "   POST /v1/licenses/{id}/suspend"
echo "   -------------------------------------------"
if [ -n "$API_KEY" ] && [ -n "$LICENSE_ID" ]; then
  SUSPEND_RESPONSE=$(curl -s -X POST "$BASE_URL/licenses/$LICENSE_ID/suspend" \
    -H "X-API-Key: $API_KEY" \
    -H "Content-Type: application/json")
  echo "$SUSPEND_RESPONSE"
  echo ""
  echo "   ✓ License suspended"
else
  echo "   ✗ Skipping - No API key or License ID available"
fi
echo ""

# 11. Resume License (requires API key)
echo "11. RESUME LICENSE"
echo "   POST /v1/licenses/{id}/resume"
echo "   -------------------------------------------"
if [ -n "$API_KEY" ] && [ -n "$LICENSE_ID" ]; then
  RESUME_RESPONSE=$(curl -s -X POST "$BASE_URL/licenses/$LICENSE_ID/resume" \
    -H "X-API-Key: $API_KEY" \
    -H "Content-Type: application/json")
  echo "$RESUME_RESPONSE"
  echo ""
  echo "   ✓ License resumed"
else
  echo "   ✗ Skipping - No API key or License ID available"
fi
echo ""

# 12. Cancel License (requires API key)
echo "12. CANCEL LICENSE"
echo "   POST /v1/licenses/{id}/cancel"
echo "   -------------------------------------------"
if [ -n "$API_KEY" ] && [ -n "$LICENSE_ID" ]; then
  CANCEL_RESPONSE=$(curl -s -X POST "$BASE_URL/licenses/$LICENSE_ID/cancel" \
    -H "X-API-Key: $API_KEY" \
    -H "Content-Type: application/json")
  echo "$CANCEL_RESPONSE"
  echo ""
  echo "   ✓ License cancelled"
else
  echo "   ✗ Skipping - No API key or License ID available"
fi
echo ""

# ==========================================
# LOGOUT (requires Sanctum token)
# ==========================================

# 13. Logout
echo "13. LOGOUT"
echo "   POST /v1/auth/logout"
echo "   -------------------------------------------"
if [ -n "$TOKEN" ]; then
  LOGOUT_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/logout" \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json")
  echo "$LOGOUT_RESPONSE"
  echo ""
  echo "   ✓ Logged out"
else
  echo "   ✗ Skipping - No token available"
fi
echo ""

echo "=============================================="
echo "   Tests Complete"
echo "=============================================="


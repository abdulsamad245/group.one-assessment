# Critical Fixes Required - Action Plan

**Priority:** CRITICAL  
**Estimated Time:** 4-6 hours  
**Impact:** Brings compliance from 75% to 95%

---

## 🔴 FIX #1: Add US2 API Endpoints (CRITICAL)

**Problem:** Service methods exist but NO API endpoints for renew/suspend/resume/cancel

**Time:** 2 hours

### Step 1: Add Routes
```php
// File: routes/api.php
// Add after line 21:

Route::prefix('v1')->middleware('api.key')->group(function () {
    // ... existing routes ...
    
    // License lifecycle operations
    Route::post('licenses/{id}/renew', [LicenseController::class, 'renew']);
    Route::post('licenses/{id}/suspend', [LicenseController::class, 'suspend']);
    Route::post('licenses/{id}/resume', [LicenseController::class, 'resume']);
    Route::post('licenses/{id}/cancel', [LicenseController::class, 'cancel']);
});
```

### Step 2: Add Controller Methods
```php
// File: app/Http/Controllers/Api/V1/LicenseController.php
// Add these methods:

public function renew(Request $request, int $id): JsonResponse
{
    try {
        $license = $this->licenseRepository->findById($id);
        if (!$license) {
            return $this->notFound(__('messages.license-not-found'));
        }
        
        $days = $request->input('days', 365);
        $renewed = $this->licenseService->renewLicense($license, $days);
        
        return $this->response(Response::HTTP_OK, __('messages.license-renewed'), 
            new LicenseResource($renewed->load('brand')));
    } catch (Exception $e) {
        return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
    }
}

public function suspend(int $id): JsonResponse
{
    try {
        $license = $this->licenseRepository->findById($id);
        if (!$license) {
            return $this->notFound(__('messages.license-not-found'));
        }
        
        $suspended = $this->licenseService->suspendLicense($license);
        
        return $this->response(Response::HTTP_OK, __('messages.license-suspended'), 
            new LicenseResource($suspended->load('brand')));
    } catch (Exception $e) {
        return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
    }
}

public function resume(int $id): JsonResponse
{
    try {
        $license = $this->licenseRepository->findById($id);
        if (!$license) {
            return $this->notFound(__('messages.license-not-found'));
        }
        
        $resumed = $this->licenseService->reactivateLicense($license);
        
        return $this->response(Response::HTTP_OK, __('messages.license-resumed'), 
            new LicenseResource($resumed->load('brand')));
    } catch (Exception $e) {
        return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
    }
}

public function cancel(int $id): JsonResponse
{
    try {
        $license = $this->licenseRepository->findById($id);
        if (!$license) {
            return $this->notFound(__('messages.license-not-found'));
        }
        
        $canceled = $this->licenseService->updateLicense($license, [
            'status' => LicenseStatus::CANCELED->value
        ]);
        
        return $this->response(Response::HTTP_OK, __('messages.license-canceled'), 
            new LicenseResource($canceled->load('brand')));
    } catch (Exception $e) {
        return $this->error(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
    }
}
```

### Step 3: Add Tests
```php
// File: tests/Feature/LicenseApiTest.php
// Add these test methods:

public function test_can_renew_license(): void
{
    $license = License::factory()->create(['brand_id' => $this->testBrand->id]);
    
    $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/renew", [
        'days' => 365
    ]);
    
    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'active');
}

public function test_can_suspend_license(): void
{
    $license = License::factory()->create(['brand_id' => $this->testBrand->id]);
    
    $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/suspend");
    
    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'suspended');
}

public function test_can_resume_license(): void
{
    $license = License::factory()->create([
        'brand_id' => $this->testBrand->id,
        'status' => 'suspended'
    ]);
    
    $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/resume");
    
    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'active');
}

public function test_can_cancel_license(): void
{
    $license = License::factory()->create(['brand_id' => $this->testBrand->id]);
    
    $response = $this->postJsonWithApiKey("/api/v1/licenses/{$license->id}/cancel");
    
    $response->assertStatus(200)
        ->assertJsonPath('data.status', 'canceled');
}
```

### Step 4: Add Swagger Documentation
```php
// File: app/Docs/LicenseDocs.php
// Add OpenAPI annotations for the new endpoints
```

---

## 🔴 FIX #2: Initialize Git Repository (CRITICAL)

**Problem:** Project is not a Git repository

**Time:** 30 minutes

### Commands:
```bash
# Initialize repository
git init

# Create develop branch (main development)
git checkout -b develop

# Add all files
git add .

# Initial commit
git commit -m "Initial commit: group.one Centralized License Service

- Laravel 11 + Sail setup
- Multi-tenant license management
- API v1 with Swagger docs
- PHPStan Level 5
- CI/CD with GitHub Actions
- Complete test suite"

# Create trunk branch (production)
git branch trunk

# Create .gitignore if not exists
# (Laravel should have this already)

# Add remote (replace with your repo URL)
git remote add origin <your-repo-url>

# Push both branches
git push -u origin develop
git push -u origin trunk
```

### Set Up Branch Protections (on GitHub):
1. Go to Settings → Branches
2. Add rule for `develop`:
   - Require pull request before merging
   - Require 1 approval
   - Require status checks to pass
   - Require branch to be up to date
3. Add rule for `trunk`:
   - Require pull request before merging
   - Require 2 approvals
   - Require status checks to pass

---

## 🔴 FIX #3: Generate PHPStan Baseline (CRITICAL)

**Problem:** Baseline file referenced but doesn't exist

**Time:** 5 minutes

### Commands:
```bash
# Generate baseline
./vendor/bin/sail composer phpstan -- --generate-baseline

# Or without Sail:
vendor/bin/phpstan analyse --generate-baseline

# This creates phpstan-baseline.neon
# Commit it:
git add phpstan-baseline.neon
git commit -m "Add PHPStan baseline for legacy code"
```

---

## 🟡 FIX #4: Implement Diff Coverage Enforcement (HIGH)

**Problem:** CI/CD has placeholder only, not enforcing 50% minimum

**Time:** 30 minutes

### Step 1: Install diff-cover
```yaml
# File: .github/workflows/ci.yml
# Replace lines 138-141 with:

      - name: Install diff-cover
        run: |
          pip install diff-cover

      - name: Check diff coverage
        run: |
          diff-cover coverage.xml \
            --compare-branch=origin/develop \
            --fail-under=50 \
            --html-report=diff-coverage.html

      - name: Upload diff coverage report
        if: always()
        uses: actions/upload-artifact@v3
        with:
          name: diff-coverage-report
          path: diff-coverage.html
```

### Step 2: Test locally
```bash
# Install diff-cover
pip install diff-cover

# Run tests with coverage
./vendor/bin/sail composer test:coverage

# Check diff coverage
diff-cover coverage.xml --compare-branch=develop --fail-under=50
```

---

## 🟡 FIX #5: Register Custom PHPStan Rules (HIGH)

**Problem:** Rules defined but not loaded by PHPStan

**Time:** 10 minutes

### Update phpstan.neon:
```neon
# File: phpstan.neon
# Add after line 2:

includes:
    - ./vendor/larastan/larastan/extension.neon

rules:
    - App\PHPStan\Rules\NoVarDumpRule
    - App\PHPStan\Rules\NoUpdateOptionRule

parameters:
    # ... rest of config
```

### Test:
```bash
# Run PHPStan
./vendor/bin/sail composer phpstan

# Should now enforce custom rules
```

---

## 🟡 FIX #6: Add Missing Tests (HIGH)

**Problem:** Missing tests for LicenseKey, Customer, Jobs

**Time:** 2 hours

### Create tests/Feature/LicenseKeyApiTest.php
### Create tests/Feature/CustomerApiTest.php
### Create tests/Unit/JobsTest.php

(See full test code in SPECIFICATION_AUDIT_REPORT.md)

---

## 📊 After Fixes - Expected Compliance

| Fix | Current | After Fix |
|-----|---------|-----------|
| US2 Endpoints | 40% | 100% |
| Git Repository | 0% | 100% |
| PHPStan Baseline | 0% | 100% |
| Diff Coverage | 20% | 100% |
| Custom Rules | 50% | 100% |
| Test Coverage | 60% | 85% |
| **Overall** | **75%** | **95%** |

---

## ⏱️ Total Time Estimate

- Fix #1 (US2 Endpoints): 2 hours
- Fix #2 (Git Init): 30 minutes
- Fix #3 (PHPStan Baseline): 5 minutes
- Fix #4 (Diff Coverage): 30 minutes
- Fix #5 (Custom Rules): 10 minutes
- Fix #6 (Missing Tests): 2 hours

**Total: ~5.5 hours**

---

## ✅ Verification Checklist

After completing all fixes:

```bash
# 1. Verify Git repository
git status
git branch -a

# 2. Verify PHPStan
./vendor/bin/sail composer phpstan

# 3. Verify tests
./vendor/bin/sail composer test

# 4. Verify coverage
./vendor/bin/sail composer test:coverage

# 5. Verify diff coverage (after making a branch)
git checkout -b feature/test-diff-coverage
# Make a small change
diff-cover coverage.xml --compare-branch=develop --fail-under=50

# 6. Verify CI/CD
git push origin feature/test-diff-coverage
# Open PR and check all CI checks pass

# 7. Verify API endpoints
curl -X POST http://localhost/api/v1/licenses/1/renew \
  -H "Authorization: Bearer your-api-key" \
  -H "Content-Type: application/json" \
  -d '{"days": 365}'
```

---

**After these fixes, the project will be 95% compliant and production-ready!**

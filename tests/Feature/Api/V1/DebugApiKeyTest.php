<?php

namespace Tests\Feature\Api\V1;

use App\Models\ApiKey;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\WithApiKey;

class DebugApiKeyTest extends TestCase
{
    use RefreshDatabase;
    use WithApiKey;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpApiKey();
    }

    public function test_debug_api_key_with_trait(): void
    {
        // Debug output
        dump('Test API Key: ' . $this->testApiKey);
        dump('Test Brand ID: ' . $this->testBrand->id);

        // Verify the key can be found
        $prefix = ApiKey::extractPrefix($this->testApiKey);
        $hashedKey = ApiKey::hash($this->testApiKey);
        dump('Prefix: ' . $prefix);
        dump('Hashed key: ' . $hashedKey);

        $foundKey = ApiKey::where('prefix', $prefix)
            ->where('key', $hashedKey)
            ->first();
        dump('Found key: ' . ($foundKey ? 'yes' : 'no'));
        if ($foundKey) {
            dump('Found key ID: ' . $foundKey->id);
            dump('Found key brand_id: ' . $foundKey->brand_id);
            dump('Found key is_active: ' . ($foundKey->is_active ? 'true' : 'false'));
        }

        // Try with trait method
        $response1 = $this->getJsonWithApiKey('/api/v1/licenses');
        dump('Response 1 (trait) status: ' . $response1->status());
        dump('Response 1 (trait) body: ' . $response1->content());

        // Try with direct header
        $response2 = $this->getJson('/api/v1/licenses', [
            'X-API-Key' => $this->testApiKey,
        ]);
        dump('Response 2 (direct) status: ' . $response2->status());
        dump('Response 2 (direct) body: ' . $response2->content());

        // Try with withHeaders
        $response3 = $this->withHeaders([
            'X-API-Key' => $this->testApiKey,
        ])->getJson('/api/v1/licenses');
        dump('Response 3 (withHeaders) status: ' . $response3->status());
        dump('Response 3 (withHeaders) body: ' . $response3->content());

        $response3->assertStatus(200);
    }
}


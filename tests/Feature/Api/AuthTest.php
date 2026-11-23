<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Sanctum\PersonalAccessToken;

class AuthTest extends ApiTestCase
{
    public function test_staff_can_login_and_receive_scoped_token(): void
    {
        $user = $this->apiUser('editor_berita', [
            'username' => 'editor.api',
            'email' => 'editor.api@example.test',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->postJson(route('api.v1.login'), [
            'login' => 'editor.api',
            'password' => 'rahasia123',
            'device_name' => 'asisten-test',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.scopes', ['website:read', 'cms:write'])
            ->assertJsonStructure([
                'data' => ['token', 'token_id', 'expires_at', 'scopes', 'user'],
                'warnings',
                'meta' => ['request_id', 'api_version'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertNotSame($response->json('data.token'), PersonalAccessToken::first()->token);
    }

    public function test_login_rejects_wrong_password_inactive_password_change_and_forbidden_role(): void
    {
        $user = $this->apiUser('guru', ['username' => 'guru.api', 'password' => Hash::make('rahasia123')]);

        $this->postJson(route('api.v1.login'), [
            'login' => 'guru.api',
            'password' => 'salah',
            'device_name' => 'test',
        ])->assertUnauthorized()->assertJsonPath('code', 'INVALID_CREDENTIALS');

        $user->update(['is_active' => false]);
        $this->postJson(route('api.v1.login'), [
            'login' => 'guru.api',
            'password' => 'rahasia123',
            'device_name' => 'test',
        ])->assertForbidden()->assertJsonPath('code', 'ACCOUNT_INACTIVE');

        $user->update(['is_active' => true, 'must_change_password' => true]);
        $this->postJson(route('api.v1.login'), [
            'login' => 'guru.api',
            'password' => 'rahasia123',
            'device_name' => 'test',
        ])->assertForbidden()->assertJsonPath('code', 'PASSWORD_CHANGE_REQUIRED');

        $parent = $this->apiUser('orang_tua', ['username' => 'ortu.api', 'password' => Hash::make('rahasia123')]);
        $this->postJson(route('api.v1.login'), [
            'login' => 'ortu.api',
            'password' => 'rahasia123',
            'device_name' => 'test',
        ])->assertForbidden()->assertJsonPath('code', 'ROLE_NOT_ALLOWED');
    }

    public function test_login_rejects_scope_escalation(): void
    {
        $this->apiUser('guru', [
            'username' => 'guru.scope',
            'password' => Hash::make('rahasia123'),
        ]);

        $this->postJson(route('api.v1.login'), [
            'login' => 'guru.scope',
            'password' => 'rahasia123',
            'device_name' => 'test',
            'scopes' => ['website:read', 'keuangan:write'],
        ])->assertForbidden()->assertJsonPath('code', 'SCOPE_NOT_ALLOWED');
    }

    public function test_me_and_logout_revoke_only_current_token(): void
    {
        $user = $this->apiUser('kepala_madrasah');
        $first = $this->bearer($user, ['website:read']);
        $second = $this->bearer($user, ['website:read']);

        $this->withHeaders($this->authHeaders($first))
            ->getJson(route('api.v1.me'))
            ->assertOk()
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.token.scopes', ['website:read']);

        $this->withHeaders($this->authHeaders($first))
            ->postJson(route('api.v1.logout'))
            ->assertOk()
            ->assertJsonPath('data.revoked', true);

        $this->assertSame(1, $user->tokens()->count());
        $this->app['auth']->forgetGuards();
        $this->withHeaders($this->authHeaders($first))
            ->getJson(route('api.v1.me'))
            ->assertUnauthorized();

        $this->withHeaders($this->authHeaders($second))
            ->getJson(route('api.v1.me'))
            ->assertOk();
    }

    public function test_missing_token_and_scope_are_distinct(): void
    {
        $this->getJson(route('api.v1.siswa.index'))->assertUnauthorized();

        $user = $this->apiUser('editor_berita');
        $token = $this->bearer($user, ['cms:write']);

        $this->withHeaders($this->authHeaders($token))->getJson(route('api.v1.siswa.index'))
            ->assertForbidden()
            ->assertJsonPath('code', 'SCOPE_MISSING');
    }

    public function test_login_is_rate_limited(): void
    {
        RateLimiter::clear('login-ip:127.0.0.1');
        RateLimiter::clear('login:rate.limit');

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson(route('api.v1.login'), [
                'login' => 'rate.limit',
                'password' => 'salah',
                'device_name' => 'test',
            ])->assertUnauthorized();
        }

        $this->postJson(route('api.v1.login'), [
            'login' => 'rate.limit',
            'password' => 'salah',
            'device_name' => 'test',
        ])->assertStatus(429)->assertJsonPath('code', 'RATE_LIMITED');

        RateLimiter::clear('login-ip:127.0.0.1');
        RateLimiter::clear('login:rate.limit');
    }

    public function test_inactive_user_token_is_revoked_on_next_request(): void
    {
        $user = $this->apiUser('bendahara');
        $token = $this->bearer($user, ['website:read']);

        $user->update(['is_active' => false]);

        $this->withHeaders($this->authHeaders($token))
            ->getJson(route('api.v1.me'))
            ->assertForbidden()
            ->assertJsonPath('code', 'ACCOUNT_INACTIVE');

        $this->assertSame(0, $user->tokens()->count());
    }
}

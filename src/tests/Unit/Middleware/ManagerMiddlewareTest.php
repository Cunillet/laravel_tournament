<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\ManagerMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

final class ManagerMiddlewareTest extends TestCase
{
    /** @test */
    public function it_allows_admin_users(): void
    {
        // Given
        $user    = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $request = Request::create('/manager', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new ManagerMiddleware();

        // When
        $response = $middleware->handle($request, fn () => response('OK'));

        // Then
        $this->assertSame('OK', $response->getContent());
    }

    /** @test */
    public function it_allows_manager_users(): void
    {
        // Given
        $user    = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $request = Request::create('/manager', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new ManagerMiddleware();

        // When
        $response = $middleware->handle($request, fn () => response('OK'));

        // Then
        $this->assertSame('OK', $response->getContent());
    }

    /** @test */
    public function it_blocks_regular_users(): void
    {
        // Given
        $user    = User::factory()->create(['role' => User::ROLE_USER]);
        $request = Request::create('/manager', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new ManagerMiddleware();

        // Then
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);

        // When
        $middleware->handle($request, fn () => response('OK'));
    }

    /** @test */
    public function it_blocks_unauthenticated_users(): void
    {
        // Given
        $request = Request::create('/manager', 'GET');
        $request->setUserResolver(fn () => null);

        $middleware = new ManagerMiddleware();

        // Then
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);

        // When
        $middleware->handle($request, fn () => response('OK'));
    }
}

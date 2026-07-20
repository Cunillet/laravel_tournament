<?php

declare(strict_types=1);

namespace Tests\Unit\Middleware;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class AdminMiddlewareTest extends TestCase
{
    /** @test */
    public function it_allows_admin_users(): void
    {
        // Given
        $user    = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new AdminMiddleware();

        // When
        $response = $middleware->handle($request, fn () => response('OK'));

        // Then
        $this->assertSame('OK', $response->getContent());
    }

    /** @test */
    public function it_blocks_manager_users(): void
    {
        // Given
        $user    = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new AdminMiddleware();

        // Then
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);

        // When
        $middleware->handle($request, fn () => response('OK'));
    }

    /** @test */
    public function it_blocks_regular_users(): void
    {
        // Given
        $user    = User::factory()->create(['role' => User::ROLE_USER]);
        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => $user);

        $middleware = new AdminMiddleware();

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
        $request = Request::create('/admin', 'GET');
        $request->setUserResolver(fn () => null);

        $middleware = new AdminMiddleware();

        // Then
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->expectExceptionCode(403);

        // When
        $middleware->handle($request, fn () => response('OK'));
    }
}

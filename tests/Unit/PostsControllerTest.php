<?php

namespace Tests\Unit;

use App\Http\Controllers\PostsController;
use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;

class PostsControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_show_returns_view(): void
    {
        $post = new Post(['id' => 1, 'title' => 'Test Post']);
        
        $repo = Mockery::mock(PostRepositoryInterface::class);
        $repo->shouldReceive('show')
            ->once()
            ->with('john', 'hello-world')
            ->andReturn($post);

        $this->app->instance(PostRepositoryInterface::class, $repo);

        $controller = $this->app->make(PostsController::class);
        $response = $controller->show('john', 'hello-world');

        $this->assertInstanceOf(ViewContract::class, $response);
        $this->assertSame('posts.show', $response->name());
    }

    public function test_create_returns_view(): void
    {
        $user = new User(['id' => 1, 'name' => 'Test User']);
        
        $repo = Mockery::mock(PostRepositoryInterface::class);
        $repo->shouldReceive('create')
            ->once()
            ->andReturn($user);

        $this->app->instance(PostRepositoryInterface::class, $repo);

        $controller = $this->app->make(PostsController::class);
        $response = $controller->create();

        $this->assertInstanceOf(ViewContract::class, $response);
        $this->assertSame('posts.create', $response->name());
    }

    public function test_store_redirects(): void
    {
        $repo = Mockery::mock(PostRepositoryInterface::class);
        $repo->shouldReceive('store')
            ->once()
            ->andReturn('/john/hello-world');

        $this->app->instance(PostRepositoryInterface::class, $repo);

        $controller = $this->app->make(PostsController::class);
        $request = Request::create('/posts', 'POST', [
            'title' => 'Hello',
            'content' => 'World',
        ]);

        $response = $controller->store($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/john/hello-world', parse_url($response->getTargetUrl(), PHP_URL_PATH));
    }

    public function test_delete_redirects(): void
    {
        $repo = Mockery::mock(PostRepositoryInterface::class);
        $repo->shouldReceive('delete')
            ->once()
            ->with(1, Mockery::type('int'))
            ->andReturn(true);

        $this->app->instance(PostRepositoryInterface::class, $repo);

        $controller = $this->app->make(PostsController::class);
        $response = $controller->delete(1);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('/', parse_url($response->getTargetUrl(), PHP_URL_PATH) ?? '/');
    }
}

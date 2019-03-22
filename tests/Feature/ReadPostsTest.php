<?php

namespace Tests\Feature;

use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PostsTest extends TestCase
{
    use DatabaseMigrations;

    /** @var Post */
    protected $post;

    public function setUp() : void
    {
        parent::setUp();

        $user = factory('App\User')->create();
        factory('App\Profile')->create(['user_id' => $user->id]);
        $category = factory('App\Category')->create();

        $this->post = factory('App\Post')->create(['user_id' => $user->id, 'category_id' => $category->id]);
    }

    /** @test */
    public function a_user_can_view_all_posts()
    {
        $this->getJson("/api/posts")
            ->assertJsonStructure(['status', 'categories', 'posts']);
    }

    /** @test */
    function a_user_can_read_a_single_post()
    {
        $category = $this->post->category->slug;
        $slug = $this->post->slug;
        $this->get("/api/posts/{$category}/{$slug}")
            ->assertJson([
                'post' => ['title' => $this->post->title]
            ]);
    }

    /** @test */
    function a_user_can_filter_posts_according_to_a_category()
    {
        $post_in_category = $this->post;
        $category_id = $post_in_category->category->id;

        $other_category_id = factory('App\Category')->create()->id;
        $post_not_in_category = factory('App\Post')->create(['category_id' => $other_category_id]);

        $this->getJson("/api/posts?category_id={$category_id}")
            ->assertJson([
                'posts' => [['title' => $post_in_category->title]]
            ])
            ->assertJsonMissing([
                'posts' => [['title' => $post_not_in_category->title]]
            ]);
    }
}

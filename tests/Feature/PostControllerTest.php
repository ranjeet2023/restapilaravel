<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        Post::factory()->count(5)->create();
        $response = $this->get('/api/posts');
        $response->assertStatus(200)
            ->assertJsonCount(5); 
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post content.'
        ];
        
        $response = $this->post('/api/post', $postData);

        $response->assertStatus(201)
            ->assertJson(['title' => 'Test Post']);
    }

    public function testShow()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $post = Post::factory()->create();
        $response = $this->get('/api/show/' . $post->id);
        $response->assertStatus(200)
            ->assertJson(['id' => $post->id]);
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $post = Post::factory()->create();
        $updatedData = [
            'title' => 'Updated Title',
            'content' => 'Updated content.'
        ];
        $response = $this->put('/api/update/' . $post->id, $updatedData);
        $response->assertStatus(200)
            ->assertJson(['title' => 'Updated Title']);
    }

    public function testDestroy()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $post = Post::factory()->create();
        $response = $this->delete('/api/delete/' . $post->id);
        $response->assertStatus(200)
            ->assertJson(['data' => 'Post deleted successfully']); 
    }

    public function testUserLogout()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $response = $this->get('/api/logout');
        $response->assertStatus(200)
            ->assertJson(['message' => 'User logout successfully.']); // Assuming the response contains the success message
    }
}


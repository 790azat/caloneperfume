<?php

namespace Tests\Feature;

use App\Livewire\Admin\WorkForm;
use App\Livewire\Auth\Register;
use App\Livewire\InquiryForm;
use App\Models\Inquiry;
use App\Models\User;
use App\Models\Work;
use App\Support\MediaFolder;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(ContentSeeder::class);
        MediaFolder::createWork('3051034622250072370', [
            ['type' => 'image', 'url' => '/media/p/1/a.webp', 'thumb' => '/media/p/1/a-t.webp'],
            ['type' => 'video', 'url' => 'https://x.public.blob.vercel-storage.com/v.mp4', 'poster' => '/media/p/1/v.webp'],
        ], '2023-03-19');
    }

    public function test_public_pages_render_in_every_language(): void
    {
        $work = Work::first();

        foreach (['hy' => 'Հավաքածու', 'ru' => 'Коллекция', 'en' => 'Collection'] as $locale => $text) {
            $this->get("/lang/{$locale}")->assertRedirect();
            $this->get('/')->assertOk()->assertSee($text);
            $this->get('/works')->assertOk();
            $this->get('/works/'.$work->slug)->assertOk()->assertSee($work->name());
            $this->get('/videos')->assertOk();
            $this->get('/contact')->assertOk();
        }
    }

    public function test_visitor_can_send_an_inquiry(): void
    {
        Livewire::test(InquiryForm::class)
            ->set('name', 'Ani')
            ->call('submit')
            ->assertHasErrors('phone')
            ->set('phone', '+37499000000')
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseHas(Inquiry::class, ['name' => 'Ani', 'status' => 'new']);
    }

    public function test_admin_email_becomes_admin_on_registration(): void
    {
        config(['app.admin_email' => 'owner@calone.am']);

        Livewire::test(Register::class)
            ->set('name', 'Owner')
            ->set('email', 'owner@calone.am')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(User::firstWhere('email', 'owner@calone.am')->is_admin);
    }

    public function test_admin_panel_is_only_for_admins(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();

        $admin = User::factory()->create(['is_admin' => true]);
        foreach (['', '/works', '/works/create', '/import', '/categories', '/services', '/testimonials', '/inquiries', '/users', '/settings'] as $page) {
            $this->actingAs($admin)->get('/admin'.$page)->assertOk();
        }
    }

    public function test_admin_uploads_photos_and_videos(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        Livewire::test(WorkForm::class)
            ->set('title.ru', 'Свадьба Ани и Арама')
            ->set('photos', [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')])
            ->set('videos', [UploadedFile::fake()->create('clip.mp4', 1024, 'video/mp4')])
            ->set('link', 'https://youtu.be/dQw4w9WgXcQ')
            ->call('save')
            ->assertHasNoErrors();

        $work = Work::latest('id')->first();
        $this->assertSame('Свадьба Ани и Арама', $work->tr('title', 'ru'));
        $this->assertSame(['image', 'image', 'video', 'embed'], $work->media->pluck('type')->all());
        Storage::disk('public')->assertExists($work->media[0]->path);
        $this->assertSame('https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0', $work->media[3]->embedUrl());
    }

    public function test_admin_gets_a_vercel_blob_client_token_and_attaches_blob_files(): void
    {
        config(['services.blob.token' => 'vercel_blob_rw_Store123_secret']);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->postJson('/admin/blob-upload', ['type' => 'blob.generate-client-token', 'payload' => ['pathname' => 'works/a.jpg']])
            ->assertUnauthorized();

        $token = $this->actingAs($admin)
            ->postJson('/admin/blob-upload', ['type' => 'blob.generate-client-token', 'payload' => ['pathname' => 'works/a.jpg']])
            ->assertOk()
            ->json('clientToken');
        $this->assertStringStartsWith('vercel_blob_client_Store123_', $token);

        $this->actingAs($admin)
            ->postJson('/admin/blob-upload', ['type' => 'blob.generate-client-token', 'payload' => ['pathname' => '../etc/passwd']])
            ->assertStatus(422);

        Livewire::test(WorkForm::class)
            ->set('title.ru', 'Blob')
            ->call('addBlob', 'image', 'https://store123.public.blob.vercel-storage.com/works/a-x1.jpg')
            ->call('save');

        $this->assertSame('https://store123.public.blob.vercel-storage.com/works/a-x1.jpg', Work::latest('id')->first()->media->first()->src());
    }

    public function test_media_folder_groups_instagram_posts_and_never_reimports(): void
    {
        $dir = sys_get_temp_dir().'/calone-'.uniqid();
        mkdir($dir);
        foreach (['3051034622250072370_1.jpg', '3051034622250072370_2.jpg', '3048670543736816745.mp4', 'rose.png', 'notes.txt'] as $file) {
            file_put_contents("{$dir}/{$file}", 'x');
        }

        $groups = MediaFolder::scan($dir);
        $this->assertSame(['rose', '3051034622250072370', '3048670543736816745'], array_map('strval', array_keys($groups)));
        $this->assertCount(2, $groups['3051034622250072370']['files']);
        $this->assertSame('2023-03-04', $groups['3051034622250072370']['date']);
        $this->assertTrue($groups['3048670543736816745']['has_video']);

        // The first post already exists (setUp), so only two new works are created.
        Storage::fake('public');
        foreach ($groups as $key => $group) {
            MediaFolder::importGroup($dir, (string) $key, $group);
        }
        $this->assertSame(3, Work::count());
        $this->assertSame('video', Work::firstWhere('source_key', '3048670543736816745')->category->slug);

        Work::where('source_key', 'rose')->first()->delete();
        $this->assertNull(MediaFolder::importGroup($dir, 'rose', $groups['rose']));
    }
}

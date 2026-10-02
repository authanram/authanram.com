<?php

use Illuminate\Support\Facades\Http;

beforeEach(fn () => Http::preventStrayRequests());

function fakeStats(): void
{
    Http::fake([
        'packagist.org/packages/innoge/laravel-rclone.json' => Http::response(['package' => ['downloads' => ['total' => 12536], 'github_stars' => 18]]),
        'packagist.org/packages/innoge/laravel-speculation-rules-api.json' => Http::response(['package' => ['downloads' => ['total' => 184], 'github_stars' => 14]]),
        'github.com/users/authanram/contributions' => Http::response('<h2 id="js-contribution-activity-description">
      3,233
      contributions
        in the last year
    </h2>'),
    ]);
}

test('it shows live package downloads, stars and contributions', function () {
    fakeStats();

    $this->get('/')
        ->assertOk()
        ->assertSeeInOrder(['InnoGE/laravel-rclone', '18', '12,536', 'InnoGE/laravel-speculation-rules-api', '14', '184'])
        ->assertSee('3,233');
});

test('it caches the stats between requests', function () {
    fakeStats();

    $this->get('/')->assertOk();
    $this->get('/')->assertOk();

    Http::assertSentCount(3);
});

test('it hides the stats when the upstream services fail', function () {
    Http::fake(['*' => Http::response(status: 500)]);

    $this->get('/')
        ->assertOk()
        ->assertSee('InnoGE/laravel-rclone')
        ->assertDontSee('downloads')
        ->assertDontSee('Contributions in the last year');
});

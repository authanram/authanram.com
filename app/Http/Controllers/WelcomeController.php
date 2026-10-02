<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class WelcomeController extends Controller
{
    private const PACKAGES = ['innoge/laravel-rclone', 'innoge/laravel-speculation-rules-api'];

    private const GITHUB_USER = 'authanram';

    public function __invoke(): View
    {
        return view('welcome', [
            'packages' => collect(self::PACKAGES)->mapWithKeys(fn (string $name) => [
                $name => $this->remember("packagist:{$name}", "https://packagist.org/packages/{$name}.json", fn (Response $response) => [
                    'downloads' => (int) $response->json('package.downloads.total'),
                    'stars' => (int) $response->json('package.github_stars'),
                ]),
            ]),
            'contributions' => $this->remember(
                'github:contributions:'.self::GITHUB_USER,
                'https://github.com/users/'.self::GITHUB_USER.'/contributions',
                fn (Response $response) => preg_match('/([\d,]+)\s+contributions?\s+in the last year/', $response->body(), $match)
                    ? (int) str_replace(',', '', $match[1])
                    : null,
            ),
        ]);
    }

    /**
     * Serves cached stats and refreshes them in the background once stale, so a slow
     * or failing upstream never delays the page; null when no value is known yet.
     */
    private function remember(string $key, string $url, Closure $parse): mixed
    {
        return Cache::flexible($key, [now()->addHour(), now()->addDay()], function () use ($url, $parse) {
            try {
                return $parse(Http::timeout(3)->get($url)->throw());
            } catch (ConnectionException|RequestException) {
                return null;
            }
        });
    }
}

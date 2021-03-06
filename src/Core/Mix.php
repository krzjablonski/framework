<?php

namespace Themosis\Core;

use Exception;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Mix
{
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     *
     * @throws \Exception
     *
     * @return \Illuminate\Support\HtmlString|string
     */
    public function __invoke($path, $manifestDirectory = '')
    {
        static $manifests = [];

        if (! $manifestDirectory) {
            $manifestDirectory = 'content/themes/'.wp_get_theme()->stylesheet;
        }

        if (! Str::startsWith($path, '/')) {
            $path = "/{$path}";
        }

        $publicDir = rtrim(THEMOSIS_PUBLIC_DIR, '/\\').'/'.trim($manifestDirectory, '/');
        if (file_exists(base_path($publicDir.'/hot'))) {
            $url = rtrim(file_get_contents(base_path($publicDir.'/hot')));

            if (Str::startsWith($url, ['http://', 'https://'])) {
                return new HtmlString(Str::after($url, ':').$path);
            }

            return new HtmlString("//localhost:8080{$path}");
        }

        $manifestPath = base_path($publicDir.'/mix-manifest.json');
        if (! isset($manifests[$manifestPath])) {
            if (! file_exists($manifestPath)) {
                throw new Exception('The Mix manifest does not exist.');
            }

            $manifests[$manifestPath] = json_decode(file_get_contents($manifestPath), true);
        }

        $manifest = $manifests[$manifestPath];

        if (! isset($manifest[$path])) {
            throw new Exception("Unable to locate Mix file: {$path}.");
        }

        return new HtmlString(get_home_url(null, rtrim($manifestDirectory, '/').$manifest[$path]));
    }
}

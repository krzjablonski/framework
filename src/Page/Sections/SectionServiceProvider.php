<?php

namespace Themosis\Page\Sections;

use Illuminate\Support\ServiceProvider;

class SectionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('sections', function () {
            $data = new SectionData();

            return new SectionBuilder($data);
        });
    }
}

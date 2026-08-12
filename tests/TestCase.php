<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The test suite runs without a Vite build, so render @vite directives
        // as no-ops. This keeps views that reference bundled assets (e.g. the
        // home page's home.js) from throwing a "manifest not found" error.
        $this->withoutVite();
    }
}

<?php

namespace Rumur\WordPress\Notice\Test\Unit;

use PHPUnit\Framework\TestCase;

class Notice extends TestCase
{
    public function testCanAddAndRenderNotices()
    {
        \Rumur\WordPress\Notice\Notice::info($n1 = 'Info');
        \Rumur\WordPress\Notice\Notice::error($n2 = 'Error');
        \Rumur\WordPress\Notice\Notice::warning($n3 = 'Warning');
        \Rumur\WordPress\Notice\Notice::success($n4 = 'Success');

        ob_start();
        \Rumur\WordPress\Notice\Notice::render();
        $rendered = ob_get_clean();

        $this->assertStringContainsString($n1, $rendered);
        $this->assertStringContainsString($n2, $rendered);
        $this->assertStringContainsString($n3, $rendered);
        $this->assertStringContainsString($n4, $rendered);
        $this->assertStringNotContainsString('Wrong Message', $rendered);
    }

    public function testCanRenderWithConditions()
    {
        \Rumur\WordPress\Notice\Notice::info($n1 = 'Should be seen only once n1');
        \Rumur\WordPress\Notice\Notice::error($n2 = 'Should not be seen at all n2')->showWhenPage('themes');
        \Rumur\WordPress\Notice\Notice::warning($n3 = 'Should be seen once and later n3')->showLater(strtotime("+1sec"));
        \Rumur\WordPress\Notice\Notice::success($n4 = 'Should be seen until n4')->showUntil('+1sec');

        ob_start();
        \Rumur\WordPress\Notice\Notice::render();
        $rendered = ob_get_clean();

        $this->assertStringContainsString($n1, $rendered);
        $this->assertStringNotContainsString($n2, $rendered);
        $this->assertStringNotContainsString($n3, $rendered);
        $this->assertStringContainsString($n4, $rendered);

        sleep(1);

        ob_start();
        \Rumur\WordPress\Notice\Notice::render();
        $rendered = ob_get_clean();

        $this->assertStringNotContainsString($n1, $rendered);
        $this->assertStringContainsString($n3, $rendered);
        $this->assertStringContainsString($n4, $rendered);

        ob_start();
        \Rumur\WordPress\Notice\Notice::render();
        $rendered = ob_get_clean();

        $this->assertStringNotContainsString($n3, $rendered);
    }
}
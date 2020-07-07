<?php

namespace Rumur\WordPress\Notice\Test\Unit;

use PHPUnit\Framework\TestCase;

class Notice extends TestCase
{
    public function canAddAndRenderNotices()
    {
        \Rumur\WordPress\Notice\Notice::info($n1 = 'Info');
        \Rumur\WordPress\Notice\Notice::error($n2 = 'Error');
        \Rumur\WordPress\Notice\Notice::warning($n3 = 'Warning');
        \Rumur\WordPress\Notice\Notice::success($n4 = 'Success');

        ob_start();
        \Rumur\WordPress\Notice\Notice::render();
        $rendered = ob_get_clean();

        var_dump($rendered);exit;

        $this->assertStringContainsString('Test', $rendered);
        $this->assertStringContainsString($n2, $rendered);
        $this->assertStringContainsString($n3, $rendered);
        $this->assertStringContainsString($n4, $rendered);
    }
}
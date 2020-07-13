<?php

namespace Rumur\WordPress\Notice\Test\Unit;

use PHPUnit\Framework\TestCase;
use Rumur\WordPress\Notice\PendingNotice;
use Rumur\WordPress\Notice\Renderer;

class RendererTest extends TestCase
{
    public function testCanRenderPendingNotice()
    {
        $pending = new PendingNotice($str = 'Testing the Pending Notice', $type = 'error');

        $pending->asAlternative()->dismissible();

        ob_start();
        (new Renderer())->render($pending);
        $rendered = ob_get_clean();

        $this->assertStringContainsString($str, $rendered);

        $this->assertStringContainsString('notice-alt', $rendered);
        $this->assertStringContainsString("notice-{$type}", $rendered);
        $this->assertStringContainsString('is-dismissible', $rendered);
    }

    public function testCanRenderFunction()
    {
        $str = 'Hello from';

        $pending = new PendingNotice('rmr_test_notice', $type = 'error');

        $pending->asAlternative()->dismissible();

        ob_start();
        (new Renderer())->render($pending);
        $rendered = ob_get_clean();

        $this->assertStringContainsString($str, $rendered);

        $this->assertStringContainsString('notice-alt', $rendered);
        $this->assertStringContainsString("notice-{$type}", $rendered);
        $this->assertStringContainsString('is-dismissible', $rendered);
    }
}
<?php

namespace Rumur\WordPress\Notice\Test\Unit;

use PHPUnit\Framework\TestCase;

class ConditionsTest extends TestCase
{
    public function testPostTypeCondition()
    {
        $checker = new \Rumur\WordPress\Notice\Conditions();

        $this->assertFalse(
            $checker->check([
                'post_type' => ['page']
            ])
        );

        $this->assertTrue(
            $checker->check([
                'post_type' => ['post']
            ])
        );

        $this->assertTrue(
            $checker->check([
                'post_type' => ['post', 'page']
            ])
        );
    }

    public function testPageCondition()
    {
        $checker = new \Rumur\WordPress\Notice\Conditions();

        $this->assertFalse(
            $checker->check([
                'page' => ['themes']
            ])
        );

        $this->assertTrue(
            $checker->check([
                'page' => ['tools']
            ])
        );

        $this->assertTrue(
            $checker->check([
                'page' => ['tools.php']
            ])
        );

        $this->assertTrue(
            $checker->check([
                'page' => ['themes', 'tools']
            ])
        );
    }

    public function testUserCondition()
    {
        $checker = new \Rumur\WordPress\Notice\Conditions();

        $this->assertFalse(
            $checker->check([
                'user' => [2020]
            ])
        );

        $this->assertTrue(
            $checker->check([
                'user' => [1]
            ])
        );

        $this->assertTrue(
            $checker->check([
                'user' => [1, 2020]
            ])
        );
    }

    public function testRoleCondition()
    {
        $checker = new \Rumur\WordPress\Notice\Conditions();

        $this->assertTrue(
            $checker->check([
                'role' => ['subscriber']
            ])
        );
    }
}
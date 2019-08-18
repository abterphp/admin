<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Grid\Filters;

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @return array
     */
    public function filterProvider(): array
    {
        return [
            [[], [], null],
        ];
    }

    /**
     * @dataProvider filterProvider
     *
     * @param string[]    $intents
     * @param array       $attributes
     * @param string|null $tag
     */
    public function testFilter(array $intents, array $attributes, ?string $tag)
    {
        $sut = new User($intents, $attributes, $tag);

        $html = (string)$sut;

        $this->assertStringContainsString('<div class="hidable">', $html);
        $this->assertStringContainsString('filter-username', $html);
        $this->assertStringContainsString('filter-email', $html);
    }
}

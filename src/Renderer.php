<?php

namespace Rumur\WordPress\Notice;

class Renderer
{
    /**
     * Renders the PendingNotice.
     *
     * @uses \sanitize_html_class
     *
     * @param PendingNotice $notice
     */
    public function render(PendingNotice $notice): void
    {
        $attributes = $notice->attributes();

        $desiredClasses = $attributes['classes'] ?? [];
        $conditionClasses = array_keys(
            array_filter([
                'notice-' . $notice->type() => true,
                'is-dismissible' => $notice->isDismissible(),
            ])
        );

        $classes = array_merge($desiredClasses, $conditionClasses);

        if (function_exists($fn = '\\sanitize_html_class')) {
            $classes = array_map($fn, $classes);
        }

        $message = $notice->message();

	    // It might happen when Notice Class has been deleted from a codebase,
	    // but still present in the store.
	    if ($message instanceof \__PHP_Incomplete_Class) {
		    throw new Exceptions\NoticeWasCorrupted( 'Not Exists.' );
	    }

        if ($message instanceof Noticeable) {
            $message = [$message, 'message'];
        }

        if (is_callable($message)) {
            $message = call_user_func($message, $notice);
        }

        if (function_exists('\\wpautop')) {
            $message = isset($attributes['no-wpautop']) ? $message : \wpautop($message);
        }

        printf(
            '<div class="notice %s" id="notice-%s">%s</div>',
            implode(' ', $classes),
            $notice->hash(),
            $message
        );
    }
}

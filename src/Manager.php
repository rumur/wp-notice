<?php

namespace Rumur\WordPress\Notice;

class Manager
{
    /** @var Repository */
    protected $repository;

    /** @var Renderer */
    protected $renderer;

    /** @var null|\Closure */
    protected $conditions;

    /** @var array */
    protected $attributes = [];

    /**
     * Supported WordPress notice types.
     *
     * @var string[]
     */
    protected $types = [
        'info',     // blue
        'error',    // red
        'warning',  // yellow/orange
        'success',  // green
    ];

    /**
     * Manager constructor.
     *
     * @param Repository $repository
     * @param Renderer $renderer
     */
    public function __construct(Repository $repository, Renderer $renderer)
    {
        $this->repository = $repository;
        $this->renderer = $renderer;
    }

    /**
     * Renders all available notices.
     */
    public function render(): void
    {
        $all = $this->repository->all();

        // If there is a global option that tells us do not use a nag notices
        // in this case we'll always return as it wasn't a nag one.
        // @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices#Disable_Nag_Notices
        $shouldIgnoreNag = defined('DISABLE_NAG_NOTICES') && DISABLE_NAG_NOTICES;

        /** @var PendingNotice $notice */
        foreach ($all as $hash => $notice) {
            $isRendered = false;

            $shouldRender = !$notice->shouldBeShownLater() && ($this->checkConditions($notice->conditions()));

            if ($shouldRender) {
                $isRendered = true;
                $this->renderer->render($notice);
            }

            $canBeDeletedCauseTime = $notice->isExpired() && !$notice->shouldBeShownLater();

            if ($isRendered && $canBeDeletedCauseTime && ($shouldIgnoreNag || !$notice->isNag())) {
                $this->repository->delete($hash);
            }
        }
    }

    /**
     * Hooks up into WordPress.
     *
     * @param string $action
     * @param int $priority
     * @return static
     * @uses \add_action
     */
    public function registerIntoWordPress(string $action = 'all_admin_notices', int $priority = 10)
    {
        \add_action($action, [$this, 'render'], $priority);

        return $this;
    }

    /**
     * Walks through all conditions which were set for a notice.
     *
     * @param array $conditions
     * @return bool
     */
    protected function checkConditions(array $conditions): bool
    {
        if (is_callable($this->conditions)) {
            return call_user_func($this->conditions, $conditions);
        }

        return  (new Conditions())->check($conditions);
    }

    /**
     * Checks whether the type is valid.
     *
     * @param string $type
     *
     * @return bool
     */
    protected function isTypeValid(string $type): bool
    {
        return in_array($type, $this->types, true);
    }

    /**
     * Creates a PendingNotice.
     *
     * @param string|Noticeable|\WP_Error $notice
     * @param string $type Could be error | warning | update | success
     * @param bool $dismissible
     *
     * @uses \esc_html
     * @uses \is_wp_error
     *
     * @return PendingNotice
     */
    public function add($notice, $type = 'error', $dismissible = false): PendingNotice
    {
        if (! is_string($notice) && ! $notice instanceof Noticeable) {
            throw new \InvalidArgumentException(
                '`$notice` wrong type, expected either `string` or ' . Noticeable::class
            );
        }

        if (function_exists('\\is_wp_error') && \is_wp_error($notice)) {
            $notice = $notice->get_error_message();
        }

        if (! $this->isTypeValid($type)) {
            $type = 'error';
        }

        return (new PendingNotice($notice, $type, $this->repository))
            ->dismissible($dismissible)->merge($this->attributes);
    }

    /**
     * Creates a PendingNotice with `info` type.
     *
     * @param string|Noticeable $message
     *
     * @param bool $dismissible
     *
     * @return PendingNotice
     */
    public function info($message, $dismissible = false): PendingNotice
    {
        return $this->add($message, 'info', $dismissible);
    }

    /**
     * Creates a PendingNotice with `error` type.
     *
     * @param string|Noticeable|\WP_Error $message
     * @param bool $dismissible
     *
     * @return PendingNotice
     */
    public function error($message, $dismissible = false): PendingNotice
    {
        return $this->add($message, 'error', $dismissible);
    }

    /**
     * Creates a PendingNotice with `warning` type.
     *
     * @param string|Noticeable $message
     * @param bool $dismissible
     *
     * @return PendingNotice
     */
    public function warning($message, $dismissible = false): PendingNotice
    {
        return $this->add($message, 'warning', $dismissible);
    }

    /**
     * Creates a PendingNotice with `success` type.
     *
     * @param string|Noticeable $message
     *
     * @param bool $dismissible
     *
     * @return PendingNotice
     */
    public function success($message, $dismissible = false): PendingNotice
    {
        return $this->add($message, 'success', $dismissible);
    }

    /**
     * Flushes all stored data from a DB, but keeps the loaded state.
     *
     * @return static
     */
    public function flush()
    {
        $this->repository->flush();

        return $this;
    }

    /**
     * Tells do not wrap with (<p>...</p>) for all notices.
     *
     * @return static
     */
    public function withoutWrapping()
    {
        $this->attributes['no-wpautop'] = true;

        return $this;
    }

    /**
     * Replaces the default `Conditions` checker with alternative one.
     *
     * E.g. if developer needs to show notices on Frontend part instead of the admin.
     *
     * @param callable $checker
     *
     * @return static
     */
    public function resolveConditions(callable $checker)
    {
        $this->conditions = $checker;

        return $this;
    }
}

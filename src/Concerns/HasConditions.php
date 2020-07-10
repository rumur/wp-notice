<?php

namespace Rumur\WordPress\Notice\Concerns;

use Rumur\WordPress\Notice\Utils\Time;

trait HasConditions
{
    /**
     * The conditions when it should be shown.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * Getter of conditions list.
     *
     * @return array
     */
    public function conditions(): array
    {
        return $this->conditions;
    }

    /**
     * Will be shown for a specific user only.
     *
     * @param int|\WP_User ...$users
     *
     * @return $this
     */
    public function showWhenUser(...$users): self
    {
        $users_ids = array_map(static function ($user) {
            if (class_exists('\\WP_User') && $user instanceof \WP_User) {
                return $user->ID;
            }
            return $user;
        }, $users);

        $this->conditions['user'] = array_merge($this->conditions['user'] ?? [], $users_ids);

        return $this;
    }

    /**
     * Will be shown for a specific user role only.
     *
     * This condition is gonna check the `current_user` role and if it matches it will be shown then.
     *
     * @param string ...$roles
     *
     * @return $this
     * @uses \get_role
     *
     */
    public function showWhenRole(string ...$roles): self
    {
        $this->conditions['role'] = array_merge(
            $this->conditions['role'] ?? [],
            array_filter($roles, '\\get_role')
        );

        return $this;
    }

    /**
     * Will be shown for a specific `post_type` only.
     *
     * This condition is gonna check the `current_screen->post_type` and if it matches it will be shown then.
     *
     * @param string ...$postTypes
     *
     * @return $this
     * @uses \post_type_exists
     *
     */
    public function showWhenPostType(string ...$postTypes): self
    {
        $this->conditions['post_type'] = array_merge(
            $this->conditions['post_type'] ?? [],
            array_filter($postTypes, '\\post_type_exists')
        );

        return $this;
    }


    /**
     * Will be shown for a specific `page` only.
     *
     * This condition is gonna check the `current_screen` page and if it matches it will be shown then.
     *
     * @param string ...$pages
     *
     * @return $this
     */
    public function showWhenPage(string ...$pages): self
    {
        $this->conditions['page'] = array_merge($this->conditions['page'] ?? [], $pages);

        return $this;
    }

    /**
     * Will be shown for a specific `taxonomy` only.
     *
     * @param string ...$taxonomies
     *
     * @return $this
     * @uses \taxonomy_exists
     *
     */
    public function showWhenTaxonomy(string ...$taxonomies): self
    {
        $this->conditions['taxonomy'] = array_merge(
            $this->conditions['taxonomy'] ?? [],
            array_filter($taxonomies, '\\taxonomy_exists')
        );

        return $this;
    }

    /**
     * Will be shown a notice later when its time.
     *
     * @param \DateTimeInterface|int|string $when The time when you need to show this notice.
     *
     * @return static
     *
     * @link https://www.php.net/manual/en/datetime.formats.relative.php
     *  - 'tomorrow'
     *  - 'next month'
     *  - 'next week'
     *  - 'last day of +2 months'
     *  - 'last day of next month'
     *  - 'last day of next month noon'
     *
     * @uses \current_datetime
     */
    public function showLater($when)
    {
        $this->conditions['time']['later'] = Time::toTimestamp($when);

        return $this;
    }

    /**
     * Checks whether the notice should be shown later,
     * by checking the `later` time condition.
     *
     * @return bool
     */
    public function shouldBeShownLater(): bool
    {
        if (! isset($this->conditions['time']['later'])) {
            return false;
        }

        return Time::now()->getTimestamp() < $this->conditions['time']['later'];
    }

    /**
     * Will be shown a notice until time.
     *
     * @param \DateTimeInterface|int|string $until The time until you need to show this notice.
     *
     * @return static
     *
     * @link https://www.php.net/manual/en/datetime.formats.relative.php
     *  - 'tomorrow'
     *  - 'next month'
     *  - 'next week'
     *  - 'last day of +2 months'
     *  - 'last day of next month'
     *  - 'last day of next month noon'
     *
     * @uses \current_datetime
     */
    public function showUntil($until)
    {
        $this->conditions['time']['until'] = Time::toTimestamp($until);

        return $this;
    }

    /**
     * Checks whether the notice time is expired or not,
     * by checking the `until` time condition.
     * If the notice doesn't have this condition it will always return `true`
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        if (! isset($this->conditions['time']['until'])) {
            return true;
        }

        return Time::now()->getTimestamp() > $this->conditions['time']['until'];
    }

    /**
     * @return bool
     */
    public function hasTimeConditions(): bool
    {
        return isset($this->conditions['time']);
    }
}

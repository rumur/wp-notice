<?php

namespace Rumur\WordPress\Notice;

class Conditions
{
    /**
     * Checks the condition list.
     *
     * @param array $conditions
     * @return bool
     */
    public function check(array $conditions): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $conditionKeys = array_keys($conditions);

        $conditionResults = array_map(function ($condition, $values) {

            $methodName = Str::pascal($condition); // "post_type"|"post-type" -> 'PostType'

            if (method_exists($this, $method = "check{$methodName}")) {
                return $this->$method($values);
            }

            return true;
        }, $conditionKeys, $conditions);

        // Combining keys with their evaluated result.
        // End result will be e.g. [ 'role' => true, 'time' => false, ... ]
        $merged = array_combine($conditionKeys, $conditionResults);

        $checked = ! empty(array_filter($merged));

        // if there is a `time` condition along with something else
        // we'll check it with `AND` statement otherwise `OR` has been applied
        if (isset($merged['time']) && count($merged) > 1) {
            $checked = $checked && $merged['time'];
        }

        return $checked;
    }

    /**
     * Checks if the current screen is on a correct page.
     *
     * @param array $values
     *
     * @return bool
     */
    protected function checkPage(array $values): bool
    {
        return ! empty(array_intersect($values, [
            $this->currentScreen()->base,
            $this->currentScreen()->parent_base,
            $this->currentScreen()->parent_file,
        ]));
    }

    /**
     * Checks if the screen is on right post type.
     *
     * @param string[] $types
     * @return bool
     */
    protected function checkPostType(array $types): bool
    {
        return in_array($this->currentScreen()->post_type, $types, true);
    }

    /**
     * Checks if the current user has a desired role.
     *
     * @param string[] $roles
     *
     * @uses \current_user_can
     *
     * @return bool
     */
    protected function checkRole(array $roles): bool
    {
        return ! empty(array_filter($roles, '\\current_user_can'));
    }

    /**
     * Checks if the screen is on right taxonomy.
     *
     * @param string[] $taxonomies
     *
     * @return bool
     */
    protected function checkTaxonomy(array $taxonomies): bool
    {
        return in_array($this->currentScreen()->taxonomy, $taxonomies, true);
    }

    /**
     * Checks if the time slots are within desired time range.
     *
     * @param int[] $slots
     *
     * @uses \current_datetime
     *
     * @return bool
     */
    protected function checkTime(array $slots): bool
    {
        $now = \current_datetime()->getTimestamp();

        $slots = array_merge([
            'later' => $now,
            'until' => $now,
        ], $slots);

        return $now >= $slots['later'] && $slots['later'] < $slots['until'];
    }

    /**
     * Checks if the current user id is within desired ones.
     *
     * @param int[] $userIds
     *
     * @uses \get_current_user_id
     *
     * @return bool
     */
    protected function checkUser(array $userIds): bool
    {
        return in_array(\get_current_user_id(), $userIds, true);
    }

    /**
     * Retrieves an instance of \WP_Screen or default placeholder instead.
     *
     * @uses \get_current_screen
     * @return object
     */
    protected function currentScreen()
    {
        if (! $screen = \get_current_screen()) {
            $screen = (object)[
                'base' => '',
                'taxonomy' => '',
                'post_type' => '',
                'parent_base' => '',
                'parent_file' => '',
            ];
        }

        return $screen;
    }
}

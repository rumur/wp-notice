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

	    // Skipping Time, since we need to check the rest, but time will give us always true.
	    $noTimeConditions = array_diff_key( $conditions, [ 'time' => true ] );

	    $conditionKeys = array_keys($noTimeConditions);

	    $conditionResults = array_map(function ($condition, $values) {

		    $methodName = Utils\Str::pascal($condition); // "post_type"|"post-type" -> 'PostType'

		    if (method_exists($this, $method = "check{$methodName}")) {
			    return $this->$method($values);
		    }

		    return true;
	    }, $conditionKeys, $noTimeConditions);

	    // Combining keys with their evaluated result.
	    // End result will be e.g. [ 'role' => true, 'user' => true, ... ]
	    $evaluated = array_combine($conditionKeys, $conditionResults);

        return !empty(array_filter($evaluated));
    }

    /**
     * Checks if the current screen is on a correct page.
     *
     * @param array $pages
     *
     * @return bool
     */
    protected function checkPage(array $pages): bool
    {
        return ! empty(array_intersect($pages, $this->currentPages()));
    }

    /**
     * Checks if the current screen is not mention pages.
     *
     * @param array $pages
     *
     * @return bool
     */
    protected function checkPageNot(array $pages): bool
    {
        return ! $this->checkPage($pages);
    }

    /**
     * Retrieves current possible pages.
     *
     * @return array
     */
    protected function currentPages(): array
    {
        return [
            $this->currentScreen()->base,
            $this->currentScreen()->parent_base,
            $this->currentScreen()->parent_file
        ];
    }

    /**
     * Checks if the screen is on right post type.
     *
     * @param string[] $types
     * @return bool
     */
    protected function checkPostType(array $types): bool
    {
        return in_array($this->currentPostType(), $types, true);
    }

	/**
	 * Checks if the screen is on right post type.
	 *
	 * @param string[] $types
	 * @return bool
	 */
	protected function checkPostTypeNot(array $types): bool
	{
		return !$this->checkPostType($types);
	}

    /**
     * Retrieves current possible post type name.
     *
     * @return string
     */
    protected function currentPostType(): string
    {
        return $this->currentScreen()->post_type;
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
	 * Checks if the current user doesn't have a desired role.
	 *
	 * @param string[] $roles
	 *
	 * @uses \current_user_can
	 *
	 * @return bool
	 */
	protected function checkRoleNot(array $roles): bool
	{
		return ! $this->checkRole($roles);
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
        return in_array($this->currentTaxonomy(), $taxonomies, true);
    }

	/**
	 * Checks if the screen is not on the one of taxonomies.
	 *
	 * @param string[] $taxonomies
	 *
	 * @return bool
	 */
	protected function checkTaxonomyNot(array $taxonomies): bool
	{
		return !$this->checkTaxonomy($taxonomies);
	}

    /**
     * Retrieves current possible taxonomy name.
     *
     * @return string
     */
    protected function currentTaxonomy(): string
    {
        return $this->currentScreen()->taxonomy;
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
     * Checks if the current user id is not one from the list.
     *
     * @param int[] $userIds
     *
     * @uses \get_current_user_id
     *
     * @return bool
     */
    protected function checkUserNot(array $userIds): bool
    {
        return !$this->checkUser($userIds);
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

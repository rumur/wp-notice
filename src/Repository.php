<?php

namespace Rumur\WordPress\Notice;

class Repository
{
    /** @var array */
    protected $notices = [];

    /**
     * The Meta Key under which will be stored notices.
     *
     * @var string
     */
    protected $storageKey;

    /**
     * Boot indicator.
     *
     * @var bool
     */
    protected $isBooted = false;

    /**
     * Flush indicator.
     *
     * @var bool
     */
    protected $isFlushed = false;

    /**
     * Repository constructor.
     *
     * @param string $storageKey
     */
    public function __construct(string $storageKey)
    {
        $this->storageKey = $storageKey;

        $this->boot();
    }

    /**
     * Saves those that didn't show up yet or those that need to be postponed to show.
     *
     * @return void
     */
    public function __destruct()
    {
        // When we flushed the whole state from a DB it means we're
        // either deleting the plugin/theme which is used this lib.
        // So we need to respect and delete the trash behind ourselves.
        // And as far as the entire state is saving when the instance destructing
        // so in this case we just skipping this part this time.
        if (! $this->isFlushed) {
            $this->save();
        }
    }

    /**
     * Boots the data from the DB.
     *
     * @uses \get_site_option
     *
     * @return static
     */
    protected function boot()
    {
        if (! $this->isBooted) {
            $this->notices = \get_site_option($this->storageKey, []);
        }

        $this->isBooted = true;

        return $this;
    }

    /**
     * Adds to a repository a new instance of PendingNotice.
     *
     * @param PendingNotice $notice
     *
     * @return static
     */
    public function add(PendingNotice $notice)
    {
        $this->notices[$notice->hash()] = $notice;

        return $this;
    }

    /**
     * Retrieves all available notices.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->notices;
    }

    /**
     * Deletes the notice.
     *
     * @param string $hash
     *
     * @return static
     */
    public function delete(string $hash)
    {
        if ($this->has($hash)) {
            unset($this->notices[$hash]);
        }

        return $this;
    }

    /**
     * Checks whether repository has a notice or not.
     *
     * @param string $hash  The Notice hash.
     *
     * @return bool
     */
    public function has(string $hash): bool
    {
        return isset($this->notices[$hash]);
    }

    /**
     * Flushes the whole storage from DB.
     *
     * @uses \delete_site_option
     *
     * @return static
     */
    public function flush()
    {
        $this->isFlushed = \delete_site_option($this->storageKey);

        return $this;
    }

    /**
     * Saves the data to the DB.
     *
     * @uses \update_site_option
     *
     * @return static
     */
    public function save()
    {
        \update_site_option($this->storageKey, $this->notices);

        return $this;
    }
}

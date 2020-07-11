<?php

namespace Rumur\WordPress\Notice;

class PendingNotice implements \Serializable
{
    use Concerns\HasConditions;

    /** @var string */
    protected $hash;

    /** @var string|Noticeable */
    protected $message;

    /** @var null|Repository */
    protected $repository;

    /**
     * The list of attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * PendingNotice constructor.
     *
     * @param string|Noticeable $message
     * @param string $type
     * @param Repository|null
     */
    public function __construct($message, string $type = 'error', ?Repository $repository = null)
    {
        $this->message = $message;
        $this->repository = $repository;
        $this->attributes['type'] = $type;
    }

    /**
     * When the chaining configuration is finished,
     * it'll add self to a repository.
     */
    public function __destruct()
    {
        if ($this->repository) {
            $this->repository->add($this);
        }
    }

    /**
     * @return string
     */
    protected function makeHash(): string
    {
        $tokens = [
            $this->type(),
            json_encode($this->attributes),
            json_encode($this->conditions),
            json_encode(is_object($this->message)
                ? get_class($this->message)
                : $this->message),
        ];

        return md5(implode(':', $tokens));
    }

    /**
     * @return string
     */
    public function hash(): string
    {
        return $this->hash ?? $this->hash = $this->makeHash();
    }

    /**
     * An Array representation of the instance.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'hash' => $this->hash(),
            'message' => $this->message,
            'attributes' => $this->attributes,
            'conditions' => $this->conditions,
        ];
    }

    /**
     * Gets attributes.
     *
     * @return array $attributes
     */
    public function attributes(): array
    {
        return $this->attributes;
    }

    /**
     * Merges attributes with existed ones.
     *
     * @param array $attributes
     *
     * @return static
     */
    public function merge(array $attributes)
    {
        $this->attributes = array_merge($attributes, $this->attributes);

        return $this;
    }

    /**
     * Makes the notification be nag and show again and again.
     *
     * Note, if `DISABLE_NAG_NOTICES` is set to `true` your nag notices will be showed once.
     *
     * @param bool $condition
     *
     * @return static
     */
    public function nag(bool $condition = true)
    {
        $this->attributes['is-nag'] = $condition;

        return $this;
    }

    /**
     * Tells if it's a nag notice.
     *
     * @return bool
     */
    public function isNag(): bool
    {
        return $this->attributes['is-nag'] ?? false;
    }

    /**
     * Makes the notification be dismissible.
     *
     * @param bool $condition
     *
     * @return static
     */
    public function dismissible(bool $condition = true)
    {
        $this->attributes['is-dismissible'] = $condition;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDismissible(): bool
    {
        return $this->attributes['is-dismissible'] ?? false;
    }

    /**
     * Will be displayed in alternative way.
     *
     * @return static
     */
    public function asAlternative()
    {
        $this->attributes['classes'][] = 'notice-alt';

        return $this;
    }

    /**
     * Getter of the message.
     *
     * string|Noticeable
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * Getter of the type.
     *
     * @return string
     */
    public function type(): string
    {
        return $this->attributes['type'] ?? 'error';
    }

    /**
     * Tells do not wrap with <p>content</p>
     *
     * @return static
     */
    public function withoutWrapping()
    {
        $this->attributes['no-wpautop'] = true;

        return $this;
    }

    /**
     * Serialises the instance.
     *
     * @return string
     */
    public function serialize(): string
    {
        // We don't need to store the repository reference to a DB.
        $this->repository = null;

        return \serialize($this->toArray());
    }

    /**
     * Restore the instance.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = \unserialize($serialized);

        $this->hash = $data['hash'] ?? '';
        $this->message = $data['message'] ?? '';
        $this->attributes = $data['attributes'] ?? [];
        $this->conditions = $data['conditions'] ?? [];
    }
}

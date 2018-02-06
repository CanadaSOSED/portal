<?php

/**
 * Manages WordPress Meme Shortcode options.
 *
 * @author Carl Alexander
 */
class WPGens_RAF_Options
{
    /**
     * @var array
     */
    private $options;

    /**
     * Load the plugin options from WordPress.
     *
     * @return WPGens_RAF_Options
     */
    public static function load()
    {
        $options = get_option('wp_meme_shortcode', array());

        return new self($options);
    }

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Gets the option for the given name. Returns the default value if the
     * value does not exist.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($name, $default = null)
    {
        if (!$this->has($name)) {
            return $default;
        }

        return $this->options[$name];
    }

    /**
     * Checks if the option exists or not.
     *
     * @param string $name
     *
     * @return Boolean
     */
    public function has($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Sets an option. Overwrites the existing option if the name is already in use.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function set($name, $value)
    {
        $this->options[$name] = $value;
    }
}
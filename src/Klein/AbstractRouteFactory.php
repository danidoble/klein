<?php
/**
 * Klein (klein.php) - A fast & flexible router for PHP
 *
 * @author      Chris O'Hara <cohara87@gmail.com>
 * @author      Trevor Suarez (Rican7) (contributor and v2 refactorer)
 * @copyright   (c) Chris O'Hara
 * @link        https://github.com/klein/klein.php
 * @license     MIT
 */

namespace Klein;

/**
 * AbstractRouteFactory
 *
 * Abstract class for a factory for building new Route instances
 */
abstract class AbstractRouteFactory
{

    /**
     * Properties
     */

    /**
     * The namespace of which to collect the routes in
     * when matching, so you can define routes under a
     * common endpoint
     *
     * @type ?string
     */
    protected ?string $namespace;


    /**
     * Methods
     */

    /**
     * Constructor
     *
     * @param string|null $namespace The initial namespace to set
     */
    public function __construct(?string $namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * Gets the value of namespace
     *
     * @return ?string
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * Sets the value of namespace
     *
     * @param ?string $namespace The namespace from which to collect the Routes under
     * @return AbstractRouteFactory
     */
    public function setNamespace(?string $namespace): static
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Append a namespace to the current namespace
     *
     * @param string $namespace The namespace from which to collect the Routes under
     * @return AbstractRouteFactory
     */
    public function appendNamespace(string $namespace): static
    {
        $this->namespace .= $namespace;

        return $this;
    }

    /**
     * Build factory method
     *
     * This method should be implemented to return a Route instance
     *
     * @param callable $callback Callable callback method to execute on route match
     * @param string|null $path Route URI path to match
     * @param string|array|null $method HTTP Method to match
     * @param boolean $count_match Whether to count the route as a match when counting total matches
     * @param string|null $name The name of the route
     * @return Route
     */
    abstract public function build(callable $callback, ?string $path = null, null|string|array $method = null, bool $count_match = true, ?string $name = null): Route;
}

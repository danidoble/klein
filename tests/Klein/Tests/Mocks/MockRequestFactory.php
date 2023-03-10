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

namespace Klein\Tests\Mocks;

use Klein\Request;

/**
 * MockRequestFactory
 *
 * Allow for the simple creation of mock requests
 * (great for testing... ;))
 */
class MockRequestFactory
{

    /**
     * Create a new mock request
     *
     * @param string $uri
     * @param string $req_method
     * @param array $parameters
     * @param array $cookies
     * @param array $server
     * @param array $files
     * @param string|null $body
     * @return Request
     */
    public static function create(
        string $uri = '/',
        string $req_method = 'GET',
        array  $parameters = array(),
        array  $cookies = array(),
        array  $server = array(),
        array  $files = array(),
        string $body = null
    ): Request {
        // Create a new Request object
        $request = new Request(
            array(),
            array(),
            $cookies,
            $server,
            $files,
            $body
        );

        // Reformat
        $req_method = strtoupper(trim($req_method));

        // Set its URI and Method
        $request->server()->set('REQUEST_URI', $uri);
        $request->server()->set('REQUEST_METHOD', $req_method);

        // Set our parameters
        switch ($req_method) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
            case 'DELETE':
                $request->paramsPost()->replace($parameters);
                break;
            default:
                $request->paramsGet()->replace($parameters);
                break;
        }

        return $request;
    }
}

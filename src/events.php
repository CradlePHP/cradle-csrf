<?php //-->
/**
 * This file is part of a package designed for the CradlePHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Http\Request\RequestInterface;
use Cradle\Http\Response\ResponseInterface;

/**
 * Loads CSRF token in stage
 *
 * @param *Request  $request
 * @param *Response $response
 */
$this->on('csrf-load', function (RequestInterface $request, ResponseInterface $response) {
    //render the key
    $key = md5(uniqid());
    if($request->hasSession('csrf')) {
        $key = $request->getSession('csrf');
    }

    $request->setSession('csrf', $key);
    $response->setResults('csrf', $key);
});

/**
 * Validates CSRF
 *
 * @param *Request  $request
 * @param *Response $response
 */
$this->on('csrf-validate', function (RequestInterface $request, ResponseInterface $response) {
    $actual = $request->getStage('csrf');
    $expected = $request->getSession('csrf');

    //no longer needed
    $request->removeSession('csrf');

    if($actual !== $expected) {
        //prepare to error
        $message = 'We prevented a potential attack on our servers coming from the request you just sent us.';
        $message = $this->package('global')->translate($message);
        $response->setError(true, $message);
    }

    //it passed
});

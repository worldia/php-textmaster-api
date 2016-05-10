<?php

/*
 * This file is part of the Textmaster Api v1 client package.
 *
 * (c) Christian Daguerre <christian@daguer.re>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Textmaster\Unit\HttpClient;

use Textmaster\HttpClient\Listener\ErrorListener;

class ErrorListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldPassIfResponseNotHaveErrorStatus()
    {
        $response = $this->getMockBuilder('Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();
        $response->expects($this->once())
            ->method('isClientError')
            ->will($this->returnValue(false));

        $listener = new ErrorListener(array());
        $listener->onRequestError($this->getEventMock($response));
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\ErrorException
     */
    public function shouldNotPassWhenContentWasNotValidJson()
    {
        $response = $this->getMockBuilder('Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();
        $response->expects($this->once())
            ->method('isClientError')
            ->will($this->returnValue(true));
        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('fail'));

        $listener = new ErrorListener(array());
        $listener->onRequestError($this->getEventMock($response));
    }

    /**
     * @test
     * @expectedException \Textmaster\Exception\ErrorException
     */
    public function shouldNotPassWhenContentWasValidJsonButStatusIsNotCovered()
    {
        $response = $this->getMockBuilder('Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();
        $response->expects($this->once())
            ->method('isClientError')
            ->will($this->returnValue(true));
        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue(json_encode(array('message' => 'test'))));
        $response->expects($this->any())
            ->method('getStatusCode')
            ->will($this->returnValue(404));

        $listener = new ErrorListener(array());
        $listener->onRequestError($this->getEventMock($response));
    }

    // /**
    //  * @test
    //  * @dataProvider getErrorCodesProvider
    //  * @expectedException \Textmaster\Exception\ValidationFailedException
    //  */
    // public function shouldNotPassWhen422IsSentWithErrorCode($errorCode)
    // {
    //     $content = json_encode(array(
    //         'message' => 'Validation Failed',
    //         'errors'  => array(
    //             array(
    //                 'code'     => $errorCode,
    //                 'field'    => 'test',
    //                 'value'    => 'wrong',
    //                 'resource' => 'fake'
    //             )
    //         )
    //     ));

    //     $response = $this->getMockBuilder('Guzzle\Http\Message\Response')->disableOriginalConstructor()->getMock();
    //     $response->expects($this->once())
    //         ->method('isClientError')
    //         ->will($this->returnValue(true));
    //     $response->expects($this->once())
    //         ->method('getBody')
    //         ->will($this->returnValue($content));
    //     $response->expects($this->any())
    //         ->method('getStatusCode')
    //         ->will($this->returnValue(422));

    //     $listener = new ErrorListener(array());
    //     $listener->onRequestError($this->getEventMock($response));
    // }

    // public function getErrorCodesProvider()
    // {
    //     return array(
    //         array('missing'),
    //         array('missing_field'),
    //         array('invalid'),
    //         array('already_exists'),
    //     );
    // }

    private function getEventMock($response)
    {
        $mock = $this->getMockBuilder('Guzzle\Common\Event')->getMock();

        $request = $this->getMockBuilder('Guzzle\Http\Message\Request')->disableOriginalConstructor()->getMock();

        $request->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $mock->expects($this->any())
            ->method('offsetGet')
            ->will($this->returnValue($request));

        return $mock;
    }
}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\App\Action\Plugin;

class MassactionKeyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\App\Action\Plugin\MassactionKey
     */
    protected $plugin;

    /**
     * @var \Closure
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    protected function setUp()
    {
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->subjectMock = $this->getMock('Magento\Backend\App\AbstractAction', [], [], '', false);
        $this->requestMock = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->plugin = new \Magento\Backend\App\Action\Plugin\MassactionKey();
    }

    /**
     * @covers \Magento\Backend\App\Action\Plugin\MassactionKey::aroundDispatch
     *
     * @param $postData array|string
     * @param array $convertedData
     * @dataProvider aroundDispatchDataProvider
     */
    public function testAroundDispatchWhenMassactionPrepareKeyRequestExists($postData, $convertedData)
    {
        $this->requestMock->expects(
            $this->at(0)
        )->method(
            'getPost'
        )->with(
            'massaction_prepare_key'
        )->will(
            $this->returnValue('key')
        );
        $this->requestMock->expects($this->at(1))->method('getPost')->with('key')->will($this->returnValue($postData));
        $this->requestMock->expects($this->once())->method('setPost')->with('key', $convertedData);
        $this->assertEquals(
            'Expected',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function aroundDispatchDataProvider()
    {
        return [
            'post_data_is_array' => [['key'], ['key']],
            'post_data_is_string' => ['key, key_two', ['key', ' key_two']]
        ];
    }

    /**
     * @covers \Magento\Backend\App\Action\Plugin\MassactionKey::aroundDispatch
     */
    public function testAroundDispatchWhenMassactionPrepareKeyRequestNotExists()
    {
        $this->requestMock->expects(
            $this->once()
        )->method(
            'getPost'
        )->with(
            'massaction_prepare_key'
        )->will(
            $this->returnValue(false)
        );
        $this->requestMock->expects($this->never())->method('setPost');
        $this->assertEquals(
            'Expected',
            $this->plugin->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}

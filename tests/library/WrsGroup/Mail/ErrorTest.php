<?php
require_once realpath(APPLICATION_PATH . '/../tests/ModelTestCase.php');

class WrsGroup_Mail_ErrorTest extends ModelTestCase
{
    public function testSetErrorsSetsView()
    {
        $view = new Zend_View();
        $errors = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        $errors->exception = 'the_exception';
        $errors->request = 'the_request';
        $mail = new WrsGroup_Mail_Error();
        $mail->view = $view;
        $this->assertNull($mail->view->errors);
        $mail->setErrors($errors);
        $this->assertEquals('the_exception', $mail->view->exception);
        $this->assertEquals('the_request', $mail->view->request);
    }

    public function testPrependAndAppendSubject()
    {
        $mail = new WrsGroup_Mail_Error();
        $mail->setSubjectPrependText('Prepend: ');
        $mail->appendSubject('Append');
        $this->assertEquals('Prepend: Append', $mail->getSubject());
    }

    public function testSetBodyHtmlFromView()
    {
        $mail = new WrsGroup_Mail_Error();
        $mock = $this->getMock('Zend_View', array('render'));
        $mock->expects($this->any())
            ->method('render')
            ->will($this->returnValue('<p>some_html</p>'));
        $mail->setView($mock);
        $mail->setBodyHtmlFromView('some/path');
        $this->assertEquals(
            $mail->getBodyHtml()->getContent(),
            '<p>some_html</p>'
        );
    }
}

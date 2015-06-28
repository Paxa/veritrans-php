<?php

class VeritransTransactionTest extends PHPUnit_Framework_TestCase
{

    public function testStatus() {
      Veritrans_Config::$serverKey = 'My Very Secret Key';
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = VtFixture::read('vt_response_cc_status_settlement.json');

      $status = Veritrans_Transaction::status("Order-111");

      $this->assertEquals($status->status_code, "200");
      $this->assertEquals($status->order_id, "Order-111");
      $this->assertEquals($status->approval_code, "1416550071152");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.veritrans.co.id/v2/Order-111/status"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertFalse(array_key_exists("POST", $fields));
      $this->assertFalse(array_key_exists("POSTFIELDS", $fields));
    }

    public function testFailureStatus() {
      Veritrans_Config::$serverKey = 'My Very Secret Key';
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
        "status_code": "404",
        "status_message": "The requested resource is not found"
      }';

      try {
        $status = Veritrans_Transaction::status("Order-111");
      } catch (Exception $error) {
        $errorHappen = true;
        $this->assertEquals(
          $error->getMessage(),
          "Veritrans Error (404): The requested resource is not found");
      }

      $this->assertTrue($errorHappen);
      VT_Tests::reset();
    }

    public function testRealStatus() {
      try {
        $status = Veritrans_Transaction::status("Order-111");
      } catch (Exception $error) {
        $errorHappen = true;
        $this->assertEquals(
          $error->getMessage(),
          "Veritrans Error (401): Access denied due to unauthorized transaction, please check client or server key");
      }

      $this->assertTrue($errorHappen);
    }

    public function testApprove () {
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = VtFixture::read('vt_response_cc_approve.json');

      $approve = Veritrans_Transaction::approve("Order-111");

      $this->assertEquals($approve, "200");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.veritrans.co.id/v2/Order-111/approve"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"], null);
    }

    public function testCancel() {
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = VtFixture::read("vt_response_cc_cancel.json");

      $cancel = Veritrans_Transaction::cancel("Order-111");

      $this->assertEquals($cancel, "200");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.veritrans.co.id/v2/Order-111/cancel"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"], null);
    }

    public function tearDown() {
      VT_Tests::reset();
    }
}

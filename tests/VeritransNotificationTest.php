<?php

require_once(dirname(__FILE__) . '/../Veritrans.php');

class VeritransNotificationTest extends PHPUnit_Framework_TestCase
{

    public function testCanWorkWithJSON() {
        $tmpfname = tempnam(sys_get_temp_dir(), "veritrans_test");
        $notification = VtFixture::read('vt_notification_cc_capture.json');
        file_put_contents($tmpfname, $notification);

        VT_Tests::$stubHttp = true;
        VT_Tests::$stubHttpResponse = $notification;

        $notif = new Veritrans_Notification($tmpfname);

        $this->assertEquals($notif->transaction_status, "capture");
        $this->assertEquals($notif->payment_type, "credit_card");
        $this->assertEquals($notif->order_id, "2014040745");
        $this->assertEquals($notif->gross_amount, "2700");

        unlink($tmpfname);
    }

    public function tearDown() {
      VT_Tests::reset();
    }
}
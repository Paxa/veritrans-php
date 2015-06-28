<?php
require_once 'VtIntegrationTest.php';

class VtWebIntegrationTest extends VtIntegrationTest {
	public function testVtWeb() {
		$charge_params = VtFixture::build('vt_charge.json',
				array(
					"transaction_details" => array("order_id" => rand()),
					"payment_type" => "vtweb"
				)
			);
		$redirect_url = Veritrans_VtWeb::getRedirectionUrl($charge_params);

		$this->assertRegExp("/https:\/\/vtweb.sandbox.veritrans.co.id\/v2\/vtweb\/[\w\-]+/", $redirect_url);
	}
}
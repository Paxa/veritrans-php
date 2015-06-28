<?php
require_once 'VtIntegrationTest.php';

class VtTransactionIntegrationTest extends VtIntegrationTest {
	private $charge_response;
	private $charge_params;

	/**
	 * @before
	 */
	public function doPermataVaTransaction(){
		$this->charge_params = VtFixture::build('vt_charge.json',
			array(
				"transaction_details" => array("order_id" => rand()),
				"payment_type" => "bank_transfer",
				"bank_transfer" => array("bank" => "permata")
			));
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
	}

	public function testStatusPermataVa() {
		$status_response = Veritrans_Transaction::status($this->charge_response->transaction_id);

		$this->assertEquals($status_response->status_code, '201');
		$this->assertEquals($status_response->transaction_status, 'pending');
		$this->assertEquals($status_response->order_id, $this->charge_params['transaction_details']['order_id']);
		$this->assertEquals($status_response->gross_amount, $this->charge_params['transaction_details']['gross_amount']);
		$this->assertEquals($status_response->transaction_id, $this->charge_response->transaction_id);
		$this->assertEquals($status_response->transaction_time, $this->charge_response->transaction_time);
		$this->assertEquals($status_response->status_message, 'Success, transaction found');

		$this->assertTrue(isset($status_response->signature_key));
	}

	public function testCancelPermataVa() {
		$cancel_status_code = Veritrans_Transaction::cancel($this->charge_response->transaction_id);

		$this->assertEquals($cancel_status_code, '200');
	}
}
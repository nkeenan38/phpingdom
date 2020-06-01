<?php

use PHPUnit\Framework\TestCase;

class PingdomTest extends TestCase
{
	private $token;

	protected function setUp()
	{
		global $token;

		$this->token = $token;

		parent::setUp();
	}

	public function testCredentials()
	{
    $this->assertNotEmpty($this->token);
	}

	/**
	 * @depends testCredentials
	 */
	public function testChecks()
	{
		$pingdom = new \Pingdom\Client($this->token);
		$checks  = $pingdom->getChecks();
		$this->assertTrue(is_array($checks));
    $this->assertNotEmpty($checks);
		return $checks;
	}

	/**
	 * @depends testCredentials
	 */
	public function testProbes()
	{
		$pingdom    = new \Pingdom\Client($this->token);
		$probes     = $pingdom->getProbes();
		$attributes = array('id', 'country', 'city', 'name', 'active', 'hostname', 'ip', 'countryiso');

		$this->assertTrue(is_array($probes));

		foreach ($probes as $probe) {
			$this->assertInstanceOf('Pingdom\Server', $probe);

			foreach ($attributes as $attribute) {
				$this->assertObjectHasAttribute($attribute, $probe);
			}

			$this->assertTrue(is_int($probe->getId()));
			$this->assertTrue(is_bool($probe->getActive()));

			$this->assertTrue(is_string($probe->getCity()));
			$this->assertStringMatchesFormat('%s', $probe->getCity());

			$this->assertTrue(is_string($probe->getCountry()));
			$this->assertStringMatchesFormat('%s', $probe->getCountry());

			$this->assertTrue(is_string($probe->getCountryiso()));
			$this->assertStringMatchesFormat('%c%c', $probe->getCountryiso());

			$this->assertTrue(is_string($probe->getHostname()));
			$this->assertStringMatchesFormat('%s', $probe->getHostname());

			$this->assertTrue(is_string($probe->getIp()));
			$this->assertStringMatchesFormat('%d.%d.%d.%d', $probe->getIp());

			$this->assertTrue(is_string($probe->getName()));
			$this->assertStringMatchesFormat('%s', $probe->getName());

			$this->assertTrue(is_string((string) $probe));
			$this->assertEquals($probe->getName(), (string) $probe);
		}
	}

	/**
	 * @depends testChecks
	 */
	public function testResults(array $checks)
	{
		$keys = array(
			'id',
			'created',
			'name',
			'hostname',
			'resolution',
			'type',
			'lastresponsetime',
			'status',
		);

		foreach ($checks as $check) {
			foreach ($keys as $key) {
				$this->assertArrayHasKey($key, $check);
			}
		}
	}

	/**
	 * @depends testChecks
	 */
	public function testAverageSummary(array $checks)
	{
		$pingdom = new \Pingdom\Client($this->token);
		foreach ($checks as $check) {
			foreach (array('hour', 'day', 'week') as $resolution) {
				foreach ($pingdom->getPerformanceSummary($check['id']) as $summary) {
					error_log(json_encode($summary));
				}
			}
		}
	}

	/**
	 * @depends testChecks
	 */
	public function testPerformanceSummary(array $checks)
	{
		$pingdom = new \Pingdom\Client($this->token);
		$keys = array(
			'unmonitored',
			'uptime',
			'avgresponse',
			'starttime',
			'downtime',
		);
		foreach ($checks as $check) {
			foreach (array('hour', 'day', 'week') as $resolution) {
				foreach ($pingdom->getPerformanceSummary($check['id'], $resolution) as $summary) {
					foreach ($keys as $key) {
						$this->assertArrayHasKey($key, $summary);
					}
				}
			}
		}
	}
}
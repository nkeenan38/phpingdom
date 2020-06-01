<?php

namespace Pingdom;
use GuzzleHttp\Client as GClient;

/**
 * Client object for executing commands on a web service.
 */
class Client
{
	/**
	 * @var string
	 */
  private $token;

  /**
	 * @var GClient
	 */
	private $client;

	/**
	 * @param string $token
	 * @return Client
	 */
	public function __construct($token)
	{
		$this->token  = $token;
    $this->client = new GClient([
      'base_uri' => 'https://api.pingdom.com/api/3.1/',
      'headers' => [
        'Authorization' => "Bearer $this->token"
      ]
    ]);
		return $this;
	}


	/**
	 * Returns the token.
	 *
	 * @return string
	 */
	protected function getToken()
	{
	  return $this->token;
	}

	/**
	 * Returns a list overview of all checks
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function getChecks()
	{
    try {
      $response = $this->client->get('checks');
    } catch (RequestException $e) {
      echo $e->getRequest() . "\n";
      if ($e->hasResponse()) {
        echo $e->getResponse() . "\n";
      }
    }
		$response = json_decode($response->getBody(), true);
		return $response['checks'];
	}

	/**
	 * Returns a list of all Pingdom probe servers
	 *
	 * @return Server[]
	 */
	public function getProbes()
	{
    try {
      $response = $this->client->get('probes');
    } catch (RequestException $e) {
      echo $e->getRequest() . "\n";
      if ($e->hasResponse()) {
        echo $e->getResponse() . "\n";
      }
    }
		$response = json_decode($response->getBody(), true);
		$probes   = array();

		foreach ($response['probes'] as $attributes) {
			$probes[] = new Server($attributes);
		}

		return $probes;
	}

	/**
	 * Return a list of raw test results for a specified check
	 *
	 * @param int        $checkId
	 * @param int        $limit
	 * @param array|null $probes
	 * @return array
	 */
	public function getResults($checkId, $limit = 100, array $probes = null)
	{
    try {
      $query = [
        'limmit' => $limit
      ];
      if (!empty($probes)) {
        $query['probes'] = implode(',', $probes);
      }
      $response = $this->client->get('results/' . $checkId, [
        'query' => $query
      ]);
    } catch (RequestException $e) {
      echo $e->getRequest() . "\n";
      if ($e->hasResponse()) {
        echo $e->getResponse() . "\n";
      }
    }
		$response = json_decode($response->getBody(), true);
		return $response['results'];
  }
  
  /**
	 * Get the average time/uptime value for a specified
	 *
	 * @param int $checkId
	 * @param int $from
   * @param int $to
	 * @return array
	 */
	public function getAverageSummary($checkId, $from = null, $to = null)
	{
    try {
      $query = [
        'include_uptime' => 'true'
      ];
      if (!empty($from)) {
        $query['from'] = $from;
      }
      if (!empty($to)) {
        $query['to'] = $to;
      }
      $response = $this->client->get('summary.average/' . $checkId, [
        'query' => $query
      ]);
    } catch (RequestException $e) {
      echo $e->getRequest() . "\n";
      if ($e->hasResponse()) {
        echo $e->getResponse() . "\n";
      }
    }
		$response = json_decode($response->getBody(), true);
		return $response['summary'];
  }

	/**
	 * Get Intervals of Average Response Time and Uptime During a Given Interval
	 *
	 * @param int $checkId
	 * @param string $resolution
	 * @return array
	 */
	public function getPerformanceSummary($checkId, $resolution = 'hour')
	{
    try {
      $response = $this->client->get('summary.performance/' . $checkId, [
        'query' => [
          'resolution'  => $resolution,
          'includeuptime' => 'true'
        ]
      ]);
    } catch (RequestException $e) {
      echo $e->getRequest() . "\n";
      if ($e->hasResponse()) {
        echo $e->getResponse() . "\n";
      }
    }
		$response = json_decode($response->getBody(), true);
		return $response['summary'][$resolution . 's'];
  }

  /**
	 * Get a list of status changes for a specified check
	 *
	 * @param int $checkId
	 * @param int $from
   * @param int $to
   * @param string $order
	 * @return array
	 */
	public function getOutageSummary($checkId, $from = null, $to = null, $order = 'asc')
	{
    try {
      $query = [
        'order' => $order
      ];
      if (!empty($from)) {
        $query['from'] = $from;
      }
      if (!empty($to)) {
        $query['to'] = $to;
      }
      $response = $this->client->get('summary.average/' . $checkId, [
        'query' => $query
      ]);
    } catch (RequestException $e) {
      echo $e->getRequest() . "\n";
      if ($e->hasResponse()) {
        echo $e->getResponse() . "\n";
      }
    }
		$response = json_decode($response->getBody(), true);
		return $response['summary']['states'];
  }
}
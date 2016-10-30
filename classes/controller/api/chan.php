<?php

namespace Foolz\FoolFuuka\Controller\Api;

use Foolz\FoolFuuka\Plugins\BoardStatistics\Model\BoardStatistics as BS;

class BoardStatistics extends \Foolz\FoolFuuka\Controller\Api\Chan
{
    /**
     * @var BS
     */
    protected $board_stats;

    public function before()
    {
        $this->board_stats = $this->getContext()->getService('foolfuuka-plugin.board_statistics');
        parent::before();
    }

    public function get_statistics()
    {
        $response = [];
        $stat = $this->getQuery('stat');
        if (!$stat) {
            $stats = $this->board_stats->getAvailableStats();
            foreach ($stats as $key => $stat) {
                $response['enabled_statistics'][] = [$key => $stat['name']];
            }
            return $this->response->setData($response);
        }

        if (!$this->check_board()) {
            return $this->response->setData(['error' => _i('No board selected.')])->setStatusCode(422);
        }

        $stats = $this->board_stats->checkAvailableStats($stat, $this->radix);

        if (!is_array($stats)) {
            return $this->response->setData(['error' => _i('Statistic currently not available.')])->setStatusCode(422);
        }

        $response['statistic']['name'] = $stats['info']['name'];
        if (isset($stats['timestamp'])) {
            $response['statistic']['timestamp'] = $stats['timestamp'];
        }
        $response['data'] = json_decode($stats['data']);

        return $this->response->setData($response);
    }
}
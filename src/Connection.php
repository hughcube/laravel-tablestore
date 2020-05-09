<?php

namespace HughCube\TableStore;

use Aliyun\OTS\OTSClient;
use Illuminate\Database\Connection as BaseConnection;
use Illuminate\Support\Arr;

class Connection extends BaseConnection
{
    /**
     * The OTSClient connection handler.
     * @var OTSClient
     */
    protected $connection;

    /**
     * Create a new database connection instance.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;

        // Create the connection
        $this->connection = $this->createConnection($config);

        $this->useDefaultPostProcessor();

        $this->useDefaultSchemaGrammar();

        $this->useDefaultQueryGrammar();
    }

    /**
     * @inheritdoc
     */
    public function getSchemaBuilder()
    {
        return new Schema\Builder($this);
    }

    /**
     * return OTSClient object.
     * @return OTSClient
     */
    public function getOTSClient()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatabaseName()
    {
        return $this->getOTSClient()->getClientConfig()->getInstanceName();
    }

    /**
     * Create a new OTSClient connection.
     * @param array $config
     * @return OTSClient
     */
    protected function createConnection(array $config)
    {
        $config['ErrorLogHandler'] = Arr::get($config, 'ErrorLogHandler', false);
        $config['DebugLogHandler'] = Arr::get($config, 'DebugLogHandler', false);

        return new OTSClient($config);
    }

    /**
     * @inheritdoc
     */
    public function disconnect()
    {
        unset($this->connection);
    }

    /**
     * @inheritdoc
     */
    public function getDriverName()
    {
        return 'tableStore';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultPostProcessor()
    {
        return new Query\Processor();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultQueryGrammar()
    {
        return new Query\Grammar();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultSchemaGrammar()
    {
        return new Schema\Grammar();
    }

    /**
     * Dynamically pass methods to the connection.
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
//        echo "<pre>";
//        var_dump(array_map(function ($item){
//            return [
//                'file' => isset($item['file']) ? $item['file'] : null,
//                'line' => isset($item['line']) ? $item['line'] : null,
//                'function' => isset($item['function']) ? $item['function'] : null,
//            ];
//        }, debug_backtrace()));
//        echo "</pre>";
//        die;

        return call_user_func_array([$this->getOTSClient(), $method], $parameters);
    }
}

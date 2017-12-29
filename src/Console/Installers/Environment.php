<?php

namespace Urbics\Civitools\Console\Installers;

class Environment
{
    /**
     * Adds CiviCRM values to the .env file.
     *
     * @param array $params
     * @return   $this
     */
    public function setEnvironment($params = [])
    {
        if (env('CIVI_DB_CONNECTION')) {
            return $this;
        }
        
        $oldEnv = $this->readEnvironment();
        $params = [
            'CIVI_DB_CONNECTION' => 'civicrm',
            'CIVI_DB_DATABASE' => 'civicrm',
            'CIVI_DB_HOST' => '127.0.0.1',
            'CIVI_DB_PORT' => '3306',
            'CIVI_DB_USERNAME' => (isset($oldEnv['DB_USERNAME']) ? $oldEnv['DB_USERNAME'] : "civiuser"),
            'CIVI_DB_PASSWORD' => 'secret',
        ];
        $addEnv = '';
        foreach ($params as $key => $value) {
            if (empty($oldEnv[$key])) {
                $addEnv .= $key ."=" . $value . "\n";
            }
        }
        $this->appendEnvironment($addEnv);

        return $this;
    }

    /**
     * Retrieve values from .env as array.
     *
     * @return array
     */
    private function readEnvironment()
    {
        $env = preg_split('/\s+/', file_get_contents(base_path('.env')));
        $oldEnv = [];
        foreach ($env as $value) {
            $val = explode("=", $value, 2);
            if (!empty($val[0])) {
                $oldEnv[$val[0]] = (empty($val[1]) ? '' : $val[1]);
            }
        }

        return $oldEnv;
    }

    /**
     * Append the CiviCRM environment values to .env.
     *
     * @param  string $addEnv
     * @return null
     */
    private function appendEnvironment($addEnv)
    {
        if (!empty($addEnv)) {
            $addEnv = "\n" . $addEnv;
            $oldEnv = file_get_contents(base_path('.env'));
            $newEnv = $oldEnv . $addEnv;
            file_put_contents(base_path('.env'), $newEnv);
        }
    }
}

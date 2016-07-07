<?php

namespace Vaites\ApacheTika\Clients;

use Exception;
use Vaites\ApacheTika\Client;
use Vaites\ApacheTika\Metadata\Metadata;

/**
 * Apache Tika command line interface client.
 *
 * @author  David Martínez <contacto@davidmartinez.net>
 *
 * @link    http://wiki.apache.org/tika/TikaJAXRS
 * @link    https://tika.apache.org/1.12/formats.html
 */
class CLIClient extends Client
{
    const MODE = 'cli';

    /**
     * Apache Tika app path.
     *
     * @var string
     */
    protected $path = null;

    /**
     * Java binary path
     *
     * @var string
     */
    protected $java = null;

    /**
     * Configure client
     *
     * @param string $path
     * @param string $java
     *
     * @throws Exception
     */
    public function __construct($path = null, $java = null)
    {
        if($path)
        {
            $this->path = $path;
        }

        if($java)
        {
            $this->java = $java;
        }
    }

    /**
     * Configure and make a request and return its results.
     *
     * @param string $type
     * @param string $file
     *
     * @return string
     *
     * @throws \Exception
     */
    public function request($type, $file = null)
    {
        // check if is cached
        if(isset($this->cache[sha1($file)][$type]))
        {
            return $this->cache[sha1($file)][$type];
        }

        // parameters for command
        $arguments = [];
        switch($type)
        {
            case 'html':
                $arguments[] = '--html';
                break;

            case 'lang':
                $arguments[] = '--language';
                break;

            case 'mime':
                $arguments[] = '--detect';
                break;

            case 'meta':
                $arguments[] = '--metadata --json';
                break;

            case 'text':
                $arguments[] = '--text';
                break;

            case 'version':
                $arguments[] = '--version';
                break;

            default:
                throw new Exception("Unknown type $type");
        }

        // invalid local file
        if($file && !preg_match('/^http/', $file) && !file_exists($file))
        {
            throw new Exception("File $file can't be opened");
        }
        // invalid remote file
        elseif($file && preg_match('/^http/', $file) && !preg_match('/200/', @get_headers($file)[0]))
        {
            throw new Exception("File $file can't be opened", 2);
        }

        // add last argument
        if($file)
        {
            $arguments[] = "'$file'";
        }

        // build command
        $command = ($this->java ?: 'java') . " -jar '{$this->path}' " . implode(' ', $arguments);

        // run command
        $exit = -1;
        $response = null;
        $descriptors = [['pipe', 'r'], ['pipe', 'w'], ['file', '/tmp/tika-error.log', 'a']];
        $process = proc_open($command, $descriptors, $pipes);

        // get output if command runs ok
        if(is_resource($process)) 
        {
            fclose($pipes[0]);
            $response = trim(stream_get_contents($pipes[1]));
            fclose($pipes[1]);
            $exit = proc_close($process);
        }
        // exception if command fails
        else
        {
            throw new Exception("Error running command $command");
        }

        // exception if exit value is not zero
        if($exit > 0)
        {
            throw new Exception("Unexpected exit value ($exit) for command $command");
        }

        // metadata response
        if($type == 'meta')
        {
            // fix for invalid? json returned only with images
            $response = str_replace(basename($file) . '"}{', '", ', $response);

            $response = Metadata::make($response, $file);
        }

        // cache certain responses
        if(in_array($type, ['lang', 'meta']))
        {
            $this->cache[sha1($file)][$type] = $response;
        }

        return $response;
    }
}

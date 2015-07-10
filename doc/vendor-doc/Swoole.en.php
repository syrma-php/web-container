<?php
##
# @source https://github.com/eaglewu/swoole-ide-helper/blob/master/Swoole.php
# Use Google translation :)
##

/**
 * Swoole Develop Structure
 *
 * Swoole structure to facilitate the development process in view documents,
 * and a shield IDE undefined tips for quick view function usage.
 *
 * This document usage
 *
 * Ordinary IDE:
 * Development Swoole project also opens in the IDE / import this file.
 * If the IDE comes Include Path features (such as: PhpStorm), specify the file
 * path.
 *
 * PhpStorm another way:
 * WinRAR open <Phpstorm_Dir> /plugins/php/lib/php.jar file
 * Copy swoole.php to the path: com \ jetbrains \ php \ lang \ psi \ stubs \
 * data \
 * Save the file and restart Phpstorm.
 *
 * PS: Please backup before replacing php.jar easy recovery if an error occurs
 * :)
 *
 * Author: EagleWu <eaglewudi@gmail.com>
 * Date: 2014/01/17
 *
 */


/**
 * Swoole_server_set function is used to set various parameters swoole_server
 * runtime
 *
 * param \ Swoole_server $serv
 * param $Arguments
 */
function swoole_server_set($serv, array $arguments)
{
}


/**
 * Create a swoole server resource object
 *
 * param String $host parameter specifies the listener's ip address, such as
 * 127.0.0.1, or outside the network address, or the address 0.0.0.0 listening all
 * param Int $port listening port, such as 9501, less than 1024 port monitor
 * requires root privileges, will fail if this port is in use server-start
 * param Int $mode to run mode, swoole provides three modes of operation, the
 * default is multi-process mode
 * param Int $sock_type specified socket type and supports TCP / UDP, TCP6 /
 * UDP64 species
 */
function swoole_server_create($host, $port, $mode = SWOOLE_PROCESS, $sock_type = SWOOLE_SOCK_TCP)
{
}


/**
 * Increased listening port
 *
 * You can mix UDP / TCP, while listening intranet and extranet port
 * Business code by calling swoole_connection_info to get a connection from
 * which port
 *
 * param \ Swoole_server $serv
 * param String $host
 * param Int $port
 * return Void
 */
function swoole_server_addlisten($serv, $host = '127.0.0.1', $port = 9502)
{
}


/**
 * Set the timer
 *
 * The second parameter is the interval timer, in milliseconds.
 * Minimum particles swoole timer is 1 ms, support multiple timers.
 * This function can be used in worker process. Or by setting timer_interval
 * swoole_server_set to adjust the minimum interval timer.
 *
 * Following the increase timer callback function to set onTimer Server,
 * otherwise it will cause serious errors.
 * Multiple timers will callback function.
 * In this function requires its own switch, depending on the value interval to
 * determine from which timer.
 *
 * param \ Swoole_server $serv
 * param Int $interval
 * return Bool
 */
function swoole_server_addtimer($serv, $interval)
{
}


/**
 * Set Server event callback function
 *
 * The first parameter is the resource object swoole
 * The second parameter is the name of the callback is not case sensitive,
 * specific reference to the callback function list contents
 * The third function is a PHP callback function, it can be a string, an array
 * of anonymous functions.
 *
 * After setting a successful return true. If $event_name complete mistake
 * returns false.
 *
 * OnConnect / onClose / onReceive these three callback function must be set,
 * other event callback function is optional.
 * If you set the timer timer, onTimer event callback function must also be set
 *
 * param \ Swoole_server $serv
 * param String $event_name
 * param Callable $event_callback_function
 * return Bool
 */
function swoole_server_handler($serv, $event_name, $event_callback_function)
{
}


/**
 * Start server, listens to all TCP / UDP port
 *
 * Creates worker_num + 2 processes after the successful launch. The main
 * process + Manager process + n * Worker process.
 * Start expansion will throw a fatal error in the failure, check php error_log
 * information. errno = {number} is the standard Linux Errno, refer to the
 * relevant documentation.
 * If you turn the log_file setting, message will be printed to the specified
 * Log file.
 *
 * If you want to start at boot automatically run your Server, you can join in
 * the /etc/rc.local file:
 *
 * /Usr/bin/php/data/webroot/www.swoole.com/server.php
 *
 * A common mistake there and shoot the wrong methods:
 *
 * 1, bind port failed because the other process has occupied this port
 * 2 Required callback function is not set, failed to start
 * 3, php has a fatal error codes, please check the php error message
 * 4, execute ulimit -c unlimited, open core dump, to see if there are mistakes
 * 5. Close daemonize, close log, so that the error message can be printed to
 * the screen
 *
 * param \ Swoole_server $serv
 * return Bool
 */
function swoole_server_start($serv)
{
}


/**
 * Graceful Restart Server
 *
 * A back-end server busy all the time to process the request, if an
 * administrator to terminate / restart the server program kill process by the
 * way, could lead to code execution just half ended.
 * In this case can produce inconsistent data. As trading system, pay logic of
 * the next segment is shipped, it is assumed after paying logic process is
 * terminated.
 * Cause users to pay money, but not shipped, the consequences are very
 * serious.
 *
 * Swoole provides a flexible termination / restart mechanism, the
 * administrator only needs to send a specific signal, worker process can be
 * safely ended Server SwooleServer.
 *
 * SIGTREM: This signal is sent to the main server will terminate the process
 * safety
 * SIGUSR1: SIGUSR1 signal is sent to the management process, the smooth
 * restart all worker processes in the PHP code can call swoole_server_reload ($
 * serv) to complete this operation
 *
 * param \ Swoole_server $serv
 * return Void
 */
function swoole_server_reload($serv)
{
}


/**
 * Close the client connection
 *
 * Server initiative to close the connection, the same trigger onClose event.
 * Do not write clean-up logic after close, should be placed into onClose
 * callback process.
 *
 * param \ Swoole_server $serv
 * param Int $fd
 * param Int $from_id
 * return Bool
 */
function swoole_server_close($serv, $fd, $from_id = 0)
{
}


/**
 * Send the data to the client
 *
 * $Data length can be arbitrary. It will be split over the extended
 * functions.
 * If the UDP protocol, will send packets directly in the worker process.
 * Send a successful returns true, if the connection has been closed or failed
 * to send returns false.
 *
 * param \ Swoole_server $serv
 * param Int $fd
 * param String $data
 * param Int $from_id
 * return Bool
 */
function swoole_server_send($serv, $fd, $data, $from_id = 0)
{
}


/**
 * Get information about client connections
 *
 * Returns an array of meanings:
 * From_id from which the poll threads
 * From_fd from which server socket
 * From_port from which Server port
 * Port remote_port client connections
 * Remote_ip ip client connections
 *
 * The following v1.6.10 increase
 * Connect_time connection time
 * Last_time last transmission time data
 *
 * param \ Swoole_server $serv
 * param Int $fd
 * return Array on success or false on failure.
 */
function swoole_connection_info($serv, $fd)
{
}


/**
 * Traverse the current Server for all client connections
 *
 * This function takes three parameters, the first parameter is the server
 * resource object, the second parameter is the starting fd, the third parameter
 * is how many of each page to take the maximum can not exceed 100.
 * Successful call will return a numerically indexed array element is taken to
 * the $fd.
 * Array is sorted from smallest to largest, the last $fd as the new start_fd
 * try to get it again.
 *
 * param \ Swoole_server $serv
 * param Int $start_fd
 * param Int $pagesize
 * return Array on success or false on failure
 */
function swoole_connection_list($serv, $start_fd = 0, $pagesize = 10)
{
}


/**
 * Name Sets Process
 *
 * After modifying the process name, see the ps command will no longer be php
 * your_file.php. But set a string.
 * This function takes a string parameter.
 * Cli_set_process_title This function is provided with PHP5.5 is the same, but
 * can be used for any version swoole_set_process_name PHP5.2 above.
 *
 * param String $name
 * return Void
 */
function swoole_set_process_name($name)
{
}


/**
 * The Socket is added to the reactor event listeners in swoole
 *
 * This function can be used in the Server or Client mode
 *
 * Parameter 1 file descriptor for the socket;
 * Parameter 2 to the callback function, the function name can be a string, an
 * object + method, class static methods or anonymous function, when the socket is
 * readable callback making.
 *
 * Server program will be increased to the reactor in server socket.
 * Client program, if it is the first call to this function will automatically
 * create a reactor, and add this socket, the program will wait here.
 * Code swoole_event_add function after not executed. When calling
 * swoole_event_exit will stop wait, program execution continues downwards.
 * The second call only adds to the reactor in the socket, start listening
 * event
 *
 * param Int $sock
 * param \\ Is_callable $callback
 * param $Write_callback
 * param $Flag
 * return Bool
 */
function swoole_event_add($sock, $read_callback = NULL, $write_callback = NULL, $flag = NULL)
{
}

/**
 * Modify the socket's event settings
 * You can modify the read / write callback event is set and the event type
 *
 * param $Sock
 * param $Read_callback
 * param Null $write_callback
 * param Null $flag
 */
function swoole_event_set($sock, $read_callback = NULL, $write_callback = NULL, $flag = NULL)
{
}

/**
 * Removed from the reactor in listening Socket
 *
 * Swoole_event_del should be used in pairs with swoole_event_add
 *
 * param Int $sock
 * return Bool
 */
function swoole_event_del($sock)
{
}


/**
 * Exit polling event
 *
 * return Void
 */
function swoole_event_exit()
{
}

/**
 * Asynchronous write
 * param Mixed $socket
 * param String $data
 */
function swoole_event_write(mixed $socket, string $data)
{

}

/**
 * Get MySQLi the socket file descriptor
 *
 * The socket can be mysql to swoole, perform asynchronous MySQL queries.
 * If you want to use asynchronous MySQL, the need to develop --enable-async-
 * mysql when compiling swoole
 * Swoole_get_mysqli_sock only supports mysqlnd drive, php5.4 following
 * versions do not support this feature
 *
 * param Mysqli $db
 * return Int
 */
function swoole_get_mysqli_sock(\ mysqli $db)
{
}


/**
 * Delivery asynchronous tasks to task_worker pool
 *
 * This function will return immediately, worker process can continue to
 * process new requests.
 * This function is used to slow the task to execute asynchronously, such as a
 * chat room server, you can use it to send a broadcast.
 * When the task is completed, the call swoole_server_finish ($serv, "finish")
 * in task_worker in;
 * Tell worker process this task has been completed. Of course
 * swoole_server_finish is optional.
 *
 * Send the $data must be a string, an array or object if it is, please
 * serialize processing business code, and unserialize in onTask / onFinish in.
 * $Data may be binary data, the maximum length is 8K. The string can be
 * compressed using gzip.
 *
 * Use swoole_server_task must set onTask and onFinish callback Server,
 * Otherwise swoole_server_start will fail. This callback will be called in
 * task_worker process.
 *
 * $Task_id function returns a number that represents this task ID. If you
 * have finish respond, onFinish callback will carry $task_id parameters.
 *
 * Number task_worker in swoole_server_set parameter adjustments such as
 * task_worker_num => 64, indicating that the startup process 64 to receive
 * asynchronous tasks.
 * Swoole_server_task $data can be sent and swoole_server_finish maximum
 * length must not exceed 8K, this parameter by SW_BUFFER_SIZE macro control.
 *
 * param \ Swoole_server $serv
 * param String $data
 * return Int $task_id
 */
function swoole_server_task($serv, $data)
{
}


/**
 * Task_worker process of notification worker process, the task has been
 * completed delivery
 *
 * This function can be passed to the worker process result data
 * Use swoole_server_finish onFinish callback function must be set to Server.
 * OnTask callback function can only be used to process task_worker
 * Swoole_server_finish is optional. If the worker process does not care about
 * the results of task execution, you can not call this function
 *
 * param \ Swoole_server $serv
 * param String $response
 * return Void
 */
function swoole_server_finish($serv, $response)
{
}


/**
 * Delete timers
 *
 * $Interval parameters for the timer interval
 * According to distinguish different timer timer
 *
 * param \ Swoole_server $serv
 * param Int $interval
 * return Void
 */
function swoole_server_deltimer($serv, $interval)
{
}


/**
 * Turn off the server
 *
 * This function can be used in the worker process.
 *
 * param \ Swoole_server $serv
 * return Void
 */
function swoole_server_shutdown($serv)
{
}


/**
 * Delivery blockage task to task process pool
 *
 * Taskwait role and task the same way, for the delivery of an asynchronous
 * task to task process pool for execution.
 * The difference is that the task is blocked taskwait wait until the task is
 * completed or a timeout return.
 * $Result as a result of the tasks performed by the $serv-> finish function
 * issues. If the task timeout, where returns false.
 *
 * Taskwait is a block interface, if your Server is a full asynchronous please
 * do not use it
 *
 * param String $task_data
 * param Float $timeout
 * return String
 */
function swoole_server_taskwait($task_data, $timeout = 0.5)
{
}

/**
 * For event polling
 *
 * PHP5.4 previous version did not join in ZendAPI the registered shutdown
 * function. So at the end of the script swoole can not automate event polling.
 * Lower than version 5.4, you need at the end of your PHP script plus
 * swoole_event_wait function, make the script starts event polling.
 *
 * 5.4 or later you do not need to add this function.
 *
 * return Void
 */
function swoole_event_wait()
{
}

/**
 * Add a timer that can be used in the client environment and fpm
 *
 * param $Interval
 * param $Callback
 * return Int
 */
function swoole_timer_add($interval, $callback)
{
}

/**
 * One-shot timer, in milliseconds callback function after N
 * param $Ms
 * param $Callback
 * param $User_param
 * return Int
 */
function swoole_timer_after($ms, $callback, $user_param = null)
{
}

/**
 * Delete timers
 *
 * param $Interval
 */
function swoole_timer_del($interval)
{
}

/**
 * Delete timers
 * param $Timer_id
 * return Bool
 */
function swoole_timer_clear($timer_id)
{
}

/**
 * Add TICK timer
 * param $Ms
 * param $Callback
 * param Null $params
 * return Int
 */
function swoole_timer_tick($ms, $callback, $params = null)
{

}

/**
 * Get swoole extended version number, such as 1.6.10
 *
 * return String
 */
function swoole_version()
{
}

/**
 * The standard Unix Errno error codes into error message
 *
 * param Int $errno
 */
function swoole_strerror($errno)
{
}

/**
 * Get the last error code system call, equivalent to the C / C ++ the errno
 * variable.
 *
 * return Int
 */
function swoole_errno()
{
}


/**
 * This function is used to obtain the IP address of the machine all network
 * interfaces,
 * Currently only IPv4 addresses returned, returns, will filter out the local
 * loop address 127.0.0.1.
 * Result is an array of associative array interface name is key.
 * For example, array ("eth0" => "192.168.1.100")
 *
 * return Array
 */
function swoole_get_local_ip()
{
}


/**
 * Asynchronous read file contents
 * This function will return immediately after the call, callback function when
 * finished reading the file will be a callback to develop.
 * Callback ($filename, $content)
 *
 * Swoole_async_readfile will copy all the contents of the file into memory, it
 * can not be used for reading large files
 * If you want to read large files, use swoole_async_read function
 * Swoole_async_readfile 4M maximum read documents, subject to
 * SW_AIO_MAX_FILESIZE macro
 *
 * param String $filename
 * param Mixed $callback
 */
function swoole_async_readfile($filename, $callback)
{
}

/**
 * Asynchronous write files, it will return immediately after calling this
 * function will automatically callback specified callback function when the
 * writing is completed
 * Callback ($filename)
 *
 * Swoole_async_writefile 4M maximum writable files
 * Swoole_async_writefile can not specify a callback function
 *
 * param String $filename
 * param String $content
 * param Callback $callback
 */
function swoole_async_writefile($filename, $content, $callback)
{
}

/**
 * Asynchronous Read file
 *
 * Use this function to read the file is non-blocking, when the read operation
 * is completed automatically develop a callback function
 * This function swoole_async_readfile different, it is to fetch, it can be
 * used to read large files.
 * Read-only $trunk_size bytes each, will not take up too much memory
 *
 * Callback ($filename, $content)
 * Callback function, you can return true / false, to control whether to
 * continue to read the next trunk
 * Return true, continue to read
 * Return false, stop reading and close the file
 *
 * param String $filename
 * param Mixed $callback
 * param Int $trunk_size
 * return Bool
 */
function swoole_async_read($filename, $callback, $trunk_size = 8192)
{
}


/**
 * Asynchronous write file
 *
 * And swoole_async_writefile different, write segmented write.
 * Do not need a one-time write content into memory, so it only takes a small
 * amount of memory.
 * Swoole_async_write offset by incoming parameters to determine the position
 * of writing
 *
 * Callback ($filename)
 *
 * param String $filename
 * param String $content
 * param Int $offset
 * param Mixed $callback
 *
 * return Bool
 */
function swoole_async_write($filename, $content, $offset, $callback = NULL)
{
}

/**
 * The domain name to an IP address
 * Calling this function will return immediately, when the DNS query is
 * completed, automatic callback specified callback function
 *
 * Callback ($host, $ip)
 *
 * param String $domain
 * param Callback $callback
 */
function swoole_async_dns_lookup($domain, $callback)
{
}

/**
 * Swoole_client
 */
class swoole_client
{

    /**
     * Function performs error set the variable
     *
     *var
     */
    public $errCode;

    /**
     * Socket file descriptor
     *
     * PHP code you can use:
     * $Sock = fopen ("php: // fd /" $swoole_client-> sock.);
     *
     * The swoole_client the socket into a stream socket. You can call fread /
     * fwrite / fclose functions such as process operations.
     * Swoole_server the $fd can not use this method to convert as $fd is
     * just a number, $fd file descriptor is primary process
     * $Swoole_client-> sock can be converted to int as the key array.
     *
     *var Int
     */
    public $sock;
    
    /**
     * Swoole_client Constructor
     *
     * param Int $sock_type specified socket type and supports TCP / UDP, TCP6
     * / UDP64 species
     * param Int $sync_type SWOOLE_SOCK_SYNC / SWOOLE_SOCK_ASYNC synchronous /
     * asynchronous
     */
    public function __construct($sock_type, $sync_type = SWOOLE_SOCK_SYNC)
    {
    }

    /**
     * Connect to the remote server
     *
     * param String $host is the address of the remote server v1.6.10 + support
     * Swoole will automatically fill in the domain DNS query
     * param Int $port is the remote server port
     * param Float $timeout is network IO timeout, the unit is s, support for
     * floating point. The default is 0.1s, ie 100ms
     * param Int $flag parameter when UDP type indicates whether udp_connect
     * enabled. Setting this option will bind $host and $port, the UDP will discard
     * non-designated host / port of the packet.
     * Before send / recv swoole_client_select must use to detect whether the
     * connection is completed
     * return Bool
     */
    public function connect($host, $port, $timeout = 0.1, $flag = 0)
    {
    }

    /**
     * Sending data to a remote server
     *
     * Parameter is a string, support for binary data.
     * Successfully sent returns sent data length
     * Failure to return false, and set $swoole_client-> errCode
     *
     * param String $data
     * return Bool
     */
    public function send($data)
    {
    }

    /**
     * Ask any IP: PORT server to send packets, only support UDP / UDP6 of
     * client
     * param $Ip
     * param $Port
     * param $Data
     */
    function sendto($ip, $port, $data)
    {

    }

    /**
     * Receive data from the server
     *
     * If you set the $waitall you must set the correct $size, otherwise they
     * will have to wait until it receives the data length reaches $size
     * If you set the wrong $size, can cause recv timeout, returns false
     * Call successfully returns the resulting string, failed to return false,
     * and set $swoole_client-> errCode property
     *
     * param Int $size maximum length of the received data
     * param Bool $waitall whether to wait for all the data back after arrival
     * return String
     */
    public function recv($size = 65535, $waitall = false)
    {
    }

    /**
     * Close remote connection
     *
     * Swoole_client object when the destructor will automatically close
     *
     * return Bool
     */
    public function close()
    {
    }

    /**
     * Register asynchronous event callback function
     *
     * param $Event_name
     * param $Callback_function
     * return Bool
     */
    public function on($event_name, $callback_function)
    {
    }

    /**
     * Determines whether to connect to the server
     * return Bool
     */
    public function isConnected()
    {
    }

    /**
     * Get client socket of host: port information
     * return Bool | array
     */
    public function getsockname()
    {
    }

    /**
     * Get the distal socket of host: port information only for UDP / UDP6
     * agreement
     * UDP sends data to the server, it may respond by another Server
     * return Bool | array
     */
    public function getpeername()
    {
    }
}

/**
 * Class swoole_server
 */
class swoole_server
{
    /**
     * Parameters * swoole_server :: set () function set is saved to
     * swoole_server :: $setting properties. Value in the callback function to access
     * the operating parameters
     *
     *var Array
     */
    public $setting = array();

    /**
     * Main process PID
     *
     *var Int
     */
    public $master_pid;

    /**
     * The current PID server management process
     *
     * !! Only after onStart / onWorkerStart get to
     *
     *var Int
     */
    public $manager_pid;

    /**
     * The current number Worker process
     *
     * This property is onWorkerStart when $worker_id is the same.
     *
     * * Worker process ID in the range [0, $serv-> setting ['worker_num'])
     * * Task process ID range is [$serv-> setting ['worker_num'], $serv-
     * > setting ['worker_num'] + $serv-> setting ['task_worker_num'])
     *
     * Work process restart worker_id value is unchanged
     *
     *var Int
     */
    public $worker_id;
    /**
     * Current Worker process ID, 0 - ($serv-> setting [worker_num] -1)
     *var Int
     */
    public $worker_pid;
    /**
     * Are Task worker process
     *
     * True indicates that the current process is the work process Task
     * False indicates that the current process is the Worker Process
     *
     *var Bool
     */
    public $taskworker;
    /**
     * TCP connection iterator, you can use all the current foreach traversal
     * server connections, function and swoole_server-> connnection_list this
     * attribute is the same, but more friendly. Fd traversing element is a single
     * connection
     *
     * Connect iterators rely pcre library is not installed pcre library can
     * not use this function
     *
     * Foreach ($server-> connections as $fd)
     * {
     * $Server-> send ($fd, "hello");
     *}
     *
     * . * Echo "Current Server Total" .count ($server-> connections)
     * "connections \ n";
     *
     *var Array
     */
    public $connections;

    /**
     * Swoole_server Constructor
     * param $Host
     * param $Port
     * param Int $mode
     * param Int $sock_type
     */
    function __construct($host, $port, $mode = SWOOLE_PROCESS, $sock_type = SWOOLE_SOCK_TCP)
    {
    }

    /**
     * Register event callback function, and swoole_server-> on the same.
     * swoole_http_server-> on the difference between:
     *
     * * Swoole_http_server-> on does not accept onConnect / onReceive callback
     * settings
     * * Swoole_http_server-> on additional accepts one kind of new types of
     * events onRequest
     *
     * Event List
     *
     * * OnStart
     * * OnShutdown
     * * OnWorkerStart
     * * OnWorkerStop
     * * OnTimer
     * * OnConnect
     * * OnReceive
     * * OnClose
     * * OnTask
     * * OnFinish
     * * OnPipeMessage
     * * OnWorkerError
     * * OnManagerStart
     * * OnManagerStop
     *
     * $Http_server-> on ('request', function (swoole_http_request $request,
     * swoole_http_response $response) {
     * $Response-> end ("<h1> hello swoole </ h1>");
     *})
     *
     *
     * Upon receipt of a complete Http request, the callback function. There
     * are two callback parameter:
     *
     * * $Request, Http request information object that contains the header /
     * get / post / cookie and other related information
     * * $Response, Http response object supports cookie / header / status,
     * etc. Http operations
     *
     *
     * When * !! $response / $request object passed to other functions, do not
     * add a reference symbol &amp;
     *
     * param String $event
     * param Mixed $callback
     */
    public function on($event, $callback)
    {
    }

    /**
     * Set run-time parameter
     *
     * Swoole_server-> set function is used to set various parameters
     * swoole_server runtime. After the server starts to access an array of set
     * function parameters set by $serv-> setting.
     *
     * param Array $setting
     */
    public function set(array $setting)
    {
    }

    /**
     * Start server, listens to all TCP / UDP port
     *
     * Creates worker_num + 2 processes after the successful launch. The main
     * process + Manager process + worker_num a Worker Process
     *
     * return Bool
     */
    public function start()
    {
    }

    /**
     * Send the data to the client
     *
     * * $Data, data sent. TCP protocol shall not exceed the maximum 2M, UDP
     * protocol can not exceed 64K
     * * Send successful returns true, if the connection has been closed or
     * failed to send returns false
     *
     * TCP server
     *
     * * Send operations are atomic, multiple processes simultaneously send
     * calls to send data to the same connection, the data does not occur mix
     * If more than 2M of data to be transmitted, the data can be written to a
     * temporary file, and then sent via sendfile Interface
     *
     * Swoole-1.6 or later does not need $from_id
     *
     * UDP server
     *
     * * Send operations will send packets directly in the worker process, and
     * then through the primary process will not be forwarded
     * Use fd save client IP, from_id save from_fd and port
     * If onReceive immediately after sending data to the client, you can not
     * pass $from_id
     * If the UDP to send data to other clients, you must be passed from_id
     * * Outside the network service to send more than 64K of data will be
     * divided into a plurality of transmission units transmitted packet loss if one
     * of the units, will cause the entire packet is discarded. So extranet service,
     * we recommend sending packets 1.5K below
     *
     * param Int $fd
     * param String $data
     * param Int $from_id
     * return Bool
     */
    public function send($fd, $data, $from_id = 0)
    {
    }

    /**
     * To any client IP: PORT to send UDP packets
     *
     * * $Ip as IPv4 string, such as 192.168.1.102. If the IP is not a
     * legitimate error is returned
     * * $Port is the network port number 1-65535, if the port fails Send
     * * $Data to send the data content, which can be text or binary content
     * * $Ipv6 whether the IPv6 address, optional parameters, defaults to
     * false
     *
     * Example
     *
     * // The IP address of 220.181.57.216 host 9502 port to send a hello world
     * string.
     * $Server-> sendto ('220.181.57.216', 9502, "hello world");
     * // Send UDP packets to IPv6 server
     * $Server-> sendto ('2600: 3c00 :: f03c: 91ff: fe73: e98f', 9501, "hello
     * world", true);
     *
     * Server must listen to the UDP port before you can use swoole_server-
     * > sendto
     * Server must listen port UDP6 before they can use swoole_server-> sendto
     * to send data to IPv6 address
     *
     * param String $ip
     * param Int $port
     * param String $data
     * param Bool $ipv6
     * return Bool
     */
    public function sendto($ip, $port, $data, $ipv6 = false)
    {
    }

    /**
     * Close the client connection
     *
     * !! Swoole-1.6 or later does not need $from_id swoole-1.5.8 versions of
     * the following, be sure to pass the correct $from_id, as this may cause the
     * connection to leak
     *
     * Operating successfully returns true, otherwise returns false.
     *
     * Server initiative to close the connection, the same trigger onClose
     * event. Do not write clean up logic after close. OnClose callback should be
     * placed to deal with.
     *
     * param Int $fd
     * param Int $from_id
     * return Bool
     */
    public function close($fd, $from_id = 0)
    {
    }

    /**
     * Taskwait role and task the same way, for the delivery of an asynchronous
     * task to task process pool for execution.
     * The difference is that the task is blocked taskwait wait until the task
     * is completed or times out return
     *
     * $Result as a result of the tasks performed by the $serv-> finish
     * function issues. If the task timeout, where returns false.
     *
     * Taskwait is a block interface, if your Server is a full asynchronous
     * please use swoole_server :: task and swoole_server :: finish, do not use
     * taskwait
     * The first three parameters can be formulated to give the task to which
     * the delivery process ID can be passed, the range is 0 - serv-> task_worker_num
     * $Dst_worker_id available 1.6.11+, the default random delivery
     * Taskwait method can not be called in the task Process
     *
     * param Mixed $task_data
     * param Float $timeout
     * param Int $dst_worker_id
     * return String
     */
    public function taskwait($task_data, $timeout = 0.5, $dst_worker_id = -1)
    {
    }

    /**
     * Deliver an asynchronous task to task_worker pool. This function returns
     * immediately. worker process can continue to process a new request
     *
     * * $Data to the task of delivering data, which can be in addition to any
     * PHP resource type variable
     * * $Dst_worker_id could be developed to give the task to which the
     * delivery process ID can be passed, the range is 0 - serv-> task_worker_num
     * The return value is an integer ($task_id), represents this task ID. If
     * you have finish respond, onFinish callback parameters will carry $task_id
     *
     * This function is used to slow the task to execute asynchronously, such
     * as a chat room server, you can use it to send a broadcast. When the task is
     * completed, the task calls $serv- Process> finish ("finish") told the worker
     * process this task has been completed. Of course swoole_server-> finish is
     * optional.
     *
     * * AsyncTask 1.6.4 version adds features not started by default task
     * function, you need to manually set task_worker_num to activate this feature
     * * Number task_worker adjustments swoole_server :: set parameters, such
     * as task_worker_num => 64, indicating that the startup process 64 to receive
     * asynchronous tasks
     *
     *
     * Precautions
     *
     * Use swoole_server_task must set onTask and onFinish callback Server,
     * otherwise swoole_server-> start to fail
     * The number of task operation must be less than onTask processing speed,
     * if the delivery capacity of more than processing power, task will be filled
     * buffer zone, resulting in worker process blocking. worker process will not
     * receive a new request
     *
     * param Mixed $data
     * param Int $dst_worker_id
     * return Bool
     */
    public function task($data, $dst_worker_id = -1)
    {
    }


    /**
     * This function can send messages to any worker process or task process.
     * You can call in the non-primary processes and management processes. Process
     * receives the message will trigger onPipeMessage event
     *
     * * $Message is the message sent by the data content
     * * $Dst_worker_id target process ID, in the range of 0 ~ (worker_num +
     * task_worker_num - 1)
     *
     * !! Use sendMessage event callback function must register onPipeMessage
     *
     * $Serv = new swoole_server ("0.0.0.0", 9501);
     * $Serv-> set (array (
     * 'Worker_num' => 2,
     * 'Task_worker_num' => 2,
     *));
     * $Serv-> on ('pipeMessage', function ($serv, $src_worker_id, $data) {
     * Echo "# {$serv-> worker_id} message from # $src_worker_id: $data \
     * n";
     *});
     * $Serv-> on ('task', function ($serv, $task_id, $from_id, $data) {
     * Var_dump ($task_id, $from_id, $data);
     *});
     * $Serv-> on ('finish', function ($serv, $fd, $from_id) {
     *
     *});
     * $Serv-> on ('receive', function (swoole_server $serv, $fd, $from_id,
     * $data) {
     * If (trim ($data) == 'task')
     * {
     * $Serv-> task ("async task coming");
     *}
     * Else
     * {
     * $Worker_id = 1 - $serv-> worker_id;
     * $Serv-> sendMessage ("hello task process", $worker_id);
     *}
     *});
     *
     * $Serv-> start ();
     *
     * param String $message
     * param Int $dst_worker_id
     * return Bool
     */
    public function sendMessage($message, $dst_worker_id = -1)
    {
        return true;
    }

    /**
     * This function is used in the process of notification task worker
     * process, delivery task is completed. This function can be passed to the worker
     * process result data
     *
     * $Serv-> finish ("response");
     *
     * Use swoole_server :: finish onFinish callback function must be set to
     * Server. OnTask callback function can only be used to process the task
     *
     * Swoole_server :: finish is optional. If the worker process does not care
     * about the results of task execution, no need to call this function
     * Return string onTask callback function is equivalent to calling finish
     *
     * param String $task_data
     */
    public function finish($task_data)
    {
    }

    /**
     * Detection server all connections and identify connections has exceeded
     * the agreed time.
     * If you specify if_close_connection, the connection will automatically
     * turn off the timeout. Fd array is not specified, returns only connected '
     *
     * * $If_close_connection is closed connection timeout, default is true
     * * Successful call returns a continuous array element is closed $fd.
     * * Call failed to return false
     *
     * param Bool $if_close_connection
     * return Array
     */
    public function heartbeat($if_close_connection = true)
    {
    }

    /**
     * Get the information about the connection
     *
     * Connection_info UDP server can be used, but need to pass parameters
     * from_id
     *
     * Array (
     * 'From_id' => 0,
     * 'From_fd' => 12,
     * 'Connect_time' => 1392895129,
     * 'Last_time' => 1392895137,
     * 'From_port' => 9501,
     * 'Remote_port' => 48918,
     * 'Remote_ip' => '127.0.0.1',
     *)
     *
     * * $Udp_client = $serv-> connection_info ($fd, $from_id);
     * * Var_dump ($udp_client);
     * * From_id reactor from which the thread
     * This is not the server socket fd which client connections * * server_fd
     * from
     * * Server_port from which Server port
     * * Port remote_port client connections
     * * Remote_ip ip client connections
     * * Connect_time Server is connected to the time in seconds
     * * Last_time last transmission time data in seconds
     *
     * param Int $fd
     * param Int $from_id
     * return Array | bool
     */
    public function connection_info($fd, $from_id = -1)
    {
    }

    /**
     * Used to traverse the current Server for all client connections,
     * connection_list method is based on shared memory, there is no IOWait, traversed
     * quickly. Also connection_list returns all TCP connections, not just the current
     * worker process TCP connection
     *
     * Example:
     *
     * $Start_fd = 0;
     * While (true)
     * {
     * $Conn_list = $serv-> connection_list ($start_fd, 10);
     * If ($conn_list === false or count ($conn_list) === 0)
     * {
     * Echo "finish \ n";
     * Break;
     *}
     * $Start_fd = end ($conn_list);
     * Var_dump ($conn_list);
     * Foreach ($conn_list as $fd)
     * {
     * $Serv-> send ($fd, "broadcast");
     *}
     *}
     *
     * param Int $start_fd
     * param Int $pagesize
     * return Array | bool
     */
    public function connection_list($start_fd = -1, $pagesize = 100)
    {
    }

    /**
     * Reset all worker processes
     *
     * A back-end server busy all the time to process the request, if an
     * administrator to terminate / restart the server program kill process by the
     * way, could lead to code execution just half ended. It will produce data
     * inconsistencies in this case. As trading system, pay logic of the next segment
     * is shipped, it is assumed after paying logic process is terminated. Cause users
     * to pay money, but not shipped, the consequences are very serious.
     *
     * Swoole provides a flexible termination / restart mechanism, the
     * administrator only needs to send a specific signal, worker process can be
     * safely ended Server SwooleServer.
     *
     * * SIGTERM: send this signal to the main process server will secure
     * termination
     * * In the PHP code you can call $serv-> shutdown () to complete this
     * operation
     * * SIGUSR1: send SIGUSR1 signal to the management process, the smooth
     * restart all worker processes
     * * In the PHP code you can call $serv-> reload () to complete this
     * operation
     * * Swoole of reload a protective mechanism, when a reload is in progress
     * receive a new restart signal will be discarded
     *
     * # Restart all worker processes
     * Kill -USR1 main process PID
     *
     * Only task_worker restart function. SIGUSR2 can simply send to the
     * server.
     *
     * # Only restart the task process
     * Kill -USR2 main process PID
     * Graceful Restart onReceive only onWorkerStart or the like in Worker
     * Process include / require the PHP file is valid, it has to start before the
     * Server include / require the PHP file, you can not be graceful restart reload
     * For Configuration Server that is $serv-> set in the incoming
     * parameters, you must shut down / restart the entire Server () before you can
     * reload
     * Server can monitor a network port, and can receive remote control
     * commands, to reset all worker
     *
     * return Bool
     */
    public function reload()
    {
    }

    /**
     * Turn off the server
     *
     * This function can be used in the worker process. Send SIGTERM to the
     * main process can also be achieved off the server.
     *
     * Kill -15 PID primary process
     * return Bool
     */
    public function shutdown()
    {
    }

    /**
     * Swoole provides swoole_server :: addListener to increase listening.
     * Business code by calling swoole_server :: connection_info to get a connection
     * from which port
     *
     * * SWOOLE_TCP / SWOOLE_SOCK_TCP tcp ipv4 socket
     * * SWOOLE_TCP6 / SWOOLE_SOCK_TCP6 tcp ipv6 socket
     * * SWOOLE_UDP / SWOOLE_SOCK_UDP udp ipv4 socket
     * * SWOOLE_UDP6 / SWOOLE_SOCK_UDP6 udp ipv6 socket
     * * SWOOLE_UNIX_DGRAM unix socket dgram
     * * SWOOLE_UNIX_STREAM unix socket stream
     *
     *
     * You can mix UDP / TCP, while internal network and external monitor
     * ports. Examples:
     *
     * $Serv-> addlistener ("127.0.0.1", 9502, SWOOLE_SOCK_TCP);
     * $Serv-> addlistener ("192.168.1.100", 9503, SWOOLE_SOCK_TCP);
     * $Serv-> addlistener ("0.0.0.0", 9504, SWOOLE_SOCK_UDP);
     * $Serv-> addlistener ("/ var / run / myserv.sock", 0,
     * SWOOLE_UNIX_STREAM);
     *
     * param String $host
     * param Int $port
     * param Int $type
     */
    public function addlistener($host, $port, $type = SWOOLE_SOCK_TCP)
    {
    }

    /**
     * Get the number of currently active TCP Server connection, start time,
     * accpet / total number of times the information close
     *
     * Array (
     * 'Start_time' => 1409831644,
     * 'Connection_num' => 1,
     * 'Accept_count' => 1,
     * 'Close_count' => 0,
     *);
     *
     * * Start_time server startup time
     * Number * * connection_num currently connected
     * * Accept_count received how many connections
     * Number of connections close_count closed
     * * Tasking_num number of jobs currently queued
     *
     * return Array
     */
    function stats()
    {
    }

    /**
     * Perform function after a specified period of time
     *
     * Swoole_server :: after a one-time timer function will be destroyed after
     * it is executed.
     *
     * $After_time_ms specified time, in milliseconds
     * $Function callback_function executed after the expiration of the time,
     * must be able to call. callback function does not accept any arguments
     * $After_time_ms maximum can not more than 86,400,000
     * This method is an alias swoole_timer_after function
     *
     * param $Ms
     * param Int $after_time_ms
     * param Mixed $callback_function
     * param Mixed $param
     */
    public function after($after_time_ms, $callback_function, $param = null)
    {
    }

    /**
    * Increased alias listening port, addlistener of
    * param $Host
    * param $Port
    * param $Type
    * return Bool
    */
    public function listen($host, $port, $type = SWOOLE_SOCK_TCP)
    {
    }

    /**
     *
     * Add a working process a user-defined
     *
     * * $Process is swoole_process objects, attention is not required start.
     * The process is automatically created when swoole_server starts and executes the
     * specified child process function
     * The child process can call each method provided $server objects, such
     * as connection_list / connection_info / stats
     * * In worker process can call the method $process provided for
     * communication with the child
     * * This function is typically used to create a special work processes for
     * monitoring, reporting, or other special tasks.
     *
     * The child process will be hosted Manager process, if a fatal error
     * occurs, manager process will re-create a
     *
     * param Swoole_process $process
     */
    public function addProcess(swoole_process $process)
    {
    }

    /**
     * Set the timer. 1.6.12 version before this function can not be used in
     * the Message Queue mode, message queues IPC mode can be used after the timer
     * 1.6.12
     *
     * The second parameter is the interval timer, in milliseconds. swoole
     * smallest particles is 1 millisecond timer. Support for multiple timers. This
     * function can be used in worker process.
     *
     * * Before swoole1.6.5 support is in seconds, so the argument passed
     * before 1.6.5 1, after that you need to pass in 1000 1.6.5
     * * After swoole1.6.5, addtimer must only be used in onStart /
     * onWorkerStart / onConnect / onReceive / onClose the callback function,
     * otherwise it will throw an error. And the timer is not valid
     * * Note that not exist two identical interval timer
     * Even in the code to add a timer multiple times, there will only be one
     * entry into force
     *
     *
     * Following the increase timer callback function to set onTimer Server, or
     * Server will not start. Multiple timer will callback function. In this function
     * requires its own switch, depending on the value interval to determine from
     * which timer.
     *
     * // Object-oriented style
     * $Serv-> addtimer (1000);// 1s
     * $Serv-> addtimer (20);// 20ms
     *
     * param Int $interval
     * return Bool
     */
    public function addtimer($interval)
    {
    }

    /**
     * Delete timers
     *
     * param $Interval
     */
    public function deltimer($interval)
    {
    }

    /**
     * Increased tick timer
     *
     * You can customize the callback function. This function is an alias
     * swoole_timer_tick
     *
     * After the worker process finishes running, all timers will self-destruct
     *
     * Set a timer interval timer, and after the timer tick difference is that
     * the timer will continue to trigger until you call swoole_timer_clear cleared.
     * And swoole_timer_add difference is that there can be multiple timers timer tick
     * same interval.
     *
     * param Int $ms
     * param Mixed $callback
     * param Mixed $param
     * return Int
     */
    public function tick($interval_ms, $callback, $param = null)
    {
    }

    /**
     * Remove the set timer that will not trigger
     * param $Id
     */
    function clearAfter($id)
    {
    }

    /**
     * Set Server event callback function
     *
     * The first parameter is the resource object swoole
     * The second parameter is the name of the callback is not case sensitive,
     * specific reference to the callback function list contents
     * The third function is a PHP callback function, it can be a string, an
     * array of anonymous functions. Such as
     * Handler / on / set methods can only be called before swoole_server ::
     * start
     *
     *
     * $Serv-> handler ('onStart', 'my_onStart');
     * $Serv-> handler ('onStart', array ($this, 'my_onStart'));
     * $Serv-> handler ('onStart', 'myClass :: onStart');
     *
     * param String $event_name
     * param Mixed $event_callback_function
     * return Bool
     */
    public function handler($event_name, $event_callback_function)
    {
    }

    /**
     * Send files to the TCP client connections
     *
     * Endfile function call sendfile system call provided by the OS, read and
     * write files directly from the operating system socket. sendfile only 2 memory
     * copy, use this function to send a large file can be reduced when the operating
     * system's CPU and memory footprint.
     *
     * File path $filename to be sent, if the file does not exist will return
     * false
     * Operating successfully returns true, otherwise returns false
     * This function swoole_server-> send are sending data to the client,
     * except that sendfile data from the specified file.
     *
     * param Int $fd
     * param String $filename file absolute path
     * return Bool
     */
    public function sendfile($fd, $filename)
    {
    }

    /**
     * Connect to bind a user-defined ID, in can set dispatch_mode = 5 setting
     * has this ID hash value fixed allocation. I can guarantee one UID of all the
     * connections will be assigned to the same process Worker
     *
     * In the default setting dispatch_mode = 2, server will be allocated in
     * accordance with socket fd connection data to a different worker.
     * Because fd is unstable, a client reconnects after disconnection, fd will
     * change. So the client's data will be assigned to another Worker.
     * After using the bind can be assigned user-defined ID. Even reconnection,
     * the same TCP connection uid data will be assigned the same Worker process.
     *
     * * $Fd file descriptor connected
     * * $Uid Specifies the UID
     *
     * Same connection can only be bind again, if you have bound uid, called
     * again bind returns false
     * You can use the $serv-> value connection_info ($fd) See the connection
     * is bound uid
     *
     * param Int $fd
     * param Int $uid
     * return Bool
     */
    public function bind($fd, $uid)
    {
    }
}


/**
 * Class swoole_lock
 */
class swoole_lock
{

    /**
     * param Int $type is the type of lock
     * param String $lockfile when type SWOOLE_FILELOCK when you must pass,
     * specify the path to the file lock
     * Note that each type of lock support methods are not the same. Such as
     * read-write locks, file locks can support $lock-> lock_read ().
     * Also in addition to the file lock, other types of locks must be created
     * within the parent process before they can compete with each other between this
     * fork lock out the child.
     */
    public function __construct($type, $lockfile = NULL)
    {
    }


    /**
     * Locking operation
     *
     * If there are other processes that hold locks, then there will enter the
     * block until the process holding the lock unlock.
     */
    public function lock()
    {
    }


    /**
     * Locking operation
     *
     * And lock method is different, trylock () does not block, it will return
     * immediately.
     * Indicates grab the lock failure when it returns false, there are other
     * processes that hold locks. Returns true if the lock is successful, then you can
     * modify shared variables.
     *
     * SWOOlE_SEM semaphore no trylock method
     */
    public function trylock()
    {
    }


    /**
     * Release lock
     */
    public function unlock()
    {
    }


    /**
     * Blocking lock
     *
     * Lock_read method is only available in read-write lock (SWOOLE_RWLOCK)
     * and file locks (SWOOLE_FILELOCK), indicates read-only locking.
     * In the process of holding a read lock, other processes can still get a
     * read lock, you can continue to occur read. But not $lock-> lock () or $lock-
     * > trylock (), these two methods is to obtain an exclusive lock.
     *
     * When another process acquired an exclusive lock (call $lock-> lock / $
     * lock-> trylock) when, $lock-> lock_read () will block occur until the process
     * holding the lock release.
     */
    public function lock_read()
    {
    }


    /**
     * Non-blocking lock
     *
     * This method lock_read same, but non-blocking. Call will return
     * immediately, must be tested to determine whether the return value to get a
     * lock.
     */
    public function trylock_read()
    {
    }
}


/**
 * IO event loop
 *
 *
 * Parallel processing swoole_client of using a select to do IO event loop. Why
 * select it?
 * Because the client generally do not have too many connections, and most
 * socket will soon receive the response data.
 * In the case of a few select connected epoll performance is better than the
 * other select easier.
 *
 * $Read, $write, $error are read / write / error file descriptor.
 * These three parameters must be referenced array variable. Elements of the
 * array must be swoole_client object.
 * $Timeout argument is select the timeout, in seconds, to accept the float.
 *
 * After the call is successful, it will return the number of events, and
 * modify the $read / $write / $error array.
 * Use foreach loop through the array, and then execute $item-> recv / $item-
 * > send to send and receive data.
 * Or by calling $item-> close () or unset ($item) to close the socket.
 *
 *
 * param Array $read readable
 * param Array $write writable
 * param Array $error error
 * param Float $timeout
 */
function swoole_client_select(array &$read, array &$write, array &$error, $timeout)
{
}

/**
 * Swoole Process Management
 * Can facilitate communication between the built-IPC communication support,
 * the child process and the main process
 * Supports standard input and output redirection, within the child echo, will
 * be sent to the pipeline, instead of the output screen
 * Class swoole_process
 */
class swoole_process
{
    /**
     * PID process
     *
     *var Int
     */
    public $pid;
    /**
     * Pipeline PIPE
     *
     *var Int
     */
    public $pipe;

    /**
     * Callbackparam mixed $callback child processes
     * param Bool $redirect_stdin_stdout whether to redirect the standard input
     * and output
     * param Bool $create_pipe whether to create a pipeline
     */
    function __construct($callback, $redirect_stdin_stdout = false, $create_pipe = true)
    {
    }

    /**
     * Write data to the pipe
     *
     * param String $data
     * return Int
     */
    function write($data)
    {
    }

    /**
     * Read data from the pipe
     *
     * param Int $buffer_len maximum read length of
     * return String
     */
    function read($buffer_len = 8192)
    {
    }

    /**
     * Exit sub-process, the actual function called exit, IDE will exit
     * identified as key words, there will be a syntax error, so here called _exit
     *
     * param Int $code
     * return Int
     */
    function _exit($code = 0)
    {
    }

    /**
     * Implementation of additional program
     * param String $execute_file executable path
     * param Array $params parameter array
     * return Bool
     */
    function exec($execute_file, $params)
    {
    }

    /**
     * Blocked waiting for the child process exits, and recovering
     * Successful return PID and exit status of an array containing child
     * process
     * If the array ('code' => 0, 'pid' => 15001), failed to return false
     *
     * return False | array
     */
    static function wait()
    {
    }

    /**
     * Daemonize
     * param Bool $nochdir
     * param Bool $noclose
     */
    static function daemon($nochdir = false, $noclose = false)
    {

    }

    /**
     * Create a message queue
     * param Int $msgkey Message Queuing KEY
     * param Int $mode mode
     */
    function useQueue($msgkey = -1, $mode = 2)
    {

    }

    /**
     * Push data to the message queue
     * param $Data
     */
    function push($data)
    {

    }

    /**
     * Extract data from the message queue
     * param Int $maxsize
     * return String
     */
    function pop($maxsize = 8192)
    {

    }

    /**
     * Send a signal to a process
     *
     * param $Pid
     * param Int $sig
     */
    static function kill($pid, $sig = SIGTERM)
    {
    }

    /**
     * Register signal processing function
     * Require swoole 1.7.9+
     * param Int $signo
     * param Mixed $callback
     */
    static function signal($signo, $callback)
    {
    }

    /**
     * Promoter process
     *
     * return Int
     */
    function start()
    {
    }

    /**
     * Rename the work process
     * param $Process_name
     */
    function name($process_name)
    {

    }
}


/**
 * Class swoole_buffer
 *
 * Memory Operations
 */
class swoole_buffer
{

    /**
     * param Int $size
     */
    function __construct($size = 128)
    {
    }

    /**
     * To append data to the end of a string buffer zone
     *
     * param String $data
     * return Int
     */
    function append($data)
    {
    }

    /**
     * Remove the contents from the buffer
     *
     * Substr copied a memory
     * After remove the memory and not released, but the bottom was a pointer
     * offset. When the destruction of this object will really free up memory
     *
     * param Int $offset indicates the offset, if it is negative, indicating
     * that the countdown to calculate the offset
     * param Int $length indicates the length of the read data, the default is
     * the end from $offset to the entire buffer zone
     * param Bool $remove representation of this data is removed from the head
     * of the buffer. Only $offset = 0 When this parameter is valid
     */
    function substr($offset, $length = -1, $remove = false)
    {
    }

    /**
     * Cleanup cache data
     * After you do this, the buffer will be reset. swoole_buffer object can be
     * used to handle new requests.
     * Swoole_buffer pointer-based operations to achieve clear, and will not
     * write memory
     */
    function clear()
    {
    }

    /**
     * To buffer zone expansion
     *
     * param Int $new_size specify new buffer size to be larger than the
     * current size
     */
    function expand($new_size)
    {
    }


    /**
     * Write to arbitrary memory location of the cache area
     * This function can write directly to memory. So be sure to use caution,
     * as this may damage the existing data
     *
     * $Data can not exceed the maximum size of the cache.
     * Write method does not automatically expand
     *
     * param Int $offset offset
     * param String $data written data
     */
    function write($offset, $data)
    {
    }

}


define ('HTTP_GLOBAL_ALL', 1);
define ('HTTP_GLOBAL_GET', 2);
define ('HTTP_GLOBAL_POST', 4);
define ('HTTP_GLOBAL_COOKIE', 8);

/**
 * Built-in Web server
 * Class swoole_http_server
 */
class swoole_http_server extends swoole_server
{
    /**
     * Enables data merging, HTTP request data to PHP's GET / POST / COOKIE
     * global array
     * param $Flag
     * param Int $request_flag
     */
    function setGlobal($flag, $request_flag = 0)
    {
    }
}

define ('WEBSOCKET_OPCODE_TEXT', 1);

class swoole_websocket_server extends swoole_http_server
{
    /**
     * WebSocket client connection to a push data
     * param $Fd
     * param $Data
     * param Bool $binary_data
     * param Bool $finish
     */
    function push($fd, $data, $binary_data = false, $finish = true)
    {
    }
}

/**
 * Http request object
 * Class swoole_http_request
 */
class swoole_http_request
{
    public $get;
    public $post;
    public $header;
    public $server;
    public $cookie;
    public $files;

    public $fd;

    /**
     * Get non urlencode-form original form POST data
     * @return string|null
     */
    function rawContent()
    {
    }
}

/**
 * Http response object
 * Class swoole_http_response
 */
class swoole_http_response
{
    /**
     * End Http response, send HTML content
     * param String $html
     */
    public function end($html = '')
    {
    }

    /**
     * Enable Http-Chunk segment sends data to the browser
     * param $Html
     */
    public function write($html)
    {
    }

    /**
     * Set Http header information
     * param $Key
     * param $Value
     */
    public function header($key, $value)
    {
    }

    /**
     * Set Cookie
     *
     * param String $key
     * param String $value
     * param Int $expire
     * param String $path
     * param String $domain
     * param Bool $secure
     * param Bool $httponly
     */
    public function cookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false)
    {
    }

    /**
     * Set HttpCode, such as 404, 501, 200
     * param $Code
     */
    public function status($code)
    {

    }

    /**
     * Set Http compression formats
     * param Int $level
     */
    function gzip($level = 1)
    {

    }
}

/**
 * Create a memory table
 */
class swoole_table
{
    const TYPE_INT = 1;
    const TYPE_STRING = 2;
    const TYPE_FLOAT = 3;

    /**
     * Get key
     * param $Key
     * return Array
     */
    function get($key)
    {
    }

    /**
     * Set key
     * param $Key
     * param Array $array
     */
    function set($key, array $array)
    {
    }

    /**
     * Delete key
     * param $Key
     * return Bool
     */
    function del($key)
    {
    }

    /**
     * Atomic increment operation, can be used for shaping or float column
     * param $Key
     * param $Column
     * param $Incrby
     * return Bool
     */
    function incr($key, $column, $incrby = 1)
    {
    }

    /**
     * Atomic decrement operation, can be used for shaping or float column
     * param $Key
     * param $Column
     * param $Decrby
     */
    function decr($key, $column, $decrby = 1)
    {
    }

    /**
     * Increased Field Definitions
     * param $Name
     * param $Type
     * param Int $len
     */
    function column($name, $type, $len = 4)
    {
    }

    /**
     * Create a table, there will apply the operating system memory
     * return Bool
     */
    function create()
    {
    }

    /**
     * Lock the entire table
     * return Bool
     */
    function lock()
    {
    }

    /**
     * Unlock the tables
     * return Bool
     */
    function unlock()
    {
    }
}

define ('SWOOLE_VERSION', '1.7.7');// current version Swoole

/**
 * New swoole_server constructor parameters
 */
define ('SWOOLE_BASE', 1);// use the Base model, business code directly executed in Reactor
define ('SWOOLE_THREAD', 2);// using threads pattern, business code execution Worker thread
define ('SWOOLE_PROCESS', 3);// use process models, business code execution in Worker Process
define ('SWOOLE_PACKET', 0x10);

/**
 * New swoole_client constructor parameters
 */
define ('SWOOLE_SOCK_TCP', 1);// Create tcp socket
define ('SWOOLE_SOCK_TCP6', 3);// create tcp ipv6 socket
define ('SWOOLE_SOCK_UDP', 2);// Create udp socket
define ('SWOOLE_SOCK_UDP6', 4);// Create udp ipv6 socket
define ('SWOOLE_SOCK_UNIX_DGRAM', 5);// Create udp socket
define ('SWOOLE_SOCK_UNIX_STREAM', 6);// Create udp ipv6 socket

define ('SWOOLE_SSL', 5);

define ('SWOOLE_TCP', 1);// Create tcp socket
define ('SWOOLE_TCP6', 2);// create tcp ipv6 socket
define ('SWOOLE_UDP', 3);// Create udp socket
define ('SWOOLE_UDP6', 4);// Create udp ipv6 socket
define ('SWOOLE_UNIX_DGRAM', 5);
define ('SWOOLE_UNIX_STREAM', 6);

define ('SWOOLE_SOCK_SYNC', 0);// synchronization client
define ('SWOOLE_SOCK_ASYNC', 1);// asynchronous client

define ('SWOOLE_SYNC', 0);// synchronization client
define ('SWOOLE_ASYNC', 1);// asynchronous client

/**
 * New swoole_lock constructor parameters
 */
define ('SWOOLE_FILELOCK', 2);// create a file lock
define ('SWOOLE_MUTEX', 3);// create a mutex
define ('SWOOLE_RWLOCK', 1);// create a read-write lock
define ('SWOOLE_SPINLOCK', 5);// create spin locks
define ('SWOOLE_SEM', 4);// Create Semaphore

define ('SWOOLE_EVENT_WRITE', 1);
define ('SWOOLE_EVENT_READ', 2);

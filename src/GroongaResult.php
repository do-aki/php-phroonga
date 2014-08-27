<?php
namespace dooaki\Phroonga;

use dooaki\Phroonga\Exception\InvalidResponse;

abstract class GroongaResult
{
    const GRN_SUCCESS = 0;
    const GRN_END_OF_DATA = 1;
    const GRN_UNKNOWN_ERROR = -1;
    const GRN_OPERATION_NOT_PERMITTED = -2;
    const GRN_NO_SUCH_FILE_OR_DIRECTORY = -3;
    const GRN_NO_SUCH_PROCESS = -4;
    const GRN_INTERRUPTED_FUNCTION_CALL = -5;
    const GRN_INPUT_OUTPUT_ERROR = -6;
    const GRN_NO_SUCH_DEVICE_OR_ADDRESS = -7;
    const GRN_ARG_LIST_TOO_LONG = -8;
    const GRN_EXEC_FORMAT_ERROR = -9;
    const GRN_BAD_FILE_DESCRIPTOR = -10;
    const GRN_NO_CHILD_PROCESSES = -11;
    const GRN_RESOURCE_TEMPORARILY_UNAVAILABLE = -12;
    const GRN_NOT_ENOUGH_SPACE = -13;
    const GRN_PERMISSION_DENIED = -14;
    const GRN_BAD_ADDRESS = -15;
    const GRN_RESOURCE_BUSY = -16;
    const GRN_FILE_EXISTS = -17;
    const GRN_IMPROPER_LINK = -18;
    const GRN_NO_SUCH_DEVICE = -19;
    const GRN_NOT_A_DIRECTORY = -20;
    const GRN_IS_A_DIRECTORY = -21;
    const GRN_INVALID_ARGUMENT = -22;
    const GRN_TOO_MANY_OPEN_FILES_IN_SYSTEM = -23;
    const GRN_TOO_MANY_OPEN_FILES = -24;
    const GRN_INAPPROPRIATE_I_O_CONTROL_OPERATION = -25;
    const GRN_FILE_TOO_LARGE = -26;
    const GRN_NO_SPACE_LEFT_ON_DEVICE = -27;
    const GRN_INVALID_SEEK = -28;
    const GRN_READ_ONLY_FILE_SYSTEM = -29;
    const GRN_TOO_MANY_LINKS = -30;
    const GRN_BROKEN_PIPE = -31;
    const GRN_DOMAIN_ERROR = -32;
    const GRN_RESULT_TOO_LARGE = -33;
    const GRN_RESOURCE_DEADLOCK_AVOIDED = -34;
    const GRN_NO_MEMORY_AVAILABLE = -35;
    const GRN_FILENAME_TOO_LONG = -36;
    const GRN_NO_LOCKS_AVAILABLE = -37;
    const GRN_FUNCTION_NOT_IMPLEMENTED = -38;
    const GRN_DIRECTORY_NOT_EMPTY = -39;
    const GRN_ILLEGAL_BYTE_SEQUENCE = -40;
    const GRN_SOCKET_NOT_INITIALIZED = -41;
    const GRN_OPERATION_WOULD_BLOCK = -42;
    const GRN_ADDRESS_IS_NOT_AVAILABLE = -43;
    const GRN_NETWORK_IS_DOWN = -44;
    const GRN_NO_BUFFER = -45;
    const GRN_SOCKET_IS_ALREADY_CONNECTED = -46;
    const GRN_SOCKET_IS_NOT_CONNECTED = -47;
    const GRN_SOCKET_IS_ALREADY_SHUTDOWNED = -48;
    const GRN_OPERATION_TIMEOUT = -49;
    const GRN_CONNECTION_REFUSED = -50;
    const GRN_RANGE_ERROR = -51;
    const GRN_TOKENIZER_ERROR = -52;
    const GRN_FILE_CORRUPT = -53;
    const GRN_INVALID_FORMAT = -54;
    const GRN_OBJECT_CORRUPT = -55;
    const GRN_TOO_MANY_SYMBOLIC_LINKS = -56;
    const GRN_NOT_SOCKET = -57;
    const GRN_OPERATION_NOT_SUPPORTED = -58;
    const GRN_ADDRESS_IS_IN_USE = -59;
    const GRN_ZLIB_ERROR = -60;
    const GRN_LZO_ERROR = -61;
    const GRN_STACK_OVER_FLOW = -62;
    const GRN_SYNTAX_ERROR = -63;
    const GRN_RETRY_MAX = -64;
    const GRN_INCOMPATIBLE_FILE_FORMAT = -65;
    const GRN_UPDATE_NOT_ALLOWED = -66;
    const GRN_TOO_SMALL_OFFSET = -67;
    const GRN_TOO_LARGE_OFFSET = -68;
    const GRN_TOO_SMALL_LIMIT = -69;
    const GRN_CAS_ERROR = -70;
    const GRN_UNSUPPORTED_COMMAND_VERSION = -71;

    /**
     * @var int リターンコード
     */
    private $return_code;

    /**
     * @var float コマンド開始時刻の UNIX timestamp
     */
    private $command_started_timestamp = null;

    /**
     * @var string エラーメッセージ
     */
    private $error_message = null;

    /**
     * @var array エラー発生箇所
     */
    private $error_location = null;

    /**
     * コマンドが成功したかどうか
     *
     * @return boolean 成功時 true, 失敗時 false
     */
    public function isSuccess()
    {
        return $this->return_code === self::GRN_SUCCESS;
    }

    /**
     * リターンコード の取得
     *
     * @return int リターンコード
     */
    public function getReturnCode()
    {
        return $this->return_code;
    }

    /**
     * リターンコード の設定
     *
     * @param int $return_code リターンコード
     */
    public function setReturnCode($return_code)
    {
        $this->return_code = intval($return_code);
    }

    /**
     * Groonga Server でコマンドが開始された時刻の UNIX timestamp を取得
     *
     * @return float コマンド開始時刻の UNIX timestamp
     */
    public function getCommandStartedTimestamp()
    {
        return $this->command_started_timestamp;
    }

    /**
     * コマンド開始時刻の設定
     *
     * @param float $command_started_timestamp コマンド開始時刻の UNIX timestamp
     */
    public function setCommandStartedTimestamp($command_started_timestamp)
    {
        $this->command_started_timestamp = floatval($command_started_timestamp);
    }

    /**
     * コマンド実行時間 の取得
     *
     * @return float コマンド実行時間
     */
    public function getElapsedSec()
    {
        return $this->elapsed_sec;
    }

    /**
     * コマンド実行時間 の設定
     *
     * @param float $elapsed_sec コマンド実行時間
     */
    public function setElapsedSec($elapsed_sec)
    {
        $this->elapsed_sec = floatval($elapsed_sec);
    }

    /**
     * エラーメッセージ の取得
     *
     * @return string エラーメッセージ
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * エラーメッセージ の設定
     *
     * @param string $error_message エラーメッセージ
     */
    public function setErrorMessage($error_message)
    {
        $this->error_message = $error_message;
    }

    /**
     * エラー発生箇所 の取得
     *
     * @return array error_location
     */
    public function getErrorLocation()
    {
        return $this->error_location;
    }

    /**
     * エラー発生箇所 の設定
     *
     * @param array $error_location error_location
     */
    public function setErrorLocation(array $error_location)
    {
        $this->error_location = $error_location;
    }

    /**
     *
     * @var array Body
     */
    private $body;

    /**
     * Body の取得
     *
     * @return array Body
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Body の設定
     *
     * @param array $body
     *            body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Build GroongaResult object from JSON
     *
     * @param string $json
     * @throws \dooaki\Phroonga\InvalidResponse
     * @return \dooaki\Phroonga\GroongaResult
     */
    public static function fromJson($json)
    {
        $ary = json_decode($json, true);
        if ($ary === null) {
            throw new InvalidResponse("cannot parse json");
        }

        return static::fromArray($ary);
    }

    /**
     * Build GroongaResult object from Array
     *
     * @param array $result
     * @return \dooaki\Phroonga\GroongaResult
     */
    public static function fromArray(array $result)
    {
        list ($header, $body) = $result;

        $r = new static();
        $r->setReturnCode($header[0]);
        $r->setCommandStartedTimestamp($header[1]);
        $r->setElapsedSec($header[2]);
        if (isset($header[3])) {
            $r->setErrorMessage($header[3]);
        }
        if (isset($header[4])) {
            $r->setErrorLocation($header[4]);
        }

        $r->setBody($body);
        return $r;
    }
}

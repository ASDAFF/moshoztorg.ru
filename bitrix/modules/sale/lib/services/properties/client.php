<?

namespace Bitrix\Sale\Services\Properties;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Context;

class Client
{
	const SERVICE_HOST = 'http://ps.perevozov.bx';
	//const SERVICE_HOST = 'http://properties.bitrix.info';
	const REST_URI = '/rest/';
	const REGISTER_URI = '/ps/register.php';
	const SERVICE_ACCESS_OPTION = 'properties_service_access';
	const METHOD_COMMON_GET_BY_INN = 'ps.common.getByInn';
	const METHOD_COMMON_GET_BY_OGRN = 'ps.common.getByOgrn';

	protected $httpTimeout = 5;
	protected $accessSettings = null;

	/** @var ErrorCollection */
	protected $errorCollection;

	/**
	 * Constructor of the client of the properties service.
	 */
	public function __construct()
	{
		$this->errorCollection = new ErrorCollection();
	}

	/**
	 * Returns properties of the organization or individual businessman by its OGRN code.
	 * @param string $ogrn OGRN code of the organization or individual businessman.
	 * @return array|false
	 */
	public function getByOgrn($ogrn)
	{
		return $this->call(static::METHOD_COMMON_GET_BY_OGRN, array('ogrn' => $ogrn));
	}

	/**
	 * Returns properties of the organization or individual businessman by its INN code.
	 * @param string $inn INN code of the organization or individual businessman.
	 * @return array|false
	 */
	public function getByInn($inn)
	{
		return $this->call(static::METHOD_COMMON_GET_BY_INN, array('inn' => $inn));
	}

	/**
	 * Performs call to the REST method and returns decoded results of the call.
	 * @param string $methodName Name of the REST method.
	 * @param array $additionalParams Parameters, that should be passed to the method.
	 * @return array|false
	 */
	protected function call($methodName, $additionalParams = null)
	{
		global $APPLICATION;

		if(is_null($this->accessSettings))
			$this->accessSettings = $this->getAccessSettings();

		if($this->accessSettings === false)
			return false;

		if(!is_array($additionalParams))
		{
			$additionalParams = array();
		}
		else
		{
			$additionalParams = $APPLICATION->ConvertCharsetArray($additionalParams, LANG_CHARSET, "utf-8");
		}

		$additionalParams['client_id'] = $this->accessSettings['client_id'];
		$additionalParams['client_secret'] = $this->accessSettings['client_secret'];

		$http = new HttpClient(array('socketTimeout' => $this->httpTimeout));
		$result = $http->post(
				static::SERVICE_HOST.static::REST_URI.$methodName,
				$additionalParams
		);

		$answer = $this->prepareAnswer($result);

		if(!is_array($answer) || count($answer) == 0)
		{
			$this->errorCollection->add(array(new Error('Malformed answer from service: '.$http->getStatus().' '.$result)));
			return false;
		}

		if(array_key_exists('error', $answer))
		{
			$this->errorCollection->add(array(new Error($answer['error_description'], $answer['error'])));
			return false;
		}

		return $answer['result'];
	}

	/**
	 * Decodes answer of the method.
	 * @param string $result Json-encoded answer.
	 * @return array|bool|mixed|string Decoded answer.
	 */
	protected function prepareAnswer($result)
	{
		return Json::decode($result);
	}

	/**
	 * Registers client on the properties service.
	 * @return array|false Access credentials if registration was successful or false otherwise.
	 */
	protected function register()
	{
		$httpClient = new HttpClient();

		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client.php");

		$queryParams = array(
				"action" => "register",
				"key" => md5(\CUpdateClient::GetLicenseKey()),
				"redirect_uri" => static::getRedirectUri(),
		);

		$result = $httpClient->post(static::SERVICE_HOST.static::REGISTER_URI, $queryParams);
		$result = Json::decode($result);

		if($result["error"])
		{
			$this->errorCollection->add(array(new Error($result["error"])));
			return false;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * Stores access credentials.
	 * @param array $params Access credentials.
	 */
	protected static function setAccessSettings(array $params)
	{
		Option::set('sale', static::SERVICE_ACCESS_OPTION, serialize($params));
	}

	/**
	 * Reads and returns access credentials.
	 * @return array|false Access credentials or false in case of errors.
	 */
	protected function getAccessSettings()
	{
		$accessSettings = Option::get('sale', static::SERVICE_ACCESS_OPTION);

		if($accessSettings != '')
		{
			return unserialize($accessSettings);
		}
		else
		{
			if($accessSettings = $this->register())
			{
				$this->setAccessSettings($accessSettings);
				return $accessSettings;
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * Drops current stored access credentials.
	 */
	public function clearAccessSettings()
	{
		Option::set('sale', static::SERVICE_ACCESS_OPTION, null);
	}

	/**
	 * @return string
	 */
	protected static function getRedirectUri()
	{
		$request = Context::getCurrent()->getRequest();

		$host = $request->getHttpHost();
		$isHttps = $request->isHttps();

		return ($isHttps ? 'https' : 'http').'://'.$host."/";
	}

	/**
	 * Returns array of errors.
	 * @return array Errors.
	 */
	public function getErrors()
	{
		return $this->errorCollection->toArray();
	}
}
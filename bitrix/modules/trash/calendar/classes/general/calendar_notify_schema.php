<?
IncludeModuleLangFile(__FILE__);

class CCalendarNotifySchema
{
	public function __construct()
	{
	}

	public static function OnGetNotifySchema()
	{
		return array(
			"calendar" => array(
//				"invite" => Array(
//					"NAME" => GetMessage('EC_NS_INVITE'),
//				),
				"reminder" => Array(
					"NAME" => GetMessage('EC_NS_REMINDER'),
				),
				"change" => Array(
					"NAME" => GetMessage('EC_NS_CHANGE'),
				),
				"info" => Array(
					"NAME" => GetMessage('EC_NS_INFO'),
				),
			),
		);
	}
}

class CCalendarPullSchema
{
	public static function OnGetDependentModule()
	{
		return Array(
			'MODULE_ID' => "calendar",
			'USE' => Array("PUBLIC_SECTION")
		);
	}
}

?>

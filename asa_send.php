<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if (!isset($_GET["bx_user"])) die("NULL");
if (!isset($_GET["message"])) die("NULL");
if (!isset($_GET["mailmessage"])) die("NULL");

$UserIDString = $_GET["bx_user"];
$Message = $_GET["message"];
$MailMessage = $_GET["mailmessage"];
$users = explode(";", $UserIDString);

//TODO: добавить проверку дилны строки

if (CModule::IncludeModule("im"))
{
	foreach ($users as $user_id)
	{
	$arMessageFields = array(
		// получатель
		"TO_USER_ID" => $user_id,
		// отправитель
		"FROM_USER_ID" => 118292, 
		// тип уведомления
		"NOTIFY_TYPE" => IM_NOTIFY_FROM, 
		// модуль запросивший отправку уведомления
		"NOTIFY_MODULE" => "ASA", 
		// символьный тэг для группировки и массового удаления, если это не требуется - не задаем параметр
		"NOTIFY_TAG" => "ASA|TEST", 
		// текст уведомления на сайте
		"NOTIFY_MESSAGE" => $Message, 
		// текст уведомления для отправки на почту (или XMPP), если различий нет - не задаем параметр
		"NOTIFY_MESSAGE_OUT" => $MailMessage
		);
		CIMNotify::Add($arMessageFields);
	}
}
?>
<?
//if (!isset($_GET['print']))
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
//else
//    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("Индивидуальный учебный план");
$APPLICATION->SetAdditionalCSS('/extranet/services/curriculum/curriculum.css',true); 
/**
 *  Controller
 */
    include_once($_SERVER["DOCUMENT_ROOT"]."/extranet/services/classes/common.php");
    include_once($_SERVER["DOCUMENT_ROOT"]."/extranet/services/classes/api.kompas-asa.php");

function getCurrentStudentContractNumber()
{
	global $USER;
	$arParams["SELECT"] = array("UF_CONTRACT_NUMBER","LAST_NAME","NAME");
	$rs = CUser::getlist(($by="ID"),($order="desc"),array("ID"=>$USER->GetID()),$arParams);
	$arUser = $rs->Fetch();
	return $arUser['UF_CONTRACT_NUMBER'];
}


    $CN = getCurrentStudentContractNumber();
    if ($CN==null) {
        $CN = (isset($_GET['agreement_number'])) ? $_GET['agreement_number'] : null;
    }
    //$CN="012013121620244981";
?>
<div style="font-size: 10pt;">
<?
    if ($CN==null) {
        require_once("view.user_is_not_student.php");
    } else {
        //$CN="012012082114521473";
        //echo $CN;
		try
		{
			$person     = kompasFactory::get_student($CN);
			$student    = $person->student();
        if (!$student->has_sended_request_individual_plan()){
            # Форма выбора дисциплин для индивидуального учебного плана
            if (isset($_POST['SUBJECT'])){
                foreach($_POST['SUBJECT'] as $subject){
                    $student->get_individual_subjects()->add_subject($subject);
                }
                $student->get_individual_subjects()->apply();
                echo "Пожалуйста, подождите, идет отправка запроса...<SCRIPT>window.location.reload();</SCRIPT>";
                
            } else {
                $APPLICATION->SetTitle("Индивидуальный учебный план: выбор дисциплин");
                include_once ("view.individual_subject.php");
            }
        } elseif (isset($_REQUEST['statement'])) {
            # Печатаем форму вывода заявления студента на выбор дисциплин
            include_once ("view.no_confirm.php");
        } else {
            if ($student->has_individual_plan()){
                # Индивидуальный план согласован, выводим форму учебного плана
				if ($student->get_curent_program()->get_education_level() == "магистратура")
				{
					include_once ("view.individual_plan.magistr.php");
				}
				else
				{
					include_once ("view.individual_plan.php");
				}
            }else{
                # Отображание уведомление, что запрос отправлен, ждем подтверждения, выводим план с учетом выбранных дисциплин
                $APPLICATION->SetTitle("Индивидуальный учебный план: ожидание подтверждения");
                $student->apply_individual_subjects();
				if ($student->get_curent_program()->get_education_level() == "магистратура")
				{
					include_once ("view.individual_plan.magistr.php");
				}
				else
				{
					include_once ("view.individual_plan.php");
				}
            }
        }
        }
		catch (kompasException $excp)
		{
			echo "Во время выполнения запроса произошла ошибка.";
			echo $excp->getMessage();
		}
        //echo "<pre>".var_dump($person)."</pre>";     
    }
?>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
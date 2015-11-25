<?
if (!isset($_GET['print'])){?>
    <a class="webform-small-button" href="<?=$APPLICATION->GetCurPageParam("print=1");?>" target="_blank"><span class="webform-small-button-left"></span><span class="webform-small-button-icon"></span><span class="webform-small-button-text">Печать заявления</span><span class="webform-small-button-right"></span></a>
    <p>Запрос на выбор дисциплин уже отправлен.</p>
    <p>Ожидайте утверждения</p>
<?} else {
	if ($student->get_curent_program()->get_education_level() == "магистратура")
	{
		include('view.individual_subject_statement.magistr.php');
	}
	else
	{
		include('view.individual_subject_statement.php');
	}
    
}
?>
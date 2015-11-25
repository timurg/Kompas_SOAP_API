<?
function out_semester_work($JoinRow, $col_count_cur, $code, $subject_name, $hours, $semester_number, $type_testing) {
	if ($semester_number == 0)
	{
		$semester_number = 1;
	}
    if ($JoinRow) {
        if ($col_count_cur > 1) {
            echo "<tr>";
        }
        echo '<td class="td-semester">' . $semester_number . "</td><td>" .
        $type_testing . "</td>";
    } else {
        echo "<tr>";
        echo "<td>" . $code . "</td><td>" .
        $subject_name . '</td><td class="td-hours">' .
        $hours . '</td><td class="td-semester">' .
        $semester_number . "</td><td>" .
        $type_testing . "</td>";
    }
    echo "</tr>";
}

function PrintSemester(&$cyc, $sem) {
    global $PrintedSubject;
    foreach ($cyc as $cycle) {
        foreach ($cycle as $subject_group) {
            echo is_array($subject_group);
            foreach ($subject_group as $subject) {
                if ((!in_array($subject->get_name(), $PrintedSubject)) && 
                        ($subject->work_count_in_semester($sem) > 0)) {
                    $PrintedSubject[] = $subject->get_name();
                    $sub_code = $subject->get_code();
                    if ($sub_code == "empty") {
                        $sub_code = "";
                    };
                    $coll_count = $subject->attestation_count();
                    $hours = $subject->get_subject_hours();
					$balls = round($hours / 36, 1);
                    $JoinRow = ($coll_count > 1);
                    if ($JoinRow) {
                        echo "<tr><td rowspan=" . $coll_count . ">" . $sub_code . 
                                "</td><td rowspan=" . $coll_count . ">" . 
                                $subject->get_name() . "</td><td  rowspan=" . 
                                $coll_count . ' class="td-hours">' . $balls .
                                "</td>";
                    }
                    $col_count_cur = 0;
                    
                    foreach ($subject as $sw) {
                        $types_testing = $sw->get_types_testing();
                        foreach ($types_testing as $tt) {
                            $col_count_cur ++;
                            //if ($subject->get_name() == 'Введение в специальность') var_dump($sw);
                            out_semester_work($JoinRow, $col_count_cur, $sub_code, $subject->get_name(), $balls, $sw->get_number(), $tt);
                        }
                    }
                }
            }
        }
    }
}

global $SETTINGS;
$PrintedSubject=array();
$cycles = $student->get_curent_program()->get_curriculum()->get_cycles();
$SD_code= intval($student->get_curent_program()->get_supervising_department_code());
$SD = $SETTINGS['SUPERVISING_DEPARTMENTS'][$SD_code];

    if (!isset($_GET['print'])){?>
        <a class="webform-small-button" href="<?=$APPLICATION->GetCurPageParam("print=1");?>" target="_blank"><span class="webform-small-button-left"></span><span class="webform-small-button-icon"></span><span class="webform-small-button-text">Печать учебного плана</span><span class="webform-small-button-right"></span></a>
        <a class="webform-small-button" href="<?=$APPLICATION->GetCurPageParam("statement&print=1");?>" target="_blank"><span class="webform-small-button-left"></span><span class="webform-small-button-icon"></span><span class="webform-small-button-text">Печать заявления</span><span class="webform-small-button-right"></span></a>
    <?}?>
    <p style="width: 100%; text-align: center; font-size: 16px;">Частное образовательное учреждение высшего образования</p>
    <p style="width: 100%; text-align: center; font-size: 16px;">ВОСТОЧНАЯ  ЭКОНОМИКО-ЮРИДИЧЕСКАЯ  ГУМАНИТАРНАЯ  АКАДЕМИЯ</p>

    <div style="float:right; text-align: right; width: 100%;margin-bottom: 30px;">
        <p>Утвержден приказом</p>
        <p>ректора Академии ВЭГУ</p>
        <p>от <?=$student->get_individual_subjects()->get_order_date()->format("d.m.Y");?> № <?=$student->get_individual_subjects()->get_order_number();?></p>
    </div>

    <div style="text-align: center; width: 100%;">
        <p>
              <strong>Индивидуальный учебный план</strong><br/>
			  <strong>образовательной программы высшего образования</strong><br/>
			  <strong>по направлению <?=$student->get_curent_program()->get_direction_code(); ?> <?=$student->get_curent_program()->get_direction(); ?></strong>,<br/>
			  <strong>направленности <?=$student->get_curent_program()->get_specialization()?></strong><br/>
        </p>
        <p></p>
        <p></p>
        <p></p>
    </div>
    <div style="width: 100%;">
        <!--<p>Дата заключения договора: <span id="std_contract_date"><?=$student->get_agreement_date()?></span></p>-->
        <p><strong>Магистрант:</strong> <?=$person->get_last_name()?> <?=$person->get_first_name()?> <?=$person->get_patronymic()?></p>
        <p><strong>Дата начала обучения:</strong> <?=$student->get_agreement_date()?></span></p>
        <p><strong>Тема магистерской диссертации:</strong> <?=$student->get_individual_subjects()->get_research_work_theme() ?></p>
        <p><strong>Научный руководитель:</strong> <?=$student->get_individual_subjects()->get_scientific_adviser() ?></p>
    </div>    
    
    <table class="plan" s cellpadding="0" cellspacing="0" class="packages">
         <tr><th colspan="2">Дисциплина (практика)</th>
             <th rowspan="2">Объем<br/>(в ЗЕТ)</th>
             <th rowspan="2">Семестр</th>
             <th rowspan="2">Форма промежуточной<br/>аттестации</th></tr>
         <tr><th>Код</th><th>Наименование</th></tr>
         <?php
         for ($x = 0; $x < 12;$x++) {
             PrintSemester($cycles, $x);
         }
        
         ?>
     </table>
	 <br/>
	 <br/>
	<table border="0" cellpadding="0" cellspacing="0" class="packages" width="100%">
		<tr>
			<td width="33%">Директор Института<br/> магистратуры и аспирантуры</td>
			<td width="33%"><img src="/extranet/services/curriculum/sign/ima.png"/></td>
			<td width="33%">К.Н. Исмагилов</td>
		</tr>
	</table>
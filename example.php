﻿<?php

include("KompasAPI.php");


function out_semester_work($JoinRow, $col_count_cur, $code, $subject_name, $hours, $semester_number, $type_testing) {
    if ($JoinRow) {
        if ($col_count_cur > 1) {
            echo "<tr>";
        }
        echo "<td>" . $semester_number . "</td><td>" .
        $type_testing . "</td>";
    } else {
        echo "<tr>";
        echo "<td>" . $code . "</td><td>" .
        $subject_name . "</td><td>" .
        $hours . "</td><td>" .
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
                    $JoinRow = ($coll_count > 1);
                    if ($JoinRow) {
                        echo "<tr><td rowspan=" . $coll_count . ">" . $sub_code . 
                                "</td><td rowspan=" . $coll_count . ">" . 
                                $subject->get_name() . "</td><td  rowspan=" . 
                                $coll_count . ">" . $hours .
                                "</td>";
                    }
                    $col_count_cur = 0;
                    
                    foreach ($subject as $sw) {
                        {
                            if ($sw->get_type_testing()<>""){
                                $col_count_cur ++;
                                out_semester_work($JoinRow, $col_count_cur, $sub_code, $subject->get_name(), $hours, $sw->get_number(), $sw->get_type_testing());
                            }
                            if ($sw->control_work()) {
                                $col_count_cur ++;
                                out_semester_work($JoinRow, $col_count_cur, $sub_code, $subject->get_name(), $hours, $sw->get_number(), "Контрольная работа");
                            }

                            if ($sw->course_work()) {
                                $col_count_cur ++;
                                out_semester_work($JoinRow, $col_count_cur, $sub_code, $subject->get_name(), $hours, $sw->get_number(), "Курсовая работа");
                            }

                            if ($sw->course_project()) {
                                $col_count_cur ++;
                                out_semester_work($JoinRow, $col_count_cur, $sub_code, $subject->get_name(), $hours, $sw->get_number(), "Курсовой проект");
                            }

                        }
                    }
                }
            }
        }
    }
}
$person = kompasFactory::get_student("012013121620244981");

echo "<p>".$person->get_full_name()."</p>";

if (!$person->student()->has_sended_request_individual_plan())
{
    $person->student()->get_individual_subjects()->add_subject("Психология");
    $person->student()->get_individual_subjects()->add_subject("Логика");
    $person->student()->get_individual_subjects()->add_subject("Региональная экономика");
    $person->student()->get_individual_subjects()->add_subject("Контроль и ревизия");
    $person->student()->get_individual_subjects()->add_subject("Маркетинговый анализ");
    $person->student()->get_individual_subjects()->add_subject("Информационные технологии в управлении");
    $person->student()->get_individual_subjects()->add_subject("Бюджетирование");
    $person->student()->get_individual_subjects()->add_subject("Анализ финансовой отчетности");
    $person->student()->get_individual_subjects()->add_subject("Основы документационного обеспечения управления");
    $person->student()->get_individual_subjects()->add_subject("Финансы организации");
    $person->student()->get_individual_subjects()->apply();
    echo "<p>Отправлен запрос на выбор дисциплин</p>";
}

$curricula = $person->student()->get_curent_program()->get_curriculum();
$cycles = $curricula->get_cycles();
//var_dump($cycles);
$PrintedSubject = array();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style type="text/css">
            .package_table{
                float: left;
                width: 90%;

            }

            .package_table h3{
                text-align:center;
                line-height: 0.1em;
            }

            .package_table h4{
                text-align:center;
                line-height: 0.1em;
                font-weight:normal;
            }

            .package_table h5{
                text-align:right;
                line-height: 0.1em;
                font-weight:normal;
            }

            .package_table p{
                line-height: 0.5em;
            }
            table.packages {
                background: url(../img/package_bg_left.gif) 0 37px no-repeat;
                margin: 20px 0px 0 0;
            }

            table.packages td {
                text-align: center;
                border-bottom: 1px solid #000;
                border-right: 1px solid #000;
                border-left: 1px solid #000;
                border-top: 1px solid #000;
                padding: 5px 0;
                vertical-align: middle;
                color: #000;
                background-color: #fff;
            }

            table.packages td.title,table.packages td.empty {
                background-color: transparent;
                padding: 0;
                margin: 0
                    border-bottom: none;
            }
            table.packages td.empty {
                border-bottom: 1px solid #dfe7e7;
                border-right: none;
            }

            table.packages th {
                vertical-align: bottom;
                background-color: transparent;

            }

            table.packages th.left {
                text-align: right;
                border-bottom: 1px solid #dfe7e7;
                padding: 3px 0;
                height: 25px;
                vertical-align: middle;
                color: #333;
                font-weight: bold;
                padding-right: 15px;
                font-size: 12px;

            }
            table.packages.foot {
                margin: 0;
                background: none;
                padding-bottom: 20px;
            }

        </style>
    </head>
    <body>
        <table cellpadding="0" cellspacing="0" class="packages">
            <tr><th colspan="2">Дисциплина (практика)</th>
                <th rowspan="2">Объем (в АЧ)</th>
                <th rowspan="2">Семестр</th>
                <th rowspan="2">Форма промежуточной аттестации</th></tr>
            <tr><th>Код</th><th>наименование</th></tr>
<?php
for ($x = -1; $x++ < 13;) {
    PrintSemester($cycles, $x);
}
?>
        </table>
    </body>
</html>
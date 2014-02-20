﻿<?php
define("kompas_wdsl", "");
define("kompas_login", "");
define("kompas_pass", "");
include("KompasAPI.php");

function GetGroupByName($gr_nm) {
    return null;
}

function PrintSemester($cyc, $sem) {
    global $PrintedSubject;
    foreach ($cyc as $cycle) {
        foreach ($cycle as $subject_group) {
            foreach ($subject_group as $subject) {
                if ((!in_array($subject->get_name(), $PrintedSubject)) && 
                        ($subject->work_count_in_semester($sem) > 0)) {
                    $PrintedSubject[] = $subject->get_name();
                    $sub_code = $subject->get_code();
                    if ($sub_code == "empty") {
                        $sub_code = "";
                    };
                    //$coll_count = $subject->work_count_in_semester($sem);
                    $coll_count = $subject->get_count();
                    $JoinRow = ($coll_count > 1);
                    if ($JoinRow) {
                        echo "<tr><td rowspan=" . $coll_count . ">" . $sub_code . 
                                "</td><td rowspan=" . $coll_count . ">" . 
                                $subject->get_name() . "</td><td  rowspan=" . 
                                $coll_count . ">" . $subject->get_subject_hours() .
                                "</td>"; //<td rowspan=".$coll_count.">".$sem."</td>";
                    }
                    $col_count_cur = 0;
                    foreach ($subject as $sw) {
                        //if ($sw->get_number()==$sem)
                        {
                            $col_count_cur = $col_count_cur + 1;
                            $tmp_gr = GetGroupByName("ПГ " . $subject->get_name());
                            if (0) {
                                $tmp_gr = GetGroupByName("ПГ Европейский суд и права человека");
                            }


                            if ($JoinRow) {
                                if ($col_count_cur > 1) {
                                    echo "<tr>";
                                }
                                echo "<td>" . $sw->get_number() . "</td><td>" . 
                                        $sw->get_type_testing() . "</td>";
                            } else {
                                echo "<tr>";
                                if ($tmp_gr == null) {
                                    echo "<td>" . $sub_code . "</td><td>" . 
                                            $subject->get_name() . "</td><td>" .
                                            $sw->get_hours() . "</td><td>" . 
                                            $sw->get_number() . "</td><td>" . 
                                            $sw->get_type_testing() . "</td>";
                                } else {
                                    echo "<td><a href='/extranet/workgroups/group/" . 
                                            $tmp_gr["ID"] . "/'>" .
                                            $subject->get_name() . 
                                            "</a></td><td>" . $sw->get_number() .
                                            "</td><td>" . $sw->get_hours() . 
                                            "</td><td>" . $sw->get_type_testing() . 
                                            "</td>";
                                }
                            }
                            echo "</tr>";
                        }
                    }
                }
            }
        }
    }
}

$curricula = kompasFactory::get_user_curriculum("012014012014043863");
$cycles = $curricula->get_cycles();
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
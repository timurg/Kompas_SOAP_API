<?php

include_once("api.asa.php");
include_once("api.kompas.php");

$agreement_number = '11502070908107';

//Загрузить данные по студенту:
$person  = kompasFactory::get_student($agreement_number); //komapsPerson
$student = $person->student(); //komapsStudent

//получить учебный план студента
$curriculum = $student->get_curent_program()->get_curriculum();//kompasCurriculum

//Получить список дисциплин учебного плана

$cycles = $curriculum->get_cycles();

foreach ($cycles as $cycle) { //учебный план состоит из нескольких циклов
        foreach ($cycle as $subject_group) {//цикл делится на группы дисциплин - обязательные и по выбору.
            foreach ($subject_group as $subject)
                { // каждая группа содержит дисциплину, каждая дисциплина соедржит учебную работу по семестрам.
				print $subject->get_name();
		}
	}
}
//Если студент отправил запрос на формирование индивидуального плана, то коллекция $person->student()->get_individual_subjects() содржит перечень выбранных дисциплин

//Отправить запрос на выбор дисциплин
if (!$person->student()->has_sended_request_individual_plan()) //Если студент не отправил запрос
{
    $person->student()->get_individual_subjects()->add_subject("Русский язык и культура речи");
    $person->student()->get_individual_subjects()->add_subject("Этика");
    $person->student()->get_individual_subjects()->add_subject("Концепции современного естествознания");
	$person->student()->get_individual_subjects()->add_subject("Педагогика раннего возраста");
	$person->student()->get_individual_subjects()->add_subject("Особенности организации педагогического процесса в дошкольном образовательном учреждении");
	$person->student()->get_individual_subjects()->add_subject("Особенности развивающей, предметной и игровой среды в дошкольном образовательном учреждении");
	$person->student()->get_individual_subjects()->add_subject("Педагогика игры");
	$person->student()->get_individual_subjects()->add_subject("Методика адаптации ребенка в дошкольном учреждении");
	$person->student()->get_individual_subjects()->add_subject("Современные образовательные программы в дошкольном образовательном учреждении");
    $person->student()->get_individual_subjects()->apply(); //отправить
}



//получить рейтинг студента по дисциплине
$rate = asaFactory::get_student_rating($agreement_number, "Педагогика игры");

?>
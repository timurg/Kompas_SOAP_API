<?php
        include_once("api.asa.php");


	$matr = asaFactory::get_matricula("012012042813051036");
	print $matr->get_count();
	$tt = new typeTesting(typeTesting::Test);
	$rec = $matr->find_record("Правовое регулирование информации, информатизации и защиты информации", $tt, 3);
	if ($rec != NULL)
	{
		print "<p>Дисциплина: <b>".$rec->get_subject()."</b></p>";
		print "<p>Форма аттестации: <b>".$rec->get_type_testing()."</b></p>";
		print "<p>Семестр: <b>".$rec->get_semester()."</b></p>";
		
		if ($rec->is_grs())
		{
			print "<p>*Используется БРС</p>";
			print "<p>Ваш текущий балл:".$rec->get_ball()." из ".$rec->get_max_ball()."</p>";
			print "<p>Рейтинг: <b>".$rec->get_rate()."</b></p>";
		}
		if ($rec->is_accepted())
		{
			print "<p>*Оценка получена</p>";
			if ($rec->is_passed())
			{
				print "<p>положительная оценка</p>";
			}
			else
			{
				print "<p>отрицательная оценка</p>";
			}
			print "<p>Дата аттестации: <b>".$rec->get_date()->format('Y-m-d H:i')."</b></p>";
			print "<p>Отметка : <b>".$rec->get_value()."</b></p>";
			print "<p>Оценка : <b>".$rec->get_text_value()."</b></p>";
		}
		
		
	}
        print "<pre>";
        var_dump(asaFactory::get_students_rating(array("012012042813051036", "11502070908107"), ""));
	print "</pre>";
?>
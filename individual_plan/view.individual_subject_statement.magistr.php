<div style=" width: 100%; overflow: hidden;position: relative; widows: 9;">
       <div id="capstatement">
              <p>Ректору Академии ВЭГУ </p>
              <p>Е.К. Миннибаеву</p>
              <p>студента Академии ВЭГУ</p>
              <p><?=$person->get_last_name()?> <?=$person->get_first_name()?> <?=$person->get_patronymic()?></p>
              <p>договор №<?=$student->get_agreement_number();?></p>
              <br />
       </div>
       <div style="float: left; text-align: center; width: 100%;">
           <p>заявление</p>
       </div>
       <div style="float: left; width: 100%;">
           <p style="text-indent: 20px;">Прошу утвердить мой индивидуальный учебный план по направлению  <u>  <?=$student->get_curent_program()->get_direction()?>  </u>  профилю  <u>  <?=$student->get_curent_program()->get_specialization()?>  </u>  на основании следующего сделанного мной выбора:</p>
       <div>

       <?
       $i=0;
       $s = $student->get_selected_individual_subject_list();
       for($i=0; $i<count($s)-1; $i++){?>
           <p style="text-indent: 20px;"><?=($i+1)?>. По двум предложенным дисциплинам <?=$s[$i]['code']?> выбираю:</br>
           <p class="subject-item" >  <?=$s[$i]['name']?>  </p>
       <?}?>
       <div class="nobreak">
              <p style="text-indent: 20px;display: block;"><?=($i+1)?>. По двум предложенным дисциплинам <?=$s[$i]['code']?> выбираю:</br>
              <p class="subject-item" style="text-indent: 20px;display: block;">  <?=$s[$i]['name']?>  </p>
              <!--<p style="display: block;width: 100%; margin:0; padding-top: 100px; min-height:20px;"><span style="display: block;float:right;">___________________________</span></p>
              <p style="display: block;width: 100%; margin:0; min-height:10px; font-size: 10px;"><span style="display: block;float:right;">подпись без расшифровки)</span></p>
              <p style="display: block;width: 100%; margin:0; min-height:20px;"><span style="display: block;float:right;">___________________________</span></p>
              <p style="display: block;width: 100%; margin:0; min-height:10px; font-size: 10px;"><span style="display: block;float:right;">(дата оформления заявления)</span></p>
              -->
              <?
              $i++;
              if ($student->get_individual_subjects()->get_research_work_theme() != ""){
                  ?>
                  <p style="text-indent: 20px;display: block;"><?=(++$i)?>. Тема ВКР (магистерской диссертации):  <?=$student->get_individual_subjects()->get_research_work_theme() ?>.</br>
                  <?
              }
              ?>
              <p style="text-indent: 20px;display: block;"><?=(++$i)?>. Выбрать основным изучаемым иностранным языком:  <?=$student->get_basic_lang() ?>, поскольку изучал его на предыдущем уровне высшего образования.</br>
              
              <div style="display: block;width: 100%; margin:0;">
                     <div style="display: block;text-align: right; position: relative;">
                            <p style="display: block; margin-top: 20px;margin-bottom: 0.5em;">___________________________</p>
                            <p style="display: block;font-size: 10px;padding-right: 20px;margin-top: 0.5em; margin-bottom: 0.5em;">(подпись без расшифровки)</p>
                            <p style="display: block; margin-bottom: 0.5em;">___________________________</p>
                            <p style="display: block;font-size: 10px;padding-right: 20px;margin-top: 0.5em; margin-bottom: 0.5em;">(дата оформления заявления)</p>   
                     </div>
              </div>
              
       </div>
</div>
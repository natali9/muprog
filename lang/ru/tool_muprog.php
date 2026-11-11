<?php
// This file is part of MuTMS suite of plugins for Moodle™ LMS.
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

// phpcs:disable moodle.Files.BoilerplateComment.CommentEndedTooSoon

/**
 * Program enrolment plugin language file.
 *
 * @package    tool_muprog
 * @copyright  2022 Open LMS (https://www.openlms.net/)
 * @copyright  2025 Petr Skoda
 * @author     Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allocation'] = 'Распределение';
$string['allocation_archive'] = 'Архивировать распределение';
$string['allocation_reset'] = 'Сбросить ход выполнения программы';
$string['allocation_reset_updateallocation'] = 'Обновить распределение';
$string['allocation_restore'] = 'Восстановить распределение';
$string['allocation_update'] = 'Обновить распределение';
$string['allocationdate'] = 'Дата распределения';
$string['allocationend'] = 'Окончание распределения';
$string['allocationend_help'] = 'Значение даты окончания распределения зависит от включенных источников распределения. Обычно после этой даты, если она указана, новые распределения невозможны.';
$string['allocations'] = 'Распределения';
$string['allocationsources'] = 'Источники распределения';
$string['allocationstart'] = 'Начало распределения';
$string['allocationstart_help'] = 'Значение даты начала распределения зависит от включенных источников распределения. Обычно новые распределения возможны только после этой даты, если она указана.';
$string['allprograms'] = 'Все программы';
$string['appendinto'] = 'Добавить в элемент';
$string['appenditem'] = 'Добавить элемент';
$string['archived'] = 'Архивировано';
$string['attendance'] = 'Посещаемость офлайн';
$string['attendance_effective'] = 'Дата вступления в силу';
$string['attendance_status'] = 'Статус';
$string['attendance_status_completed'] = 'Завершено';
$string['attendance_status_failed'] = 'Не выполнено';
$string['attendance_status_noshow'] = 'Неявка';
$string['attendance_take'] = 'Отметить посещаемость';
$string['calendarprogramdue'] = '{$a} должно быть выполнено';
$string['calendarprogramend'] = '{$a} заканчивается';
$string['calendarprogramstart'] = '{$a} начинается';
$string['catalogue'] = 'Каталог программ';
$string['catalogue_actions'] = 'Действия с каталогом';
$string['catalogue_dofilter'] = 'Поиск';
$string['catalogue_resetfilter'] = 'Очистить';
$string['catalogue_searchtext'] = 'Текст для поиска';
$string['catalogue_tag'] = 'Фильтровать по тегу';
$string['certificatetemplatechoose'] = 'Выберите шаблон...';
$string['cohorts'] = 'Доступно для когорт';
$string['cohorts_help'] = 'Непубличные программы могут быть доступны только для определенных участников когорт.

Статус видимости не влияет на уже выделенные программы.';
$string['columnusedalready'] = 'Столбец уже используется';
$string['completiondate'] = 'Дата завершения';
$string['completiondelay'] = 'Задержка завершения';
$string['completionoverride'] = 'Переопределение завершения';
$string['creategroups'] = 'Группы курса';
$string['creategroups_help'] = 'Если этот параметр включен, в каждом курсе, добавленном в программу, будет создана группа, а все назначенные пользователи будут добавлены в нее в качестве участников.';
$string['credits'] = 'Кредиты';
$string['credits_progress'] = 'Текущее количество кредитов: {$a->current}/{$a->total}';
$string['credits_requiredcredits'] = 'Необходимое количество кредитов: {$a}';
$string['currentcontextonly'] = 'Исключить подкатегории';
$string['customfields'] = 'Пользовательские поля программы';
$string['customfields_allocation'] = 'Пользовательские поля распределения программы';
$string['customfieldsettings'] = 'Общие настройки пользовательских полей программы';
$string['customfieldvisible:allocated'] = 'Пользователи, назначенные на программы';
$string['customfieldvisible:allocatee'] = 'Получатель';
$string['customfieldvisible:everyone'] = 'Все, кто может видеть информацию о других программах';
$string['customfieldvisible:viewcapability'] = 'Пользователи с возможностью просмотра программ';
$string['customfieldvisibleto'] = 'Содержимое поля видно';
$string['deleteallocation'] = 'Удалить распределение по программам';
$string['deleteattendance'] = 'Удалить офлайн-посещаемость';
$string['deletecourse'] = 'Удалить курс';
$string['deletecredits'] = 'Удалить кредиты';
$string['deleteset'] = 'Удалить набор';
$string['duedate'] = 'Срок сдачи';
$string['enrolrole'] = 'Роль в курсе';
$string['enrolrole_desc'] = 'Выберите роль, которая будет использоваться программами для зачисления на курс';
$string['errorcontentproblem'] = 'Обнаружена проблема в структуре содержимого программы, завершение программы не будет отслеживаться корректно!';
$string['errorcoursemissing'] = 'Курс отсутствует';
$string['errorcoursesmissing'] = 'Отсутствуют курсы: {$a}';
$string['errordifferenttenant'] = 'Доступ к программе из другого тенанта невозможен';
$string['errorinvalidoverridedates'] = 'Недопустимые переопределения дат';
$string['errornoallocation'] = 'Программа не распределена';
$string['errornoallocations'] = 'Не найдено пользовательских выделений памяти';
$string['errornomyprograms'] = 'Вы не распределены ни по одной программе.';
$string['errornoprograms'] = 'Программ не найдено.';
$string['errornorequests'] = 'Запросов на программы не найдено';
$string['errornotenabled'] = 'Плагин "Программы" не активирован';
$string['event_allocation_archived'] = 'Распределение пользователя заархивировано';
$string['event_allocation_completed'] = 'Пользователь завершил программу';
$string['event_allocation_created'] = 'Пользователь распределен по программе';
$string['event_allocation_deleted'] = 'Пользователь исключен из программы';
$string['event_allocation_restored'] = 'Распределение пользователя восстановлено';
$string['event_allocation_updated'] = 'Распределение пользователя по программе обновлено';
$string['event_allocation_viewed'] = 'Распределение по программе просмотрено';
$string['event_catalogue_program_viewed'] = 'Просмотрен каталог программ';
$string['event_program_archived'] = 'Программа заархивирована';
$string['event_program_created'] = 'Программа создана';
$string['event_program_deleted'] = 'Программа удалена';
$string['event_program_restored'] = 'Программа восстановлена';
$string['event_program_updated'] = 'Программа обновлена';
$string['event_program_viewed'] = 'Программа просмотрена';
$string['evidence'] = 'Другие доказательства';
$string['evidence_details'] = 'Подробности';
$string['evidence_detailsdefault'] = 'Подробности по умолчанию';
$string['evidencedate'] = 'Дата завершения доказательства';
$string['evidenceupdate'] = 'Обновить другие доказательства';
$string['evidenceupload'] = 'Загрузить доказательства завершения';
$string['evidenceupload_csvfile'] = 'CSV-файл';
$string['evidenceupload_errors'] = 'Обнаружено {$a} недопустимых строк';
$string['evidenceupload_skipped'] = 'Пропущено {$a} строк';
$string['evidenceupload_updated'] = 'Доказательства завершения обновлены для {$a} пользователей';
$string['export'] = 'Экспорт программ';
$string['exportfile_info'] = 'Информация';
$string['exportfile_programs'] = 'Программы';
$string['exportformat'] = 'Формат файла';
$string['exportformat_csv'] = 'CSV';
$string['exportformat_json'] = 'JSON';
$string['extdb_query_allocation'] = 'Распределение программ';
$string['favourite'] = 'Избранная программа';
$string['fixeddate'] = 'В фиксированную дату';
$string['importallocationend'] = 'Завершение распределения ({$a})';
$string['importallocationstart'] = 'Начало распределения ({$a})';
$string['importprogramallocation'] = 'Импортировать настройки распределения';
$string['importprogramallocationconfirmation'] = 'Вы импортируете настройки распределения из программы __{$a->fullname} / {$a->idnumber} / {$a->category}__. 

 Пожалуйста, выберите все настройки, которые вы хотите импортировать.';
$string['importprogramcontent'] = 'Импортировать содержимое программы';
$string['importprogramcontentconfirmation'] = 'Вы импортируете содержимое из программы __{$a->fullname} / {$a->idnumber} / {$a->category}__.';
$string['importprogramdue'] = 'Срок выполнения программы ({$a})';
$string['importprogramend'] = 'Завершение программы ({$a})';
$string['importprogramstart'] = 'Начало программы ({$a})';
$string['importselectprogram'] = 'Выбрать программу';
$string['includesubcontexts'] = 'Включить подконтексты';
$string['invalidallocationdates'] = 'Неверные даты распределения программы';
$string['invalidcompletiondate'] = 'Неверная дата завершения программы';
$string['item'] = 'Предмет';
$string['item_create_attendance'] = 'Добавить офлайн-посещаемость';
$string['item_create_course'] = 'Добавить курсы';
$string['item_create_credits'] = 'Добавить кредиты';
$string['item_create_set'] = 'Добавить набор';
$string['item_type'] = 'Тип предмета';
$string['item_update_attendance'] = 'Обновить офлайн-посещаемость';
$string['itemcompletion'] = 'Завершение изучения предмета';
$string['itempoints'] = 'Баллы';
$string['itemrecalculate'] = 'Пересчитать завершение изучения предмета';
$string['management'] = 'Управление программой';
$string['management_index_actions'] = 'Действия с программами';
$string['management_program_allocation_actions'] = 'Действия с настройками распределения';
$string['management_program_general_actions'] = 'Действия с программой';
$string['management_program_users_actions'] = 'Действия с пользователями';
$string['messageprovider:allocation_notification'] = 'Уведомление о распределении программы';
$string['messageprovider:approval_reject_notification'] = 'Уведомление об отклонении запроса на участие в программе';
$string['messageprovider:approval_request_notification'] = 'Уведомление о запросе на утверждение программы';
$string['messageprovider:cc_supervisor_notification'] = 'Копия уведомлений о подчиненных программах';
$string['messageprovider:completion_notification'] = 'Уведомление о завершении программы';
$string['messageprovider:deallocation_notification'] = 'Уведомление о перераспределении программы';
$string['messageprovider:due_notification'] = 'Уведомление о просроченной программе';
$string['messageprovider:duesoon_notification'] = 'Уведомление о скором сроке сдачи программы';
$string['messageprovider:endcompleted_notification'] = 'Уведомление о завершении программы';
$string['messageprovider:endfailed_notification'] = 'Уведомление о завершении программы с ошибкой';
$string['messageprovider:endsoon_notification'] = 'Уведомление о скором завершении программы';
$string['messageprovider:reset_notification'] = 'Уведомление о сбросе программы';
$string['messageprovider:start_notification'] = 'Уведомление о запуске программы';
$string['moveafter'] = 'Переместить "{$a->item}" после "{$a->target}"';
$string['movebefore'] = 'Переместить "{$a->item}" перед "{$a->target}"';
$string['moveinto'] = 'Переместить "{$a->item}" в "{$a->target}"';
$string['moveitem'] = 'Переместить элемент';
$string['moveitemcancel'] = 'Отменить перемещение';
$string['muprog:addcourse'] = 'Добавить курс в программы';
$string['muprog:addtocertifications'] = 'Добавить программу в сертификаты';
$string['muprog:admin'] = 'Расширенное администрирование программ';
$string['muprog:allocate'] = 'Распределить пользователей по программам';
$string['muprog:clone'] = 'Клонировать содержимое и настройки программы';
$string['muprog:configurecustomfields'] = 'Настройка пользовательских полей программы';
$string['muprog:deallocate'] = 'Отключение пользователей от программ';
$string['muprog:delete'] = 'Удаление программ';
$string['muprog:edit'] = 'Добавление и обновление программ';
$string['muprog:export'] = 'Экспорт программ';
$string['muprog:manageallocation'] = 'Управление распределением пользователей';
$string['muprog:manageevidence'] = 'Управление другими доказательствами выполнения';
$string['muprog:reset'] = 'Сброс хода выполнения программы';
$string['muprog:takeattendance'] = 'Отмежевание от программы';
$string['muprog:upload'] = 'Загрузка программ';
$string['muprog:view'] = 'Просмотр управления программами';
$string['muprog:viewcatalogue'] = 'Доступ к каталогу программ';
$string['muprog:viewuserprograms'] = 'Просмотр программ других пользователей';
$string['myprograms'] = 'Мои программы';
$string['notification_allocation'] = 'Пользователь назначен на должность';
$string['notification_allocation_body'] = 'Здравствуйте, {$a->user_fullname},

вы назначены на должность в рамках программы "{$a->program_fullname}", дата начала — {$a->program_startdate}.
';
$string['notification_allocation_description'] = 'Уведомление, отправляемое пользователям при назначении на должность в рамках программы.';
$string['notification_allocation_subject'] = 'Уведомление о назначении на должность в рамках программы';
$string['notification_cc_supervisor_body'] = 'Здравствуйте, {$a->supervisor_fullname},

следующему пользователю было отправлено уведомление:

* {$a->subordinate_title}: {$a->user_fullname}
* Тип уведомления: {$a->notification_name}
* Соответствующая программа: {$a->program_fullname}

';
$string['notification_cc_supervisor_subject'] = 'Уведомление {$a->supervisor_title} — {$a->program_fullname}';
$string['notification_completion'] = 'Программа завершена';
$string['notification_completion_body'] = 'Здравствуйте, {$a->user_fullname},

вы завершили программу "{$a->program_fullname}".
';
$string['notification_completion_description'] = 'Уведомление, отправляемое пользователям после завершения ими программы.';
$string['notification_completion_subject'] = 'Программа завершена';
$string['notification_deallocation'] = 'Пользователь исключен из программы';
$string['notification_deallocation_body'] = 'Здравствуйте, {$a->user_fullname},

вы исключены из программы "{$a->program_fullname}".
';
$string['notification_deallocation_description'] = 'Уведомление, отправляемое пользователям после их исключения из программы.';
$string['notification_deallocation_subject'] = 'Уведомление об исключении из программы';
$string['notification_due'] = 'Программа просрочена';
$string['notification_due_body'] = 'Здравствуйте, {$a->user_fullname},

завершение программы "{$a->program_fullname}" ожидалось до {$a->program_duedate}.
';
$string['notification_due_description'] = 'Уведомление, отправляемое пользователям, когда срок завершения их программы истекает.';
$string['notification_due_subject'] = 'Ожидалось завершение программы';
$string['notification_duesoon'] = 'Срок завершения программы скоро наступит';
$string['notification_duesoon_body'] = 'Здравствуйте, {$a->user_fullname},

завершение программы "{$a->program_fullname}" ожидается {$a->program_duedate}.
';
$string['notification_duesoon_description'] = 'Уведомление отправлено пользователям до даты завершения их программы, если программа еще не завершена.';
$string['notification_duesoon_subject'] = 'Завершение программы ожидается в ближайшее время';
$string['notification_endcompleted'] = 'Программа завершена';
$string['notification_endcompleted_body'] = 'Здравствуйте {$a->user_fullname},

программа "{$a->program_fullname}" завершена, вы завершили ее раньше срока.
';
$string['notification_endcompleted_description'] = 'Уведомление отправлено пользователям после завершения их программы.';
$string['notification_endcompleted_subject'] = 'Программа завершена';
$string['notification_endfailed'] = 'Программа не завершена';
$string['notification_endfailed_body'] = 'Здравствуйте {$a->user_fullname},

программа "{$a->program_fullname}" завершена, вы не смогли ее завершить.
';
$string['notification_endfailed_description'] = 'Уведомление отправлено пользователям после завершения программы, которую они не смогли завершить.';
$string['notification_endfailed_subject'] = 'Программа не завершена';
$string['notification_endsoon'] = 'Дата завершения программы скоро';
$string['notification_endsoon_body'] = 'Здравствуйте, {$a->user_fullname},

программа "{$a->program_fullname}" завершается {$a->program_enddate}.
';
$string['notification_endsoon_description'] = 'Уведомление отправляется пользователям до даты завершения программы, если программа еще не завершена.';
$string['notification_endsoon_subject'] = 'Программа скоро завершится';
$string['notification_reset'] = 'Сброс прогресса пользователя';
$string['notification_reset_body'] = 'Здравствуйте, {$a->user_fullname},

ваш прогресс в программе "{$a->program_fullname}" был сброшен.
';
$string['notification_reset_description'] = 'Уведомление отправляется пользователям при сбросе прогресса в программе.';
$string['notification_reset_subject'] = 'Уведомление о сбросе прогресса в программе';
$string['notification_start'] = 'Программа запущена';
$string['notification_start_body'] = 'Здравствуйте, {$a->user_fullname},

программа "{$a->program_fullname}" запущена.
';
$string['notification_start_description'] = 'Уведомление отправляется пользователям при запуске программы.';
$string['notification_start_subject'] = 'Программа запущена';
$string['notificationdates'] = 'Даты уведомлений';
$string['notset'] = 'Не задано';
$string['plugindisabled'] = 'Плагин для записи на программы отключен, программы не будут работать. 

[Включить плагин]({$a->url})';
$string['pluginname'] = 'Программы';
$string['pluginname_desc'] = 'Программы предназначены для создания наборов курсов.';
$string['privacy:metadata:field:allocationid'] = 'Идентификатор распределения программы';
$string['privacy:metadata:field:archived'] = 'Заархивирована ли запись';
$string['privacy:metadata:field:createdby'] = 'Создано';
$string['privacy:metadata:field:datajson'] = 'Информация о запросе';
$string['privacy:metadata:field:evidencejson'] = 'Информация о подтверждении прохождения';
$string['privacy:metadata:field:explanation'] = 'Пояснение';
$string['privacy:metadata:field:issueid'] = 'Идентификатор проблемы';
$string['privacy:metadata:field:itemid'] = 'Идентификатор элемента';
$string['privacy:metadata:field:programid'] = 'Идентификатор программы';
$string['privacy:metadata:field:quantity'] = 'Количество';
$string['privacy:metadata:field:reason'] = 'Причина';
$string['privacy:metadata:field:rejectedby'] = 'Запрос отклонен';
$string['privacy:metadata:field:sourcedatajson'] = 'Информация об источнике распределения';
$string['privacy:metadata:field:sourceid'] = 'Источник распределения';
$string['privacy:metadata:field:status'] = 'Статус';
$string['privacy:metadata:field:takenby'] = 'Ответственный за посещаемость';
$string['privacy:metadata:field:timeallocated'] = 'Дата распределения по программе';
$string['privacy:metadata:field:timecompleted'] = 'Дата завершения';
$string['privacy:metadata:field:timecreated'] = 'Дата создания';
$string['privacy:metadata:field:timedue'] = 'Срок выполнения';
$string['privacy:metadata:field:timeeffective'] = 'Дата вступления в силу';
$string['privacy:metadata:field:timeend'] = 'Дата окончания';
$string['privacy:metadata:field:timerejected'] = 'Дата отклонения';
$string['privacy:metadata:field:timerequested'] = 'Дата запроса';
$string['privacy:metadata:field:timestart'] = 'Дата начала';
$string['privacy:metadata:field:userid'] = 'Идентификатор пользователя';
$string['privacy:metadata:table:tool_muprog_allocation'] = 'Информация о распределении по программе';
$string['privacy:metadata:table:tool_muprog_attendance'] = 'Посещаемость в офлайн-режиме';
$string['privacy:metadata:table:tool_muprog_cert_issue'] = 'Выдача сертификатов о зачислении на программу';
$string['privacy:metadata:table:tool_muprog_completion'] = 'Завершение обучения по программе';
$string['privacy:metadata:table:tool_muprog_evidence'] = 'Информация о других подтверждениях завершения обучения';
$string['privacy:metadata:table:tool_muprog_request'] = 'Информация о запросе на зачисление';
$string['program'] = 'Программа';
$string['program_actions'] = 'Действия с программой';
$string['program_allocations_edit'] = 'Изменить распределение';
$string['program_archive'] = 'Архивировать программу';
$string['program_archive_info'] = 'Архивирование программы:

* приостанавливает зачисление на курсы программы,
* удаляет события программы из календаря,
* предотвращает обновление программы,
* и в целом скрывает программу от обычных пользователей.

Архивирование является обязательным шагом перед удалением программы.';
$string['program_create'] = 'Добавить программу';
$string['program_delete'] = 'Удалить программу';
$string['program_delete_info'] = 'При удалении программы все данные программы удаляются, а пользователи исключаются из курсов программы.';
$string['program_move'] = 'Переместить программу';
$string['program_restore'] = 'Восстановить программу';
$string['program_restore_info'] = 'При восстановлении программы отменяются изменения, внесенные при ее архивировании.

 Однако после этого рекомендуется проверить все настройки программы и распределение пользователей.';
$string['program_update'] = 'Обновить программу';
$string['programallocations'] = 'Распределение пользователей по программам';
$string['programautofix'] = 'Автоматическое исправление программы';
$string['programcategory'] = 'Категория программы';
$string['programcompletion'] = 'Дата завершения программы';
$string['programcompletionoverride'] = 'Переопределить дату завершения программы';
$string['programdue'] = 'Срок выполнения программы';
$string['programdue_date'] = 'Дата выполнения';
$string['programdue_delay'] = 'Срок выполнения после начала';
$string['programdue_help'] = 'Срок выполнения программы указывает, когда пользователи должны завершить обучение по программе.';
$string['programend'] = 'Завершение программы';
$string['programend_date'] = 'Дата окончания программы';
$string['programend_delay'] = 'Завершение после начала';
$string['programend_help'] = 'Пользователи не могут добавлять курсы в программу после ее завершения.';
$string['programidnumber'] = 'Идентификатор программы';
$string['programimage'] = 'Изображение программы';
$string['programname'] = 'Название программы';
$string['programprogress'] = 'Ход выполнения';
$string['programs'] = 'Программы';
$string['programstart'] = 'Начало программы';
$string['programstart_allocation'] = 'Начать сразу после распределения';
$string['programstart_date'] = 'Дата начала программы';
$string['programstart_delay'] = 'Отложить начало после распределения';
$string['programstart_help'] = 'Пользователи не могут добавлять курсы в программу до ее начала.';
$string['programstatus'] = 'Статус программы';
$string['programstatus_any'] = 'Любой статус программы';
$string['programstatus_archived'] = 'Архивировано';
$string['programstatus_archivedcompleted'] = 'Архивировано и завершено';
$string['programstatus_completed'] = 'Завершено';
$string['programstatus_failed'] = 'Не выполнено';
$string['programstatus_future'] = 'Еще не открыта';
$string['programstatus_open'] = 'Открыта';
$string['programstatus_overdue'] = 'Просрочена';
$string['programurl'] = 'URL программы';
$string['publicaccess'] = 'Публичная';
$string['publicaccess_help'] = 'Публичные программы видны всем пользователям.

 Статус видимости не влияет на уже назначенные программы.';
$string['purchaseaccess'] = 'Доступ по покупке';
$string['resettype'] = 'Тип сброса';
$string['resettype_deallocate'] = 'Только освобождение памяти программы';
$string['resettype_full'] = 'Полная очистка курса';
$string['resettype_none'] = 'Нет';
$string['resettype_standard'] = 'Стандартная очистка курса';
$string['scheduling'] = 'Планирование';
$string['sequencetype'] = 'Тип завершения';
$string['sequencetype_allinanyorder'] = 'Все в произвольном порядке';
$string['sequencetype_allinorder'] = 'Все по порядку';
$string['sequencetype_atleast'] = 'Не менее {$a->min}';
$string['sequencetype_minpoints'] = 'Минимум {$a->minpoints} баллов';
$string['set'] = 'Набор';
$string['settings'] = 'Настройки программы';
$string['source'] = 'Источник';
$string['source_approval'] = 'Запросы с утверждением';
$string['source_approval_allownew'] = 'Разрешить утверждения';
$string['source_approval_allownew_desc'] = 'Разрешить добавление новых источников _запросов с утверждением_ в программы';
$string['source_approval_allowrequest'] = 'Разрешить новые запросы';
$string['source_approval_confirm'] = 'Пожалуйста, подтвердите, что вы хотите запросить доступ к программе.';
$string['source_approval_daterejected'] = 'Дата отклонения запроса';
$string['source_approval_daterequested'] = 'Дата запроса';
$string['source_approval_makerequest'] = 'Запросить доступ';
$string['source_approval_notification_approval_reject_body'] = 'Здравствуйте, {$a->user_fullname},

ваш запрос на доступ к программе "{$a->program_fullname}" был отклонен.

{$a->reason}
';
$string['source_approval_notification_approval_reject_subject'] = 'Уведомление об отклонении запроса на доступ к программе';
$string['source_approval_notification_approval_request_body'] = '
Пользователь {$a->user_fullname} запросил доступ к программе "{$a->program_fullname}".
';
$string['source_approval_notification_approval_request_subject'] = 'Уведомление о запросе на участие в программе';
$string['source_approval_rejectionreason'] = 'Причина отклонения';
$string['source_approval_request'] = 'Запрос';
$string['source_approval_requestallowed'] = 'Запросы разрешены';
$string['source_approval_requestapprove'] = 'Подтвердить запрос';
$string['source_approval_requestdelete'] = 'Удалить запрос';
$string['source_approval_requestnotallowed'] = 'Запросы запрещены';
$string['source_approval_requestpending'] = 'Запрос на доступ ожидает рассмотрения';
$string['source_approval_requestreject'] = 'Отклонить запрос';
$string['source_approval_requestrejected'] = 'Запрос на доступ отклонен';
$string['source_approval_requests'] = 'Запросы';
$string['source_cohort'] = 'Автоматическое распределение по когортам';
$string['source_cohort_allownew'] = 'Разрешить распределение по когортам';
$string['source_cohort_allownew_desc'] = 'Разрешить добавление новых источников _автоматического распределения по когортам_ в программы';
$string['source_cohort_cohortstoallocate'] = 'Распределить по когортам';
$string['source_extdb'] = 'Выделение из внешней базы данных';
$string['source_extdb_allownew'] = 'Разрешить синхронизацию с внешней базой данных';
$string['source_extdb_allownew_desc'] = 'Для синхронизации с внешней базой данных необходимо выбрать предопределенный запрос.
 Чтобы использовать этот источник выделения, администраторам сайта необходимо настроить сервер внешней базы данных и запросы в разделе _Администрирование сайта / Сервер / Внешние базы данных_.';
$string['source_extdb_archiveremoved'] = 'Архивировать удаленных пользователей';
$string['source_extdb_lastsync'] = 'Последнее обновление распределения';
$string['source_extdb_pendingsync'] = 'Ожидание обновления распределения';
$string['source_extdb_sync'] = 'Обработать распределения';
$string['source_extdb_sync_notqueued'] = 'Не удалось создать специальную задачу для распределения из внешней базы данных';
$string['source_extdb_sync_queued'] = 'Создана специальная задача для распределения из внешней базы данных';
$string['source_manual'] = 'Распределение вручную';
$string['source_manual_allocateusers'] = 'Распределить пользователей';
$string['source_manual_csvfile'] = 'CSV-файл';
$string['source_manual_hasheaders'] = 'Первая строка — заголовок';
$string['source_manual_potusers'] = 'Кандидаты на распределение';
$string['source_manual_potusersmatching'] = 'Сопоставление кандидатов на распределение';
$string['source_manual_result_assigned'] = '{$a} пользователей были распределены по программам.';
$string['source_manual_result_errors'] = 'При распределении программ обнаружено {$a} ошибок.';
$string['source_manual_result_skipped'] = '{$a} пользователей уже были распределены по программам.';
$string['source_manual_timeduecolumn'] = 'Столбец со временем выполнения';
$string['source_manual_timeendcolumn'] = 'Столбец с окончанием времени';
$string['source_manual_timestartcolumn'] = 'Столбец с началом времени';
$string['source_manual_uploadusers'] = 'Загрузить данные о распределении';
$string['source_manual_usercolumn'] = 'Столбец с идентификацией пользователя';
$string['source_manual_usermapping'] = 'Сопоставление пользователей через';
$string['source_manual_userupload_allocated'] = 'Распределены по программе \'{$a}\'';
$string['source_manual_userupload_alreadyallocated'] = 'Уже распределены по программе \'{$a}\'';
$string['source_manual_userupload_invalidprogram'] = 'Невозможно выделить память для \'{$a}\'';
$string['source_mucertify'] = 'Сертификаты';
$string['source_mucertify_allownew'] = 'Разрешить выделение сертификатов';
$string['source_mucertify_allownew_desc'] = 'Разрешить добавление новых источников _certification_ в программы';
$string['source_program'] = 'Завершённая программа';
$string['source_program_allownew'] = 'Разрешить распределение по завершении программы';
$string['source_program_allownew_desc'] = 'Разрешить добавление новых источников _завершения программы_ в программы';
$string['source_program_completedprogram'] = 'Программа, которую необходимо завершить';
$string['source_selfallocation'] = 'Самостоятельное распределение';
$string['source_selfallocation_allocate'] = 'Зарегистрироваться';
$string['source_selfallocation_allownew'] = 'Разрешить самостоятельное распределение';
$string['source_selfallocation_allownew_desc'] = 'Разрешить добавление новых источников _самостоятельного распределения_ в программы';
$string['source_selfallocation_allowsignup'] = 'Разрешить регистрацию новых участников';
$string['source_selfallocation_confirm'] = 'Пожалуйста, подтвердите, что вы хотите принять участие в программе.';
$string['source_selfallocation_enable'] = 'Включить самостоятельное распределение';
$string['source_selfallocation_key'] = 'Ключ для регистрации';
$string['source_selfallocation_keyrequired'] = 'Требуется ключ для регистрации';
$string['source_selfallocation_maxusers'] = 'Максимальное количество пользователей';
$string['source_selfallocation_maxusers_status'] = 'Пользователи {$a->count}/{$a->max}';
$string['source_selfallocation_maxusersreached'] = 'Максимальное количество пользователей, самостоятельно распределивших места, уже достигнуто';
$string['source_selfallocation_signupallowed'] = 'Регистрация разрешена';
$string['source_selfallocation_signupnotallowed'] = 'Регистрация запрещена';
$string['taballocation'] = 'Настройки распределения';
$string['tabcontent'] = 'Контент';
$string['tabgeneral'] = 'Общие';
$string['tabusers'] = 'Пользователи';
$string['tabvisibility'] = 'Видимость каталога';
$string['tagarea_tool_muprog_program'] = 'Программы';
$string['task_extdb_cron'] = 'Cron-задача для распределения программ из внешней базы данных';
$string['task_extdb_sync'] = 'Распределение программ из внешней базы данных';
$string['taskcertificate'] = 'Cron-задача для выдачи сертификатов для программ';
$string['taskcron'] = 'Cron-задача для плагинов для программ';
$string['unlinkeditems'] = 'Отвязанные элементы';
$string['updateattendance'] = 'Обновить посещаемость офлайн-занятий';
$string['updatecourse'] = 'Обновить курс';
$string['updatecredits'] = 'Обновить кредиты';
$string['updatescheduling'] = 'Обновить расписание';
$string['updateset'] = 'Обновить набор';
$string['updatesource'] = 'Обновить {$a}';
$string['upload'] = 'Загрузить программы';
$string['upload_files'] = 'Файлы';
$string['upload_files_error'] = 'Ожидается несколько CSV-файлов, один JSON-файл или один Zip-архив';
$string['upload_invalidcount'] = 'Некорректные записи';
$string['upload_preview'] = 'Предварительный просмотр данных';
$string['upload_status'] = 'Статус';
$string['upload_status_invalid'] = 'Некорректно';
$string['upload_targetcontext'] = 'Добавить программы в контекст';
$string['upload_uploadcount'] = 'Программы для загрузки';
$string['upload_usecategory'] = 'Использовать столбец категории для контекстов';
$string['userupload_completion_error'] = 'Завершение программы не может быть обновлено';
$string['userupload_completion_updated'] = 'Завершение программы обновлено';
$string['duration_days'] = 'Срок прохождения (дней)';
$string['duration_days_help'] = 'Количество дней, отведенных сотруднику на прохождение этого курса.';
$string['error_duration_positive'] = 'Срок прохождения должен быть больше 0 дней.';
$string['offset_type'] = 'Начало отсчета срока';
$string['offset_type_help'] = 'Выберите, с какой даты начать отсчёт указанных выше дней.';
$string['offset_program_start'] = 'С даты назначения в программу';
$string['offset_prev_complete'] = 'С даты успешного завершения предыдущего курса в программе';
$string['block_on_deadline'] = 'Блокировать доступ после истечения срока';
$string['block_on_deadline_help'] = 'Если этот флажок установлен, пользователь не сможет открыть курс после наступления дедлайна.  Если флажок снят, курс останется доступным, но будет считаться просроченным.';
$string['condition_description'] = 'Доступ ограничен сроком прохождения программы';
$string['sequencetype_byschedule'] = 'По расписанию (в соответствии с продолжительностью курсов)';
$string['course_start_date'] = 'Дата начала курса';
$string['course_deadline_date'] = 'Дедлайн курса';
$string['current_deadline'] = 'Текущий дедлайн';
$string['new_deadline'] = 'Новый дедлайн';
$string['new_deadline_help'] = 'Выберите новую дату дедлайна для этого курса. Дата старта курса не изменяется — курс остаётся открытым с прежней даты. Если снять галочку «Включить», дедлайн будет удалён (срок не ограничен).';
$string['override_deadline'] = 'Переопределить дедлайн';
$string['current_startdate'] = 'Текущая дата старта';
$string['new_startdate'] = 'Новая дата старта';
$string['new_startdate_help'] = 'Выберите новую дату открытия доступа к курсу. Дедлайн курса не изменяется. Если снять галочку «Включить», дата старта будет снята (курс станет доступен сразу).';
$string['override_startdate'] = 'Переопределить дату старта';
$string['task_fix_cohort_schedules'] = 'Автоматический пересчёт расписания курсов (исправление синхронизации с когортами)';
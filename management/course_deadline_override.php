<?php
// This file is part of MuTMS suite of plugins for Moodle™ LMS.

/**
 * Переопределение дедлайна курса для конкретного пользователя.
 *
 * @package    tool_muprog
 * @copyright  2026 Your Name
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/** @var moodle_database $DB */
/** @var moodle_page $PAGE */

use tool_muprog\local\program;
use core\url;

define('AJAX_SCRIPT', true);

require('../../../../config.php');

$allocationid = required_param('allocationid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$itemid = required_param('itemid', PARAM_INT);

$allocation = $DB->get_record('tool_muprog_allocation', ['id' => $allocationid], '*', MUST_EXIST);
$program = $DB->get_record('tool_muprog_program', ['id' => $allocation->programid], '*', MUST_EXIST);

$context = context::instance_by_id($program->contextid);
require_login();
require_capability('tool/muprog:manageallocation', $context);

if ($program->archived || $allocation->archived) {
    throw new \moodle_exception('cannoteditarchived', 'tool_muprog');
}

$PAGE->set_context($context);
$PAGE->set_url('/admin/tool/muprog/management/course_deadline_override.php', [
    'allocationid' => $allocationid,
    'courseid' => $courseid,
    'itemid' => $itemid,
]);

$top = program::load_content($program->id);
$course_item = $top->find_item($itemid);

if (!$course_item || !($course_item instanceof \tool_muprog\local\content\course)) {
    throw new \moodle_exception('invalidcourseitem', 'tool_muprog');
}

// Используем courseid из объекта курса (для надёжности)
$actual_courseid = $course_item->get_courseid();

// Получаем текущий дедлайн из нашей таблицы.
$current_deadline_record = $DB->get_record('tool_muprog_user_deadlines', [
    'userid' => $allocation->userid,
    'programid' => $program->id,
    'courseid' => $actual_courseid,
]);
$current_deadline = $current_deadline_record ? (int)$current_deadline_record->deadline : 0;

$returnurl = new url('/admin/tool/muprog/management/allocation.php', ['id' => $allocation->id]);

$form = new \tool_muprog\local\form\course_deadline_override(null, [
    'course' => $course_item,
    'allocation' => $allocation,
    'current_deadline' => $current_deadline,
]);

if ($form->is_cancelled()) {
    $form->ajax_form_cancelled($returnurl);
}

if ($data = $form->get_data()) {
    $new_deadline = !empty($data->new_deadline) ? (int)$data->new_deadline : 0;

    debugging("=== COURSE_DEADLINE_OVERRIDE ===", DEBUG_DEVELOPER);
    debugging("Курс: $actual_courseid (параметр courseid: $courseid)", DEBUG_DEVELOPER);
    debugging("Пользователь: {$allocation->userid}", DEBUG_DEVELOPER);
    debugging("Новый дедлайн: $new_deadline (" . ($new_deadline ? date('Y-m-d H:i', $new_deadline) : 'не установлен') . ")", DEBUG_DEVELOPER);

    // 1. Обновляем запись в tool_muprog_user_deadlines.
    if ($current_deadline_record) {
        $current_deadline_record->deadline = $new_deadline;
        $current_deadline_record->timemodified = time();
        $DB->update_record('tool_muprog_user_deadlines', $current_deadline_record);
        debugging("Таблица tool_muprog_user_deadlines обновлена (ID: {$current_deadline_record->id})", DEBUG_DEVELOPER);
    } else {
        $record = new \stdClass();
        $record->userid = $allocation->userid;
        $record->programid = $program->id;
        $record->courseid = $actual_courseid;
        $record->deadline = $new_deadline;
        $record->completed = 0;
        $record->overridden_start = 0;
        $record->overridden_deadline = 1;
        $record->timecreated = time();
        $record->timemodified = time();
        $DB->insert_record('tool_muprog_user_deadlines', $record);
        debugging("Таблица tool_muprog_user_deadlines: создана новая запись", DEBUG_DEVELOPER);
    }

    // 2. Обновляем ТОЛЬКО timeend во ВСЕХ записях user_enrolments (не только активных!).
    $enrol_instances = $DB->get_records('enrol', [
        'courseid' => $actual_courseid,
    ]);

    debugging("Найдено методов зачисления для курса $actual_courseid: " . count($enrol_instances), DEBUG_DEVELOPER);

    $updated = 0;
    foreach ($enrol_instances as $enrol_instance) {
        debugging("Метод зачисления ID={$enrol_instance->id}, тип={$enrol_instance->enrol}, статус={$enrol_instance->status}", DEBUG_DEVELOPER);
        
        $user_enrolment = $DB->get_record('user_enrolments', [
            'enrolid' => $enrol_instance->id,
            'userid' => $allocation->userid,
        ]);

        if (!$user_enrolment) {
            debugging("  -> Пользователь не зачислен через этот метод", DEBUG_DEVELOPER);
            continue;
        }

        $old_timeend = $user_enrolment->timeend;
        $user_enrolment->timeend = $new_deadline;
        $user_enrolment->timemodified = time();
        $DB->update_record('user_enrolments', $user_enrolment);
        $updated++;
        
        debugging("  -> Обновлено user_enrolments ID={$user_enrolment->id}: timeend {$old_timeend} -> $new_deadline", DEBUG_DEVELOPER);
    }

    // 3. Помечаем дедлайн как переопределённый вручную.
    $deadline_record = $DB->get_record('tool_muprog_user_deadlines', [
        'userid' => $allocation->userid,
        'programid' => $program->id,
        'courseid' => $actual_courseid,
    ]);
    
    if ($deadline_record) {
        $deadline_record->overridden_deadline = 1;
        $deadline_record->timemodified = time();
        $DB->update_record('tool_muprog_user_deadlines', $deadline_record);
        debugging("Флаг overridden_deadline установлен в 1", DEBUG_DEVELOPER);
    }

    debugging("Итого обновлено записей user_enrolments: $updated", DEBUG_DEVELOPER);
    debugging("=== END COURSE_DEADLINE_OVERRIDE ===", DEBUG_DEVELOPER);

    $form->ajax_form_submitted($returnurl);
}

$form->ajax_form_render();
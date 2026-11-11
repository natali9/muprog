<?php
// This file is part of MuTMS suite of plugins for Moodle™ LMS.

/**
 * Переопределение даты старта курса для конкретного пользователя.
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
$PAGE->set_url('/admin/tool/muprog/management/course_startdate_override.php', [
    'allocationid' => $allocationid,
    'courseid' => $courseid,
    'itemid' => $itemid,
]);

$top = program::load_content($program->id);
$course_item = $top->find_item($itemid);

if (!$course_item || !($course_item instanceof \tool_muprog\local\content\course)) {
    throw new \moodle_exception('invalidcourseitem', 'tool_muprog');
}

// Получаем текущую дату старта из user_enrolments (это источник истины).
$current_start = 0;
$enrol_instances = $DB->get_records('enrol', ['courseid' => $courseid, 'status' => 0]);
foreach ($enrol_instances as $enrol_instance) {
    $ue = $DB->get_record('user_enrolments', [
        'enrolid' => $enrol_instance->id,
        'userid' => $allocation->userid,
    ]);
    if ($ue && !empty($ue->timestart)) {
        $current_start = (int)$ue->timestart;
        break;
    }
}

$returnurl = new url('/admin/tool/muprog/management/allocation.php', ['id' => $allocation->id]);

$form = new \tool_muprog\local\form\course_startdate_override(null, [
    'course' => $course_item,
    'allocation' => $allocation,
    'current_start' => $current_start,
]);

if ($form->is_cancelled()) {
    $form->ajax_form_cancelled($returnurl);
}

if ($data = $form->get_data()) {
    $new_start = !empty($data->new_start) ? (int)$data->new_start : 0;

    // Обновляем ТОЛЬКО timestart во ВСЕХ записях user_enrolments.
    // Дедлайн (timeend) остаётся неизменным.
    $enrol_instances = $DB->get_records('enrol', [
        'courseid' => $courseid,
        'status' => 0,
    ]);

    $updated = 0;
    foreach ($enrol_instances as $enrol_instance) {
        $user_enrolment = $DB->get_record('user_enrolments', [
            'enrolid' => $enrol_instance->id,
            'userid' => $allocation->userid,
        ]);

        if (!$user_enrolment) {
            continue;
        }

        $user_enrolment->timestart = $new_start;
        $user_enrolment->timemodified = time();
        $DB->update_record('user_enrolments', $user_enrolment);
        $updated++;
    }

    // Помечаем дату старта как переопределённую вручную.
    $deadline_record = $DB->get_record('tool_muprog_user_deadlines', [
        'userid' => $allocation->userid,
        'programid' => $program->id,
        'courseid' => $courseid,
    ]);
    
    if ($deadline_record) {
        $deadline_record->overridden_start = 1;
        $deadline_record->timemodified = time();
        $DB->update_record('tool_muprog_user_deadlines', $deadline_record);
    }

    debugging("Дата старта курса $courseid для пользователя {$allocation->userid} изменена на " .
        ($new_start ? date('Y-m-d H:i', $new_start) : 'не установлена') .
        ". Обновлено записей зачисления: $updated. Дедлайн не изменялся.", DEBUG_DEVELOPER);

    $form->ajax_form_submitted($returnurl);
}

$form->ajax_form_render();
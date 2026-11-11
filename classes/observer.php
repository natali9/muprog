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

namespace tool_muprog;

defined('MOODLE_INTERNAL') || die();

/**
 * Event observers for tool_muprog.
 *
 * @package    tool_muprog
 * @copyright  2026 Your Name / Organization
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * 1. Срабатывает при распределении пользователя в программу.
     */
    public static function handle_allocation_created(\tool_muprog\event\allocation_created $event) {
        global $DB;

        $userid = $event->relateduserid;
        $programid = $event->other['programid'] ?? null;
        $allocationid = $event->objectid;

        if (!$userid || !$programid || !$allocationid) {
            return;
        }

        debugging("=== ALLOCATION_CREATED: user=$userid, program=$programid ===", DEBUG_DEVELOPER);

        $allocation = $DB->get_record('tool_muprog_allocation', ['id' => $allocationid]);
        if (!$allocation) {
            return;
        }

        $program_start = $allocation->timestart ?: time();
        $top = \tool_muprog\local\program::load_content($programid);

        self::process_item_schedule($userid, $programid, $top, $program_start);
        
        debugging("Индивидуальное расписание для пользователя $userid успешно создано и применено.", DEBUG_DEVELOPER);
    }

    /**
     * Рекурсивно обрабатывает элементы программы, соблюдая иерархию и порядок.
     */
    public static function process_item_schedule(int $userid, int $programid, $item, int $start_time): int {
        global $DB;

        $is_byschedule = false;

        if ($item instanceof \tool_muprog\local\content\top) {
            $record = $DB->get_record('tool_muprog_item', ['id' => $item->get_id()]);
            if ($record) {
                $seq = json_decode($record->sequencejson);
                if (isset($seq->type) && $seq->type === \tool_muprog\local\content\set::SEQUENCE_TYPE_BY_SCHEDULE) {
                    $is_byschedule = true;
                }
            }
        } else if ($item instanceof \tool_muprog\local\content\set) {
            if ($item->get_sequencetype() === \tool_muprog\local\content\set::SEQUENCE_TYPE_BY_SCHEDULE) {
                $is_byschedule = true;
            }
        }

        $current_time = $start_time;
        $children = $item->get_children();

        foreach ($children as $child) {
            if ($child->is_course()) {
                if ($is_byschedule) {
                    $current_time = self::process_course_schedule($userid, $programid, $child, $current_time);
                } else {
                    self::process_course_schedule($userid, $programid, $child, $start_time);
                }
            } else {
                $current_time = self::process_item_schedule($userid, $programid, $child, $current_time);
            }
        }

        return $current_time;
    }

    /**
     * Обрабатывает один курс: рассчитывает даты и обновляет зачисления.
     * Полностью уважает ручные переопределения и не перезаписывает их.
     */
    public static function process_course_schedule(int $userid, int $programid, $course_item, int $start_time): int {
        global $DB;

        $courseid = $course_item->get_courseid();

        $rule = $DB->get_record('tool_muprog_course_rules', [
            'programid' => $programid,
            'courseid' => $courseid
        ]);
        $duration_days = $rule ? (int)$rule->duration_days : 14;

        $course_start = $start_time;
        $course_deadline = $course_start + ($duration_days * DAYSECS);

        // === Защита от дубликатов ===
        $existing_records = $DB->get_records('tool_muprog_user_deadlines', [
            'userid' => $userid,
            'programid' => $programid,
            'courseid' => $courseid
        ], 'id DESC', '*', 0, 1);
        $existing = !empty($existing_records) ? reset($existing_records) : null;

        $update_start = true;
        $update_deadline = true;
        $skip_save = false; // Флаг: полностью пропустить сохранение в tool_muprog_user_deadlines

        if ($existing && !empty($existing->overridden_start)) {
            // Дата старта переопределена вручную — читаем её из user_enrolments
            $update_start = false;
            $enrol_instance = $DB->get_record('enrol', ['courseid' => $courseid, 'status' => 0], '*', IGNORE_MULTIPLE);
            if ($enrol_instance) {
                $ue = $DB->get_record('user_enrolments', ['enrolid' => $enrol_instance->id, 'userid' => $userid]);
                if ($ue && !empty($ue->timestart)) {
                    $course_start = (int)$ue->timestart;
                    // Пересчитываем дедлайн от ручного старта (если он тоже не переопределён)
                    if (!$existing || empty($existing->overridden_deadline)) {
                        $course_deadline = $course_start + ($duration_days * DAYSECS);
                    }
                }
            }
            debugging("Курс $courseid: дата старта переопределена вручную ({$course_start}), не изменяем timestart.", DEBUG_DEVELOPER);
        }

        if ($existing && !empty($existing->overridden_deadline)) {
            // Дедлайн переопределён вручную — сохраняем его
            $course_deadline = (int)$existing->deadline;
            $update_deadline = false;
            debugging("Курс $courseid: дедлайн переопределён вручную ({$course_deadline}), не изменяем timeend.", DEBUG_DEVELOPER);
        }

        // === КЛЮЧЕВОЕ ИСПРАВЛЕНИЕ ===
        // Если ОБА флага установлены (и старт, и дедлайн переопределены вручную),
        // мы вообще не должны перезаписывать запись в tool_muprog_user_deadlines.
        // Иначе мы потеряем ручные значения.
        if ($existing && !empty($existing->overridden_start) && !empty($existing->overridden_deadline)) {
            $skip_save = true;
            debugging("Курс $courseid: оба значения переопределены вручную, пропускаем сохранение.", DEBUG_DEVELOPER);
        }

        debugging("Курс $courseid: Старт=" . date('Y-m-d H:i', $course_start) .
                  ", Дедлайн=" . date('Y-m-d H:i', $course_deadline), DEBUG_DEVELOPER);

        // Сохраняем в tool_muprog_user_deadlines только если не оба значения переопределены
        if (!$skip_save) {
            self::save_deadline($userid, $programid, $courseid, $course_deadline);
        }

        // Обновляем user_enrolments, учитывая флаги переопределения
        self::update_enrolment_dates_respect_overrides(
            $userid, $courseid, $course_start, $course_deadline, $update_start, $update_deadline
        );

        // Следующий курс стартует на следующий день после дедлайна текущего
        // (будь то ручной или расчётный)
        return $course_deadline + DAYSECS;
    }

    /**
     * Обновляет user_enrolments, уважая ручные переопределения.
     */
    private static function update_enrolment_dates_respect_overrides(
        int $userid, int $courseid, int $timestart, int $timeend,
        bool $update_start, bool $update_deadline
    ): void {
        global $DB;

        $enrol_instances = $DB->get_records('enrol', [
            'courseid' => $courseid,
            'status' => 0
        ]);

        if (empty($enrol_instances)) {
            return;
        }

        foreach ($enrol_instances as $enrol_instance) {
            $user_enrolment = $DB->get_record('user_enrolments', [
                'enrolid' => $enrol_instance->id,
                'userid' => $userid
            ]);

            if (!$user_enrolment) {
                continue;
            }

            $changed = false;
            if ($update_start) {
                $user_enrolment->timestart = $timestart;
                $changed = true;
            }
            if ($update_deadline) {
                $user_enrolment->timeend = $timeend;
                $changed = true;
            }

            if ($changed) {
                $user_enrolment->timemodified = time();
                $DB->update_record('user_enrolments', $user_enrolment);
            }
        }
    }

    /**
     * 2. Срабатывает при завершении курса.
     */
    public static function handle_course_completed(\core\event\course_completed $event) {
        global $DB;

        $userid = $event->userid;
        $courseid = $event->courseid;

        $course_rule = $DB->get_record('tool_muprog_course_rules', ['courseid' => $courseid]);
        if (!$course_rule) {
            return;
        }

        $DB->set_field('tool_muprog_user_deadlines', 'completed', 1, [
            'userid' => $userid,
            'programid' => $course_rule->programid,
            'courseid' => $courseid
        ]);
    }

    /**
     * 3. Резервный обработчик при зачислении в курс (страховка для синхронизации с когортой).
     */
    public static function handle_user_enrolment_created(\core\event\user_enrolment_created $event) {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        debugging("[ENROL_CREATED] Start: user=$userid, course=$courseid", DEBUG_DEVELOPER);

        $course_rules = $DB->get_records('tool_muprog_course_rules', ['courseid' => $courseid]);
        if (empty($course_rules)) {
            debugging("[ENROL_CREATED] Abort: course not in any program", DEBUG_DEVELOPER);
            return;
        }

        foreach ($course_rules as $course_rule) {
            debugging("[ENROL_CREATED] Checking program: {$course_rule->programid}", DEBUG_DEVELOPER);
            
            $allocation = $DB->get_record('tool_muprog_allocation', [
                'userid' => $userid,
                'programid' => $course_rule->programid,
                'archived' => 0
            ]);

            if (!$allocation) {
                debugging("[ENROL_CREATED] Abort: no active allocation for this user/program", DEBUG_DEVELOPER);
                continue;
            }

            $deadline_records = $DB->get_records('tool_muprog_user_deadlines', [
                'userid' => $userid,
                'programid' => $course_rule->programid,
                'courseid' => $courseid
            ], 'id DESC', '*', 0, 1);
            
            $deadline_record = !empty($deadline_records) ? reset($deadline_records) : null;

            if (!$deadline_record) {
                debugging("[ENROL_CREATED] No deadline record. Triggering schedule generation...", DEBUG_DEVELOPER);
                $top = \tool_muprog\local\program::load_content($course_rule->programid);
                $program_start = $allocation->timestart ?: time();
                self::process_item_schedule($userid, $course_rule->programid, $top, $program_start);
                
                $deadline_records = $DB->get_records('tool_muprog_user_deadlines', [
                    'userid' => $userid,
                    'programid' => $course_rule->programid,
                    'courseid' => $courseid
                ], 'id DESC', '*', 0, 1);
                $deadline_record = !empty($deadline_records) ? reset($deadline_records) : null;
            }

            if (!$deadline_record || empty($deadline_record->deadline)) {
                debugging("[ENROL_CREATED] Abort: still no deadline record after generation", DEBUG_DEVELOPER);
                continue;
            }

            debugging("[ENROL_CREATED] Deadline found: {$deadline_record->deadline}. Applying to enrolments...", DEBUG_DEVELOPER);

            $duration_days = (int)$course_rule->duration_days ?: 14;
            $calculated_start = $deadline_record->deadline - ($duration_days * DAYSECS);

            $enrol_instances = $DB->get_records('enrol', ['courseid' => $courseid, 'status' => 0]);
            
            foreach ($enrol_instances as $enrol_instance) { // <-- ИСПРАВЛЕНО: здесь теперь правильная фигурная скобка {
                $ue = $DB->get_record('user_enrolments', [
                    'enrolid' => $enrol_instance->id,
                    'userid' => $userid
                ]);
                
                if (!$ue) {
                    debugging("[ENROL_CREATED] Skip: user not enrolled via this instance", DEBUG_DEVELOPER);
                    continue;
                }

                $changed = false;

                if (empty($deadline_record->overridden_deadline) && $ue->timeend != $deadline_record->deadline) {
                    $ue->timeend = $deadline_record->deadline;
                    $changed = true;
                    debugging("[ENROL_CREATED] Updating timeend to: {$ue->timeend}", DEBUG_DEVELOPER);
                }

                if (empty($deadline_record->overridden_start)) {
                    if ($ue->timestart == 0 || $ue->timestart != $calculated_start) {
                        $ue->timestart = $calculated_start;
                        $changed = true;
                        debugging("[ENROL_CREATED] Updating timestart to: {$ue->timestart}", DEBUG_DEVELOPER);
                    }
                }

                if ($changed) {
                    $ue->timemodified = time();
                    $DB->update_record('user_enrolments', $ue);
                    debugging("[ENROL_CREATED] SUCCESS: Enrolment updated!", DEBUG_DEVELOPER);
                } else {
                    debugging("[ENROL_CREATED] Skip: dates already match or overridden", DEBUG_DEVELOPER);
                }
            }
        }
    }

    /**
     * Вспомогательный метод: сохраняет или обновляет дедлайн.
     */
    private static function save_deadline(int $userid, int $programid, int $courseid, int $deadline): void {
        global $DB;

        $existing_records = $DB->get_records('tool_muprog_user_deadlines', [
            'userid' => $userid,
            'programid' => $programid,
            'courseid' => $courseid
        ], 'id DESC', '*', 0, 1);
        
        $existing = !empty($existing_records) ? reset($existing_records) : null;

        $record = new \stdClass();
        $record->userid = $userid;
        $record->programid = $programid;
        $record->courseid = $courseid;
        $record->deadline = $deadline;
        $record->completed = 0;
        $record->timemodified = time();

        if ($existing) {
            $record->id = $existing->id;
            $record->timecreated = $existing->timecreated;
            $DB->update_record('tool_muprog_user_deadlines', $record);
        } else {
            $record->timecreated = time();
            $DB->insert_record('tool_muprog_user_deadlines', $record);
        }
    }

    /**
     * Принудительно обновляет timestart и timeend во ВСЕХ записях user_enrolments.
     */
    private static function update_enrolment_dates_force(int $userid, int $courseid, int $timestart, int $timeend): void {
        global $DB;

        $enrol_instances = $DB->get_records('enrol', [
            'courseid' => $courseid,
            'status' => 0
        ]);

        if (empty($enrol_instances)) {
            return;
        }

        foreach ($enrol_instances as $enrol_instance) {
            $user_enrolment = $DB->get_record('user_enrolments', [
                'enrolid' => $enrol_instance->id,
                'userid' => $userid
            ]);

            if (!$user_enrolment) {
                continue;
            }

            $user_enrolment->timestart = $timestart;
            $user_enrolment->timeend = $timeend;
            $user_enrolment->timemodified = time();
            $DB->update_record('user_enrolments', $user_enrolment);
        }
    }
}
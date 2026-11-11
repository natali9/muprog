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
     * Рассчитывает жесткое расписание для курсов.
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

        // Базовая дата начала программы
        $program_start = $allocation->timestart ?: time();

        // Загружаем иерархическую структуру программы (гарантирует правильный порядок sortorder)
        $top = \tool_muprog\local\program::load_content($programid);

        // Запускаем рекурсивную обработку расписания для индивидуальных курсов
        self::process_item_schedule($userid, $programid, $top, $program_start);
        
        debugging("Индивидуальное расписание для пользователя $userid успешно создано.", DEBUG_DEVELOPER);
    }

    /**
     * Рекурсивно обрабатывает элементы программы, соблюдая иерархию и порядок.
     */
    private static function process_item_schedule(int $userid, int $programid, $item, int $start_time): int {
        global $DB;

        $is_byschedule = false;

        // Проверяем, является ли текущий элемент (top или set) расписанием
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
        $children = $item->get_children(); // Дети уже отсортированы по sortorder

        foreach ($children as $child) {
            if ($child->is_course()) {
                if ($is_byschedule) {
                    // Последовательное расписание: передаем current_time и получаем время старта следующего
                    $current_time = self::process_course_schedule($userid, $programid, $child, $current_time);
                } else {
                    // Параллельное расписание: все курсы стартуют одновременно от start_time
                    self::process_course_schedule($userid, $programid, $child, $start_time);
                }
            } else {
                // Рекурсивная обработка вложенных наборов
                $current_time = self::process_item_schedule($userid, $programid, $child, $current_time);
            }
        }

        return $current_time;
    }

    /**
     * Обрабатывает один курс: рассчитывает даты и обновляет зачисления, уважая ручные переопределения.
     * Возвращает время старта следующего курса (дедлайн + 1 день).
     */
    private static function process_course_schedule(int $userid, int $programid, $course_item, int $start_time): int {
        global $DB;

        $courseid = $course_item->get_courseid();

        $rule = $DB->get_record('tool_muprog_course_rules', [
            'programid' => $programid,
            'courseid' => $courseid
        ]);
        $duration_days = $rule ? (int)$rule->duration_days : 14;

        $course_start = $start_time;
        $course_deadline = $course_start + ($duration_days * DAYSECS);

        // Проверяем, есть ли ручные переопределения для этого пользователя и курса.
        $existing = $DB->get_record('tool_muprog_user_deadlines', [
            'userid' => $userid,
            'programid' => $programid,
            'courseid' => $courseid
        ]);

        $update_start = true;
        $update_deadline = true;

        if ($existing && !empty($existing->overridden_start)) {
            // Дата старта была изменена вручную. Мы НЕ будем обновлять её в БД.
            $update_start = false;
            
            // Для корректного логирования читаем реальное значение из user_enrolments
            $enrol_instance = $DB->get_record('enrol', ['courseid' => $courseid, 'status' => 0], '*', IGNORE_MULTIPLE);
            if ($enrol_instance) {
                $ue = $DB->get_record('user_enrolments', ['enrolid' => $enrol_instance->id, 'userid' => $userid]);
                if ($ue && !empty($ue->timestart)) {
                    $course_start = (int)$ue->timestart;
                }
            }
            debugging("Курс $courseid: дата старта переопределена вручную ({$course_start}), не изменяем timestart.", DEBUG_DEVELOPER);
        }

        if ($existing && !empty($existing->overridden_deadline)) {
            // Дедлайн был изменён вручную — сохраняем его и не обновляем в БД.
            $course_deadline = (int)$existing->deadline;
            $update_deadline = false;
            debugging("Курс $courseid: дедлайн переопределён вручную ({$course_deadline}), не изменяем timeend.", DEBUG_DEVELOPER);
        }

        debugging("Курс $courseid: Старт=" . date('Y-m-d H:i', $course_start) .
                  ", Дедлайн=" . date('Y-m-d H:i', $course_deadline), DEBUG_DEVELOPER);

        self::save_deadline($userid, $programid, $courseid, $course_deadline);

        // Обновляем user_enrolments, учитывая флаги переопределения.
        self::update_enrolment_dates_respect_overrides(
            $userid, $courseid, $course_start, $course_deadline, $update_start, $update_deadline
        );

        // Следующий курс стартует на следующий день после дедлайна текущего
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
     * Отмечает курс как завершенный в нашей таблице.
     */
    public static function handle_course_completed(\core\event\course_completed $event) {
        global $DB;

        $userid = $event->userid;
        $courseid = $event->courseid;

        debugging("=== COURSE_COMPLETED: user=$userid, course=$courseid ===", DEBUG_DEVELOPER);

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
     * 3. Резервный обработчик при зачислении в курс.
     * Применяет только дедлайн (timeend), дата старта не пересчитывается.
     */
        /**
     * 3. Резервный обработчик при зачислении в курс (страховка для асинхронных зачислений).
     * Применяет и timeend, и timestart, если они не были переопределены вручную.
     */
    public static function handle_user_enrolment_created(\core\event\user_enrolment_created $event) {
        global $DB;

        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        $course_rule = $DB->get_record('tool_muprog_course_rules', ['courseid' => $courseid]);
        if (!$course_rule) {
            return;
        }

        $deadline_record = $DB->get_record('tool_muprog_user_deadlines', [
            'userid' => $userid,
            'programid' => $course_rule->programid,
            'courseid' => $courseid
        ]);

        // Если для этого пользователя и курса еще нет расписания, ничего не делаем
        if (!$deadline_record) {
            return;
        }

        // Рассчитываем правильный старт на основе дедлайна и длительности курса
        $duration_days = (int)$course_rule->duration_days ?: 14;
        $calculated_start = $deadline_record->deadline - ($duration_days * DAYSECS);

        $enrol_instances = $DB->get_records('enrol', ['courseid' => $courseid, 'status' => 0]);
        
        foreach ($enrol_instances as $enrol_instance) {
            $ue = $DB->get_record('user_enrolments', [
                'enrolid' => $enrol_instance->id,
                'userid' => $userid
            ]);
            
            if (!$ue) {
                continue;
            }

            $changed = false;

            // 1. Обновляем timeend, если он НЕ переопределен вручную и отличается от расчетного
            if (empty($deadline_record->overridden_deadline) && $ue->timeend != $deadline_record->deadline) {
                $ue->timeend = $deadline_record->deadline;
                $changed = true;
            }

            // 2. Обновляем timestart, если он НЕ переопределен вручную
            // Применяем, если он еще не задан (0) или отличается от расчетного
            if (empty($deadline_record->overridden_start)) {
                if ($ue->timestart == 0 || $ue->timestart != $calculated_start) {
                    $ue->timestart = $calculated_start;
                    $changed = true;
                }
            }

            if ($changed) {
                $ue->timemodified = time();
                $DB->update_record('user_enrolments', $ue);
                
                debugging("Обновлены даты зачисления (страховка) для курса $courseid, пользователь $userid. " .
                          "Start: " . ($ue->timestart ? date('Y-m-d', $ue->timestart) : '0') . 
                          ", End: " . ($ue->timeend ? date('Y-m-d', $ue->timeend) : '0'), DEBUG_DEVELOPER);
            }
        }
    }

    /**
     * Вспомогательный метод: сохраняет или обновляет дедлайн в таблице tool_muprog_user_deadlines.
     */
    private static function save_deadline(int $userid, int $programid, int $courseid, int $deadline): void {
        global $DB;

        $existing = $DB->get_record('tool_muprog_user_deadlines', [
            'userid' => $userid,
            'programid' => $programid,
            'courseid' => $courseid
        ]);

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
     * (Резервный метод, используется при необходимости полного сброса расписания).
     */
    private static function update_enrolment_dates_force(int $userid, int $courseid, int $timestart, int $timeend): void {
        global $DB;

        $enrol_instances = $DB->get_records('enrol', [
            'courseid' => $courseid,
            'status' => 0
        ]);

        if (empty($enrol_instances)) {
            debugging("Методы зачисления не найдены для курса $courseid", DEBUG_DEVELOPER);
            return;
        }

        $updated_count = 0;
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
            $updated_count++;
        }

        debugging("Обновлено $updated_count записей user_enrolments для курса $courseid, пользователя $userid", DEBUG_DEVELOPER);
    }
}
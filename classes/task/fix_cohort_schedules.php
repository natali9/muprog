<?php
// This file is part of MuTMS suite of plugins for Moodle™ LMS.

namespace tool_muprog\task;

defined('MOODLE_INTERNAL') || die();

/**
 * Задача для автоматического пересчёта расписания курсов.
 * Решает проблему с синхронизацией через когорты.
 *
 * @package    tool_muprog
 * @copyright  2026 Your Name
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fix_cohort_schedules extends \core\task\scheduled_task {

    /**
     * Возвращает имя задачи (отображается в интерфейсе Moodle).
     */
    public function get_name() {
        return get_string('task_fix_cohort_schedules', 'tool_muprog');
    }

    /**
     * Выполняет задачу.
     */
    public function execute() {
        global $DB;

        mtrace("Starting MuTMS schedule fix task...");

        // Находим все активные распределения
        $sql = "SELECT a.*, p.fullname as program_name
                  FROM {tool_muprog_allocation} a
                  JOIN {tool_muprog_program} p ON p.id = a.programid
                 WHERE a.archived = 0 AND p.archived = 0";

        $allocations = $DB->get_records_sql($sql);

        if (empty($allocations)) {
            mtrace("No active allocations found.");
            return;
        }

        $fixed_count = 0;
        $skipped_count = 0;

        foreach ($allocations as $allocation) {
            $top = \tool_muprog\local\program::load_content($allocation->programid);
            
            // Проверяем, есть ли в программе тип byschedule
            $has_byschedule = false;
            $top_record = $DB->get_record('tool_muprog_item', ['id' => $top->get_id()]);
            if ($top_record) {
                $seq = json_decode($top_record->sequencejson);
                if (isset($seq->type) && $seq->type === \tool_muprog\local\content\set::SEQUENCE_TYPE_BY_SCHEDULE) {
                    $has_byschedule = true;
                }
            }

            if (!$has_byschedule) {
                $skipped_count++;
                continue;
            }

            // Запускаем пересчёт расписания
            $program_start = $allocation->timestart ?: time();
            \tool_muprog\observer::process_item_schedule(
                $allocation->userid, 
                $allocation->programid, 
                $top, 
                $program_start
            );
            
            $fixed_count++;
        }

        mtrace("Done! Fixed: $fixed_count, Skipped: $skipped_count");
    }
}
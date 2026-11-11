<?php
namespace tool_muprog;

defined('MOODLE_INTERNAL') || die();

/**
 * Менеджер дедлайнов для программ обучения.
 *
 * @package    tool_muprog
 * @copyright  2026 Your Name
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deadline_manager {
    
    /**
     * Сохраняет или обновляет правило срока для курса в программе (Ваш код)
     */
    public static function save_course_rule(int $programid, int $courseid, int $duration_days, string $offset_type, int $sortorder, int $block_on_deadline = 1): void {
        global $DB;

        $existing = $DB->get_record('tool_muprog_course_rules', [
            'programid' => $programid,
            'courseid' => $courseid
        ]);

        $record = new \stdClass();
        $record->programid = $programid;
        $record->courseid = $courseid;
        $record->duration_days = $duration_days;
        $record->offset_type = $offset_type;
        $record->sortorder = $sortorder;
        $record->block_on_deadline = $block_on_deadline;

        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('tool_muprog_course_rules', $record);
        } else {
            $DB->insert_record('tool_muprog_course_rules', $record);
        }
    }

    /**
     * Создает начальные дедлайны для пользователя при зачислении в программу
     */
    public static function create_initial_deadlines(int $userid, int $programid): void {
        global $DB;

        $rules = $DB->get_records('tool_muprog_course_rules', ['programid' => $programid], 'sortorder ASC');
        $now = time();

        foreach ($rules as $rule) {
            // Если отсчет от начала программы или это самый первый курс
            if ($rule->offset_type === 'program_start' || $rule->sortorder == 0) {
                $deadline_time = $now + ($rule->duration_days * DAYSECS);
                
                $existing = $DB->get_record('tool_muprog_user_deadlines', [
                    'userid' => $userid, 'programid' => $programid, 'courseid' => $rule->courseid
                ]);

                $record = new \stdClass();
                $record->userid = $userid;
                $record->programid = $programid;
                $record->courseid = $rule->courseid;
                $record->deadline = $deadline_time;
                $record->completed = 0;
                $record->timemodified = $now;

                if ($existing) {
                    $record->id = $existing->id;
                    $record->timecreated = $existing->timecreated;
                    $DB->update_record('tool_muprog_user_deadlines', $record);
                } else {
                    $record->timecreated = $now;
                    $DB->insert_record('tool_muprog_user_deadlines', $record);
                }
                // === ДОБАВЛЯЕМ ЭТУ СТРОКУ ===
                self::sync_enrolment_end_date($userid, $rule->courseid, $deadline_time, (bool)$rule->block_on_deadline);
                // 
            }
        }
    }

    /**
     * Рассчитывает дедлайн для следующего курса при завершении текущего
     */
    public static function handle_course_completion(int $userid, int $programid, int $courseid): void {
        global $DB;

        // Отмечаем текущий курс как завершенный
        $DB->set_field('tool_muprog_user_deadlines', 'completed', 1, [
            'userid' => $userid, 'programid' => $programid, 'courseid' => $courseid
        ]);

        // Находим правило текущего курса
        $current_rule = $DB->get_record('tool_muprog_course_rules', ['programid' => $programid, 'courseid' => $courseid]);
        if (!$current_rule) return;

        // Находим следующий курс в программе
        $next_rule = $DB->get_record_select('tool_muprog_course_rules',
            'programid = :programid AND sortorder > :sortorder',
            ['programid' => $programid, 'sortorder' => $current_rule->sortorder],
            '*',
            IGNORE_MULTIPLE
        );

        if ($next_rule && $next_rule->offset_type === 'prev_course_complete') {
            $new_deadline = time() + ($next_rule->duration_days * DAYSECS);

            $existing = $DB->get_record('tool_muprog_user_deadlines', [
                'userid' => $userid, 'programid' => $programid, 'courseid' => $next_rule->courseid
            ]);

            $record = new \stdClass();
            $record->userid = $userid;
            $record->programid = $programid;
            $record->courseid = $next_rule->courseid;
            $record->deadline = $new_deadline;
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
            // === ДОБАВЛЯЕМ ЭТУ СТРОКУ ===
            self::sync_enrolment_end_date($userid, $next_rule->courseid, $new_deadline, (bool)$next_rule->block_on_deadline);
            // ============================
        }
    }

    /**
     * Проверка доступности курса (используется в условии доступа)
     */
    public static function is_course_available(int $userid, int $courseid): bool {
        global $DB;

        $deadline_record = $DB->get_record('tool_muprog_user_deadlines', [
            'userid' => $userid,
            'courseid' => $courseid
        ]);

        // Если дедлайн не установлен, доступ открыт
        if (!$deadline_record) {
            return true;
        }

        // Если курс уже завершен, доступ всегда открыт
        if ($deadline_record->completed == 1) {
            return true;
        }

        // Если срок истек
        if (time() > $deadline_record->deadline) {
            $rule = $DB->get_record('tool_muprog_course_rules', [
                'programid' => $deadline_record->programid,
                'courseid' => $courseid
            ]);

            // Если флаг блокировки равен 1, доступ закрыт
            if ($rule && $rule->block_on_deadline == 1) {
                return false;
            }
        }

        return true;
    }

        /**
     * Синхронизирует наш дедлайн с нативным сроком окончания зачисления Moodle.
     * Это обновляет поле "Срок действия зачисления" в таблице "Зачисленные пользователи".
     */
        public static function sync_enrolment_end_date(int $userid, int $courseid, int $deadline_timestamp, bool $block_on_deadline): void {
        global $DB;

        // ОТЛАДКА: Логируем вызов
        debugging("=== sync_enrolment_end_date вызван ===", DEBUG_DEVELOPER);
        debugging("UserID: $userid, CourseID: $courseid, Deadline: " . date('Y-m-d H:i', $deadline_timestamp), DEBUG_DEVELOPER);

        $sql = "SELECT ue.id, ue.timeend, e.id AS enrolid
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                 WHERE ue.userid = :userid 
                   AND e.courseid = :courseid 
                   AND ue.status = :active";
        
        $enrolments = $DB->get_records_sql($sql, [
            'userid' => $userid,
            'courseid' => $courseid,
            'active' => ENROL_USER_ACTIVE
        ]);

        // ОТЛАДКА: Логируем количество найденных зачислений
        debugging("Найдено зачислений: " . count($enrolments), DEBUG_DEVELOPER);

        if (empty($enrolments)) {
            debugging("ВНИМАНИЕ: зачислений не найдено! Синхронизация невозможна.", DEBUG_DEVELOPER);
            return;
        }

        foreach ($enrolments as $enrol) {
            $record = new \stdClass();
            $record->id = $enrol->id;
            $record->timeend = $deadline_timestamp;
            $record->timemodified = time();
            
            $DB->update_record('user_enrolments', $record);
            debugging("Обновлена запись user_enrolments ID: " . $enrol->id, DEBUG_DEVELOPER);
        }
    }
}
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
// phpcs:disable moodle.Files.LineLength.TooLong

namespace tool_muprog\local\content;

/**
 * Program course item.
 *
 * @package    tool_muprog
 * @copyright  2022 Open LMS (https://www.openlms.net/)
 * @copyright  2025 Petr Skoda
 * @author     Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course extends item {

    /** @var int|null Срок прохождения в днях */
    public ?int $duration_days = null;

    #[\Override]
    public static function get_type(): string {
        return 'course';
    }

    #[\Override]
    public static function get_type_name(): string {
        return get_string('course');
    }

    /**
     * Get course id.
     *
     * @return int
     */
    public function get_courseid(): int {
        return $this->courseid;
    }

    /**
     * Get duration in days.
     *
     * @return int
     */
    public function get_duration_days(): int {
        return $this->duration_days ?? 14; // Возвращаем 14 дней по умолчанию, если значение не задано
    }

    #[\Override]
    protected static function init_from_record(\stdClass $record, ?item $previous, array &$unusedrecords, array &$prerequisites): item {
        if ($record->type !== 'course' || $record->topitem || !$record->courseid || $record->creditframeworkid !== null) {
            throw new \core\exception\coding_exception('Invalid course item');
        }
        
        /** @var course $item */
        $item = parent::init_from_record($record, $previous, $unusedrecords, $prerequisites);

        // === ЗАГРУЖАЕМ НАШУ ДЛИТЕЛЬНОСТЬ ИЗ ТАБЛИЦЫ ПРАВИЛ ===
        global $DB;
        $rule = $DB->get_record('tool_muprog_course_rules', [
            'programid' => $record->programid,
            'courseid' => $record->courseid
        ]);
        
        $item->duration_days = $rule ? (int)$rule->duration_days : 14;
        // ====================================================

        return $item;
    }

    #[\Override]
    protected function get_record(): array {
        global $DB;

        $record = parent::get_record();

        // Получаем актуальное полное имя курса из таблицы course
        $fullname = $DB->get_field('course', 'fullname', ['id' => $this->courseid]);
        if ($fullname !== false) {
            $record['fullname'] = $fullname;
        }

        return $record;
    }
}
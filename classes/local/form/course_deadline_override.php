<?php
// This file is part of MuTMS suite of plugins for Moodle™ LMS.

namespace tool_muprog\local\form;

defined('MOODLE_INTERNAL') || die();

/**
 * Форма переопределения дедлайна курса для конкретного пользователя.
 *
 * @package    tool_muprog
 * @copyright  2026 Your Name
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_deadline_override extends \tool_mulib\local\ajax_form {

    #[\Override]
    protected function definition() {
        $mform = $this->_form;
        $course = $this->_customdata['course'];
        $allocation = $this->_customdata['allocation'];
        $current_deadline = $this->_customdata['current_deadline'];

        // Заголовок с названием курса
        $mform->addElement('static', 'course_name', get_string('course'), format_string($course->get_fullname()));
        
        // Текущий дедлайн (только для информации)
        $current_deadline_str = $current_deadline ? userdate($current_deadline, get_string('strftimedate')) : get_string('notset', 'tool_muprog');
        $mform->addElement('static', 'current_deadline', get_string('current_deadline', 'tool_muprog'), $current_deadline_str);

        // Новое поле: дата дедлайна
        $mform->addElement('date_time_selector', 'new_deadline', get_string('new_deadline', 'tool_muprog'), ['optional' => true]);
        $mform->addHelpButton('new_deadline', 'new_deadline', 'tool_muprog');
        
        // Если есть текущий дедлайн, подставляем его как значение по умолчанию
        if ($current_deadline) {
            $mform->setDefault('new_deadline', $current_deadline);
        }

        // Скрытые поля
        $mform->addElement('hidden', 'allocationid', $allocation->id);
        $mform->setType('allocationid', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $course->get_courseid());
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'itemid', $course->get_id());
        $mform->setType('itemid', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Валидация: новый дедлайн должен быть в будущем (или хотя бы не в далеком прошлом)
        if (!empty($data['new_deadline']) && $data['new_deadline'] < time() - DAYSECS) {
            // Предупреждение, но не ошибка — администратор может хотеть установить прошедшую дату
            // Можно добавить предупреждение через JavaScript, но не блокировать сохранение
        }

        return $errors;
    }
}
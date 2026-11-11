<?php
// This file is part of MuTMS suite of plugins for Moodle™ LMS.

namespace tool_muprog\local\form;

defined('MOODLE_INTERNAL') || die();

/**
 * Форма переопределения даты старта курса для конкретного пользователя.
 *
 * @package    tool_muprog
 * @copyright  2026 Your Name
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_startdate_override extends \tool_mulib\local\ajax_form {

    #[\Override]
    protected function definition() {
        $mform = $this->_form;
        $course = $this->_customdata['course'];
        $allocation = $this->_customdata['allocation'];
        $current_start = $this->_customdata['current_start'];

        $mform->addElement('static', 'course_name', get_string('course'), format_string($course->get_fullname()));

        $current_start_str = $current_start ? userdate($current_start, get_string('strftimedate')) : get_string('notset', 'tool_muprog');
        $mform->addElement('static', 'current_start', get_string('current_startdate', 'tool_muprog'), $current_start_str);

        $mform->addElement('date_time_selector', 'new_start', get_string('new_startdate', 'tool_muprog'), ['optional' => true]);
        $mform->addHelpButton('new_start', 'new_startdate', 'tool_muprog');

        if ($current_start) {
            $mform->setDefault('new_start', $current_start);
        }

        $mform->addElement('hidden', 'allocationid', $allocation->id);
        $mform->setType('allocationid', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $course->get_courseid());
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'itemid', $course->get_id());
        $mform->setType('itemid', PARAM_INT);

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
<?php

namespace Test\Unit;

use Api\Database\SectionRepository;
use Phake;
use PHPUnit_Framework_TestCase;

class SectionRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testFindAllCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllData());

        $sectionRepository = new SectionRepository($databaseConnection);

        $this->assertSame($this->buildFindAllSection(), $sectionRepository->findAll());
    }

    public function testFindAllBySemesterCallsQueryOnDatabaseConnection() {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Semester.code LIKE :semester
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllData());

        $sectionRepository = new SectionRepository($databaseConnection);

        $semester = 'FS';
        $this->assertSame($this->buildFindAllSection(),
            $sectionRepository->findAllBySemester($semester));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind('semester', "%$semester%"),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindAllBySubjectCallsQueryOnDatabaseConnection() {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Subject.code LIKE :subject
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllData());

        $sectionRepository = new SectionRepository($databaseConnection);

        $subject = '410';
        $this->assertSame($this->buildFindAllSection(),
            $sectionRepository->findAllBySubject($subject));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind('subject', "%$subject%"),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneBySemesterAndSubjectCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT Subject.code AS subject_code, Subject.name AS subject_name,
	                Semester.code AS semester_code, Semester.name AS semester_name,
	                Section.section_number,
	                SectionTime.day_name, SectionTime.start_time, SectionTime.end_time
                  FROM treadstone_section Section, treadstone_subject Subject,
	                treadstone_semester Semester, treadstone_section_time SectionTime,
	                treadstone_day Day
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND SectionTime.section_id = Section.id
                  AND Semester.code LIKE :semester
                  AND Subject.code LIKE :subject
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $sectionRepository = new SectionRepository($databaseConnection);

        $semester = 'FS';
        $subject = '410';
        $this->assertSame($this->buildFindOneSection(),
            $sectionRepository->findOneBySemesterAndSubject($semester, $subject));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind('semester', "%$semester%"),
            Phake::verify($databaseConnection)->bind('subject', "%$subject%"),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneData() {
        $data[] = array('subject_code' => 'CSE 410', 'subject_name' => 'Operating Systems',
            'semester_code' => 'FS15', 'semester_name' => 'Fall Semester 2015', 'section_number' => 2,
            'day_name' => 'Monday', 'start_time' => '15:00:00', 'end_time' => '16:20:00');
        $data[] = array('subject_code' => 'CSE 410', 'subject_name' => 'Operating Systems',
            'semester_code' => 'FS15', 'semester_name' => 'Fall Semester 2015', 'section_number' => 2,
            'day_name' => 'Wednesday', 'start_time' => '15:00:00', 'end_time' => '16:20:00');
        return $data;
    }

    private function buildFindAllData() {
        $data = $this->buildFindOneData();
        $data[] = array('subject_code' => 'IAH 241A', 'subject_name' => 'Music and Society in the Modern World',
            'semester_code' => 'SS16', 'semester_name' => 'Spring Semester 2016', 'section_number' => 2,
            'day_name' => 'Tuesday', 'start_time' => '12:40:00', 'end_time' => '14:30:00');
        $data[] = array('subject_code' => 'IAH 241A', 'subject_name' => 'Music and Society in the Modern World',
            'semester_code' => 'SS16', 'semester_name' => 'Spring Semester 2016', 'section_number' => 2,
            'day_name' => 'Thursday', 'start_time' => '12:40:00', 'end_time' => '14:30:00');
        return $data;
    }

    private function buildFindOneSection() {
        $section = array(
            'semester' => array('code' => 'FS15', 'name' => 'Fall Semester 2015'),
            'subject' => array('code' => 'CSE 410', 'name' => 'Operating Systems'),
            'section_number' => 2,
            'times' => array(
                array('day' => 'Monday', 'start_time' => '15:00:00', 'end_time' => '16:20:00'),
                array('day' => 'Wednesday', 'start_time' => '15:00:00', 'end_time' => '16:20:00')
            )
        );
        return $section;
    }

    private function buildFindAllSection() {
        $sections[] = $this->buildFindOneSection();
        $sections[] = array(
            'semester' => array('code' => 'SS16', 'name' => 'Spring Semester 2016'),
            'subject' => array('code' => 'IAH 241A', 'name' => 'Music and Society in the Modern World'),
            'section_number' => 2,
            'times' => array(
                array('day' => 'Tuesday', 'start_time' => '12:40:00', 'end_time' => '14:30:00'),
                array('day' => 'Thursday', 'start_time' => '12:40:00', 'end_time' => '14:30:00')
            )
        );
        return $sections;
    }
}

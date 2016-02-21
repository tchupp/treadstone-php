<?php

namespace Test\Unit;

use Api\Database\DatabaseConnection;
use Api\Database\SectionRepository;
use Phake;
use PHPUnit_Framework_TestCase;

class SectionRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testAutowire() {
        $sectionRepository = SectionRepository::autowire();

        $this->assertAttributeInstanceOf(DatabaseConnection::class, 'databaseConnection', $sectionRepository);
    }

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

    public function testFindAllBySemesterAndSubjectCallsQueryOnDatabaseConnectionWithCorrectQuery() {
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
            ->thenReturn($this->buildFindAllData());

        $sectionRepository = new SectionRepository($databaseConnection);

        $semester = 'FS';
        $subject = '410';
        $this->assertSame($this->buildFindAllSection(),
            $sectionRepository->findAllBySemesterAndSubject($semester, $subject));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind('semester', "%$semester%"),
            Phake::verify($databaseConnection)->bind('subject', "%$subject%"),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneBySectionNumberAndSemesterAndSubject() {
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
                  AND Semester.code = :semester
                  AND Subject.code = :subject
                  AND Section.section_number = :number
                  AND Day.name = SectionTime.day_name
                  ORDER BY Subject.code, Day.number";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $sectionRepository = new SectionRepository($databaseConnection);

        $semester = 'FS15';
        $subject = 'CSE 410';
        $number = 2;
        $this->assertSame($this->buildFindOneSection(),
            $sectionRepository->findOneBySectionNumberAndSemesterAndSubject($semester, $subject, $number));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind('semester', $semester),
            Phake::verify($databaseConnection)->bind('subject', $subject),
            Phake::verify($databaseConnection)->bind('number', $number),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneData() {
        $data[] = ['subject_code'  => 'CSE 410', 'subject_name' => 'Operating Systems',
                   'semester_code' => 'FS15', 'semester_name' => 'Fall Semester 2015', 'section_number' => 2,
                   'day_name'      => 'Monday', 'start_time' => '15:00:00', 'end_time' => '16:20:00'];
        $data[] = ['subject_code'  => 'CSE 410', 'subject_name' => 'Operating Systems',
                   'semester_code' => 'FS15', 'semester_name' => 'Fall Semester 2015', 'section_number' => 2,
                   'day_name'      => 'Wednesday', 'start_time' => '15:00:00', 'end_time' => '16:20:00'];
        return $data;
    }

    private function buildFindAllData() {
        $data = $this->buildFindOneData();
        $data[] = ['subject_code'  => 'IAH 241A', 'subject_name' => 'Music and Society in the Modern World',
                   'semester_code' => 'SS16', 'semester_name' => 'Spring Semester 2016', 'section_number' => 2,
                   'day_name'      => 'Tuesday', 'start_time' => '12:40:00', 'end_time' => '14:30:00'];
        $data[] = ['subject_code'  => 'IAH 241A', 'subject_name' => 'Music and Society in the Modern World',
                   'semester_code' => 'SS16', 'semester_name' => 'Spring Semester 2016', 'section_number' => 2,
                   'day_name'      => 'Thursday', 'start_time' => '12:40:00', 'end_time' => '14:30:00'];
        return $data;
    }

    private function buildFindOneSection() {
        $section = [
            'semester'      => ['code' => 'FS15', 'name' => 'Fall Semester 2015'],
            'subject'       => ['code' => 'CSE 410', 'name' => 'Operating Systems'],
            'sectionNumber' => 2,
            'times'         => [
                ['day' => 'Monday', 'startTime' => '15:00:00', 'endTime' => '16:20:00'],
                ['day' => 'Wednesday', 'startTime' => '15:00:00', 'endTime' => '16:20:00']
            ]
        ];
        return $section;
    }

    private function buildFindAllSection() {
        $sections[] = $this->buildFindOneSection();
        $sections[] = [
            'semester'      => ['code' => 'SS16', 'name' => 'Spring Semester 2016'],
            'subject'       => ['code' => 'IAH 241A', 'name' => 'Music and Society in the Modern World'],
            'sectionNumber' => 2,
            'times'         => [
                ['day' => 'Tuesday', 'startTime' => '12:40:00', 'endTime' => '14:30:00'],
                ['day' => 'Thursday', 'startTime' => '12:40:00', 'endTime' => '14:30:00']
            ]
        ];
        return $sections;
    }
}

<?php

namespace Test\Unit;

use Api\Database\DatabaseConnection;
use Api\Database\SubjectRepository;
use Phake;
use PHPUnit_Framework_TestCase;

class SubjectRepositoryTest extends PHPUnit_Framework_TestCase {

    public function testAutowire() {
        $subjectRepository = SubjectRepository::autowire();

        $this->assertAttributeInstanceOf(DatabaseConnection::class, 'databaseConnection', $subjectRepository);
    }

    public function testSaveCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "INSERT
                  INTO treadstone_subject(code, name)
                  VALUES(:code, :name)";
        $subject = ['code' => 'ECE 410', 'name' => 'VSLI Design'];

        $rowsModified = 1;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($rowsModified);

        $subjectRepository = new SubjectRepository($databaseConnection);

        $this->assertEquals($rowsModified, $subjectRepository->save($subject));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bindMore($subject),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testSaveDoesNotCallQueryIfAnyParametersAreMissing() {
        $codeMissing = ['name' => 'VSLI Design'];
        $nameMissing = ['code' => 'ECE 410'];
        $bothMissing = [];

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');

        $subjectRepository = new SubjectRepository($databaseConnection);

        $this->assertEquals(0, $subjectRepository->save($codeMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $subjectRepository->save($nameMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $subjectRepository->save($bothMissing));
        Phake::verifyNoInteraction($databaseConnection);
    }

    public function testFindAllCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT *
                  FROM treadstone_subject";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllData());

        $subjectRepository = new SubjectRepository($databaseConnection);

        $this->assertSame($this->buildFindAllSubjects(), $subjectRepository->findAll());
    }

    public function testFindAllBySemester() {
        $query = "SELECT Subject.code AS subjectCode, Subject.name AS subjectName,
                    Semester.code AS semesterCode, Semester.name AS semesterName
                  FROM treadstone_subject Subject, treadstone_semester Semester, treadstone_section Section
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND Semester.code = :semester
                  ORDER BY Subject.code";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllSubjectsBySemesterData());

        $subjectRepository = new SubjectRepository($databaseConnection);

        $semesterKey = 'semester';
        $semesterValue = 'SS16';
        $this->assertSame($this->buildFindAllSubjectsBySemester(),
            $subjectRepository->findAllBySemester($semesterValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($semesterKey, $semesterValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testFindOneBySemesterAndSubject() {
        $query = "SELECT Subject.code AS subjectCode, Subject.name AS subjectName,
                    Semester.code AS semesterCode, Semester.name AS semesterName
                  FROM treadstone_subject Subject, treadstone_semester Semester, treadstone_section Section
                  WHERE Section.subject_id = Subject.id
                  AND Section.semester_id = Semester.id
                  AND Semester.code = :semester
                  AND Subject.code = :subject
                  ORDER BY Subject.code";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneWithSemesterData());

        $subjectRepository = new SubjectRepository($databaseConnection);

        $semesterKey = 'semester';
        $subjectKey = 'subject';
        $semesterValue = 'SS16';
        $subjectValue = 'ECE 410';
        $this->assertSame($this->buildFindOneSubjectWithSemester(),
            $subjectRepository->findOneBySemesterAndSubject($semesterValue, $subjectValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($semesterKey, $semesterValue),
            Phake::verify($databaseConnection)->bind($subjectKey, $subjectValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneWithSemesterData() {
        $data[] = ['subjectCode'  => 'ECE 410', 'subjectName' => 'VSLI Design',
                   'semesterCode' => 'SS16', 'semesterName' => 'Spring Semester 2016'];
        return $data;
    }

    private function buildFindAllData() {
        $data[] = ['code' => 'ECE 410', 'name' => 'VSLI Design'];
        $data[] = ['code' => 'CSE 410', 'name' => 'Operating Systems'];
        return $data;
    }

    private function buildFindAllSubjectsBySemesterData() {
        $data[] = ['subjectCode'  => 'ECE 410', 'subjectName' => 'VSLI Design',
                   'semesterCode' => 'SS16', 'semesterName' => 'Spring Semester 2016'];
        $data[] = ['subjectCode'  => 'ECE 480', 'subjectName' => 'Senior Design',
                   'semesterCode' => 'SS16', 'semesterName' => 'Spring Semester 2016'];
        return $data;
    }

    private function buildFindOneSubjectWithSemester() {
        $subject = ['subjectCode'  => 'ECE 410', 'subjectName' => 'VSLI Design',
                    'semesterCode' => 'SS16', 'semesterName' => 'Spring Semester 2016'];
        return $subject;
    }

    private function buildFindAllSubjects() {
        $subjects['ECE 410'] = ['code' => 'ECE 410', 'name' => 'VSLI Design'];
        $subjects['CSE 410'] = ['code' => 'CSE 410', 'name' => 'Operating Systems'];
        return $subjects;
    }

    private function buildFindAllSubjectsBySemester() {
        $data[] = ['subjectCode'  => 'ECE 410', 'subjectName' => 'VSLI Design',
                   'semesterCode' => 'SS16', 'semesterName' => 'Spring Semester 2016'];
        $data[] = ['subjectCode'  => 'ECE 480', 'subjectName' => 'Senior Design',
                   'semesterCode' => 'SS16', 'semesterName' => 'Spring Semester 2016'];
        return $data;
    }
}

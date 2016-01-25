<?php

namespace Test\Unit;

use Api\Database\DatabaseConnection;
use Api\Database\SemesterRepository;
use Phake;
use Test\TreadstoneTestCase;

class SemesterRepositoryTest extends TreadstoneTestCase {

    public function testAutowire() {
        $semesterRepository = SemesterRepository::autowire();

        $databaseConnection = $this->getPrivateProperty($semesterRepository, 'databaseConnection');

        $this->assertEquals(DatabaseConnection::class, get_class($databaseConnection));
    }

    public function testSaveCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "INSERT
                  INTO treadstone_semester(code, name)
                  VALUES(:code, :name)";
        $semester = array('code' => 'SS16', 'name' => 'Spring Semester 2016');

        $rowsModified = 1;

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($rowsModified);

        $semesterRepository = new SemesterRepository($databaseConnection);

        $this->assertEquals($rowsModified, $semesterRepository->save($semester));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bindMore($semester),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    public function testSaveDoesNotCallQueryIfAnyParametersAreMissing() {
        $codeMissing = array('name' => 'Spring Semester 2016');
        $nameMissing = array('code' => 'SS16');
        $bothMissing = array();

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');

        $semesterRepository = new SemesterRepository($databaseConnection);

        $this->assertEquals(0, $semesterRepository->save($codeMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $semesterRepository->save($nameMissing));
        Phake::verifyNoInteraction($databaseConnection);

        $this->assertEquals(0, $semesterRepository->save($bothMissing));
        Phake::verifyNoInteraction($databaseConnection);
    }

    public function testFindAllCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT *
                  FROM treadstone_semester";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindAllData());

        $semesterRepository = new SemesterRepository($databaseConnection);

        $this->assertSame($this->buildFindAllSemesters(), $semesterRepository->findAll());
    }

    public function testFindOneByCodeCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT *
                  FROM treadstone_semester
                  WHERE code = :code";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $semesterRepository = new SemesterRepository($databaseConnection);

        $codeKey = 'code';
        $codeValue = 'SS15';
        $this->assertSame($this->buildFindOneSemester(),
            $semesterRepository->findOneByCode($codeValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($codeKey, $codeValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneData() {
        $data[] = array('id' => 2, 'code' => 'SS16', 'name' => 'Spring Semester 2016');
        return $data;
    }

    private function buildFindAllData() {
        $data = $this->buildFindOneData();
        $data[] = array('id' => 3, 'code' => 'FS15', 'name' => 'Fall Semester 2015');
        return $data;
    }

    private function buildFindOneSemester() {
        $semester = array('id' => 2, 'code' => 'SS16', 'name' => 'Spring Semester 2016');
        return $semester;
    }

    private function buildFindAllSemesters() {
        $semesters['SS16'] = $this->buildFindOneSemester();
        $semesters['FS15'] = array('id' => 3, 'code' => 'FS15', 'name' => 'Fall Semester 2015');
        return $semesters;
    }
}

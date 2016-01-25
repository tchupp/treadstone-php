<?php

namespace Test\Unit;

use Api\Database\DatabaseConnection;
use Api\Database\SubjectRepository;
use Phake;
use Test\TreadstoneTestCase;

class SubjectRepositoryTest extends TreadstoneTestCase {

    public function testAutowire() {
        $subjectRepository = SubjectRepository::autowire();

        $databaseConnection = $this->getPrivateProperty($subjectRepository, 'databaseConnection');

        $this->assertEquals(DatabaseConnection::class, get_class($databaseConnection));
    }

    public function testSaveCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "INSERT
                  INTO treadstone_subject(code, name)
                  VALUES(:code, :name)";
        $subject = array('code' => 'ECE 410', 'name' => 'VSLI Design');

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
        $codeMissing = array('name' => 'VSLI Design');
        $nameMissing = array('code' => 'ECE 410');
        $bothMissing = array();

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

    public function testFindOneByCodeCallsQueryOnDatabaseConnectionWithCorrectQuery() {
        $query = "SELECT *
                  FROM treadstone_subject
                  WHERE code = :code";

        $databaseConnection = Phake::mock('Api\Database\DatabaseConnection');
        Phake::when($databaseConnection)
            ->query($query)
            ->thenReturn($this->buildFindOneData());

        $subjectRepository = new SubjectRepository($databaseConnection);

        $codeKey = 'code';
        $codeValue = 'ECE 410';
        $this->assertSame($this->buildFindOneSubject(),
            $subjectRepository->findOneByCode($codeValue));

        Phake::inOrder(
            Phake::verify($databaseConnection)->bind($codeKey, $codeValue),
            Phake::verify($databaseConnection)->query($query)
        );
    }

    private function buildFindOneData() {
        $data[] = array('id' => 10, 'code' => 'ECE 410', 'name' => 'VSLI Design');
        return $data;
    }

    private function buildFindAllData() {
        $data = $this->buildFindOneData();
        $data[] = array('id' => 11, 'code' => 'CSE 410', 'name' => 'Operating Systems');
        return $data;
    }

    private function buildFindOneSubject() {
        $subject = array('id' => 10, 'code' => 'ECE 410', 'name' => 'VSLI Design');
        return $subject;
    }

    private function buildFindAllSubjects() {
        $subjects['ECE 410'] = $this->buildFindOneSubject();
        $subjects['CSE 410'] = array('id' => 11, 'code' => 'CSE 410', 'name' => 'Operating Systems');
        return $subjects;
    }
}

<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Base class for all Controller Tests (funtional test)
 * 
 * Includes the setup up of the database layer to roll back the database changes after executio
 * 
 * All DB changes are rolled back automatically due to use of DAMADoctrineTestBundle
 * See https://github.com/dmaicher/doctrine-test-bundle
 */
class ControllerTestBase extends WebTestCase{

    protected $client;
    protected $em;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        /**
         * https://stackoverflow.com/questions/42295526/how-to-rollback-transactions-when-doing-functional-testing-with-symfony2
         */
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        parent::tearDown();
    
        if($this->em->getConnection()->isTransactionActive()) {
            $this->em->rollback();
        }        
    }

}

?>
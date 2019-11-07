<?php

namespace App\Tests\Controller;

use App\Dao\UserDao;

/**
 * Controller test for the user controller
 * Calls the api interface and executes various tests
 * 
 * Test executed:
 * /api/user: update of existing user
 * - Confirm that the call is successfull
 * - Confirm that the content type is set to json
 * - Confirms that the attributes are updated after the call
 * - Confirm that the count of the posts increased by one
 * 
 */
class UserControllerTest extends ControllerTestBase{


    const USER_ID = 2;

    /**
     * Confirm that the current user is loaded correctly
     */
    public function testApi_getCurrentUser(){
        $this->client->request(
            'GET', 
            '/api/current_user', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'ok'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'ok');
        //Verify that a user exists
        $this->assertObjectHasAttribute('user', json_decode($this->client->getResponse()->getContent()));
        //Verify that the user has a valid id
        $this->assertGreaterThan(0, json_decode($this->client->getResponse()->getContent())->user->id);
    }

    /**
     * Positive test to verify that the database is updated correctly
     */
    public function testApi_updateUser_positive(){
        /*******************
         * Preparation for the test
         * Clone the user (required to ensure the same object is not used for both due to caching)
         */
        $userBeforeRequest = clone UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID);
        /*******************
         * Execution of the test
         */
        $this->client->request(
            'POST', 
            '/api/user', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{ "id": "' . UserControllerTest::USER_ID . '", 
                "attributes" : {
                    "firstName": "dummyLast",
                    "lastName": "dummyFirst",
                    "nickname": "dummydummy",
                    "dateOfBirth": "01.01.1900 00:00:00",
                    "email": "dummydummy@dummy.de"
                }
            }'
        );
        /*******************
         * Verification of test results / metrics
         */
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'ok'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'ok');
        //Verify that the request was sent as json
        $this->assertEquals(
            $this->client->getRequest()->headers->all()
                ['content-type'][0], 
            'application/json'
        );
        //user instance after change
        $userAfterChange = UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID);
        //Verify that the attributes has changes
        $this->assertNotEquals(
            $userBeforeRequest->getFirstName(), 
            $userAfterChange->getFirstName() 
        );
        //Verify that the first name has changes
        $this->assertNotEquals(
            $userBeforeRequest->getLastName(), 
            $userAfterChange->getLastName() 
        );
        //Verify that the first name has changes
        $this->assertNotEquals(
            $userBeforeRequest->getNickname(), 
            $userAfterChange->getNickname() 
        );
        //Verify that the first name has changes
        $this->assertNotEquals(
            $userBeforeRequest->getDateOfBirth(), 
            $userAfterChange->getDateOfBirth() 
        );
    }
    /**
     * Negativ test to verify that the api breaks controlled in case of wrong attribute name
     */
    public function testApi_updateUser_negativ_wrongAttributeName(){
        
        /*******************
         * Preparation for the test
         */
        $userFirstNameBeforeRequest = UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID)->getFirstName();
        /*******************
         * Execution of the test
         * firstNames instead of firstname; The response should return a failure
         */
        $this->client->request(
            'POST', 
            '/api/user', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{ "id": "' . UserControllerTest::USER_ID . '", 
                "attributes" : {
                    "firstNames": "Peter"
                }
            }'
        );
        /*******************
         * Verification of test results / metrics
         */
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'failed'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'failed');
        //Verify that the errorCode is 2
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->errorCode, 2);
        //Verify that the request was sent as json
        $this->assertEquals(
            $this->client->getRequest()->headers->all()
                ['content-type'][0], 
            'application/json'
        );
        //Verify that the first name has not changed due to the failure (used as proof that other have not changed too)
        $this->assertEquals(
            $userFirstNameBeforeRequest, 
            UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID)->getFirstName() 
        );
        
    }

    /**
     * Negativ test to verify that the api breaks controlled in case of wrong user id
     */
    public function testApi_updateUser_negativ_wrongUserID(){
        
        /*******************
         * Preparation for the test
         */
        $userFirstNameBeforeRequest = UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID)->getFirstName();
        /*******************
         * Execution of the test
         * firstNames instead of firstname; The response should return a failure
         */
        $this->client->request(
            'POST', 
            '/api/user', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{ "id": "' . 0 . '", 
                "attributes" : {
                    "firstName": "Peter"
                }
            }'
        );
        /*******************
         * Verification of test results / metrics
         */
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'failed'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'failed');
        //Verify that the errorCode is 2
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->errorCode, 4);
        //Verify that the request was sent as json
        $this->assertEquals(
            $this->client->getRequest()->headers->all()
                ['content-type'][0], 
            'application/json'
        );
        //Verify that the first name has not changed due to the failure
        $this->assertEquals(
            $userFirstNameBeforeRequest, 
            UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID)->getFirstName() 
        );
        
    }

    /**
     * Negativ test to verify that the api breaks controlled in case of wrong user id
     * // TODO: not yet completed
     */
    public function testApi_updateUser_negativ_invalidEmail(){
        
        /*******************
         * Preparation for the test
         */
        $userFirstNameBeforeRequest = UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID)->getFirstName();
        /*******************
         * Execution of the test
         * firstNames instead of firstname; The response should return a failure
         */
        $this->client->request(
            'POST', 
            '/api/user', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{ "id": "' . UserControllerTest::USER_ID . '", 
                "attributes" : {
                    "email": "Peter"
                }
            }'
        );
        /*******************
         * Verification of test results / metrics
         */
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'failed'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'failed');
        //Verify that the errorCode is 2
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->errorCode, 5);
        $this->assertEquals(1, count(json_decode($this->client->getResponse()->getContent())->validationErrors));
        $this->assertEquals('email', json_decode($this->client->getResponse()->getContent())->validationErrors[0]->field);
        //Verify that the request was sent as json
        $this->assertEquals(
            $this->client->getRequest()->headers->all()
                ['content-type'][0], 
            'application/json'
        );
        //Verify that the first name has not changed due to the failure
        $this->assertEquals(
            $userFirstNameBeforeRequest, 
            UserDao::getInstance()->getUser($this->em, UserControllerTest::USER_ID)->getFirstName() 
        );
        
    }
    
}

?>
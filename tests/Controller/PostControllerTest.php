<?php
namespace App\Tests\Controller;

use App\Dao\PostDao;

/**
 * Controller test for the post controller
 * Calls the api interface and executes various tests
 * 
 * Test executed:
 * /api/post: Creation of new post
 * - Confirm that the call is successfull
 * - Confirm that the content type is set to json
 * - Confirms that the return status is 'ok'
 * - Confirms that a number (id of the created post) is returned
 * - Confirm that the count of the posts increased by one
 * 
 */
class PostControllerTest extends ControllerTestBase{

    /**
     * Function to test the api to load posts
     */
    public function testApi_getPosts(){
        $this->client->request(
            'GET', 
            '/api/posts', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'ok'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'ok');
        //Verify that a user exists
        $this->assertObjectHasAttribute('posts', json_decode($this->client->getResponse()->getContent()));
    }
    /**
     * Function to test the api to create a new post
     */
    public function testApi_createPost_positive(){
        /*******************
         * Preparation for the test
         */
        //Get count before the test 
        // TODO: Ensure that this doesn't interfere with other test activities which might also increase the count
        $countBeforeTest = PostDao::getInstance()->getCount($this->em);
        // enable the profiler only for the next request (if you make
        // new requests, you must call this method again)
        // (it does nothing if the profiler is not available)
        $this->client->enableProfiler();

        /*******************
         * Execution of the request
         */
        $result = $this->client->request(
            'POST', 
            '/api/post', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{
                "title": "The Title", 
                "postText": "The Post Text"
            }'
        );
        /*******************
         * Verification of test results / metrics
         */
        //Verify that the request was sent as json
        $this->assertEquals(
            $this->client->getRequest()->headers->all()
                ['content-type'][0], 
            'application/json'
        );
        
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'ok'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'ok');
        //Verify that the response (which is the id of the newly inserted entity) is greater 0
        $this->assertGreaterThan(0, (int)json_decode($this->client->getResponse()->getContent())->id);
        //Verify that one row was inserted into the database
        $this->assertEquals(
            $countBeforeTest + 1, 
            PostDao::getInstance()->getCount($this->em)
        );
    }
    public function testApi_createPost_negative_WrongAttributeName(){
        /*******************
         * Execution of the request
         */
        $result = $this->client->request(
            'POST', 
            '/api/post', 
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{
                "titles": "The Title"
            }'
        );
        //Verify that the request as such was successfull
        $this->assertResponseIsSuccessful();
        //Verify that the return status is 'failed'
        $this->assertEquals(json_decode($this->client->getResponse()->getContent())->status, 'failed');
        //Verify that the errorCode is 2
        $this->assertEquals(2, json_decode($this->client->getResponse()->getContent())->errorCode);
    }
}

?>
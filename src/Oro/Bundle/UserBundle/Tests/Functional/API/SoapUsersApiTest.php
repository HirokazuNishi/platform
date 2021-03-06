<?php

namespace Oro\Bundle\UserBundle\Tests\Functional\API;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Bundle\TestFrameworkBundle\Test\ToolsAPI;
use Oro\Bundle\TestFrameworkBundle\Test\Client;

/**
 * @outputBuffering enabled
 * @db_isolation
 */
class SoapUsersApiTest extends WebTestCase
{
    /** Default value for role label */
    const DEFAULT_VALUE = 'USER_LABEL';

    /** @var \SoapClient */
    protected $client = null;

    public function setUp()
    {
        $this->client = static::createClient(array(), ToolsAPI::generateWsseHeader());

        $this->client->soap(
            "http://localhost/api/soap",
            array(
                'location' => 'http://localhost/api/soap',
                'soap_version' => SOAP_1_2
            )
        );
    }

    /**
     * @param string $request
     * @param array  $response
     *
     * @dataProvider requestsApi
     */
    public function testCreateUser($request, $response)
    {
        $result = $this->client->soapClient->createUser($request);
        $result = ToolsAPI::classToArray($result);
        ToolsAPI::assertEqualsResponse($response, $result, $this->client->soapClient->__getLastResponse());
    }

    /**
     * @param string $request
     * @param array  $response
     *
     * @dataProvider requestsApi
     * @depends testCreateUser
     */
    public function testUpdateUser($request, $response)
    {
        //get user id
        $userId = $this->client->soapClient->getUserBy(array('item' => array('key' =>'username', 'value' =>$request['username'])));
        $userId = ToolsAPI::classToArray($userId);

        $request['username'] = 'Updated_' . $request['username'];
        $request['email'] = 'Updated_' . $request['email'];
        unset($request['plainPassword']);
        $result = $this->client->soapClient->updateUser($userId['id'], $request);
        $result = ToolsAPI::classToArray($result);
        ToolsAPI::assertEqualsResponse($response, $result);
        $user = $this->client->soapClient->getUser($userId['id']);
        $user = ToolsAPI::classToArray($user);
        $this->assertEquals($request['username'], $user['username']);
        $this->assertEquals($request['email'], $user['email']);
    }

    /**
     * @dataProvider requestsApi
     * @depends testUpdateUser
     */
    public function testGetUsers($request, $response)
    {
        $users = $this->client->soapClient->getUsers(1, 1000);
        $users = ToolsAPI::classToArray($users);
        $result = false;
        foreach ($users as $user) {
            foreach ($user as $userDetails) {
                $result = $userDetails['username'] == 'Updated_' . $request['username'];
                if ($result) {
                    break;
                }
            }
        }
        $this->assertTrue($result);
    }

    /**
     * @dataProvider requestsApi
     * @depends testGetUsers
     */
    public function testDeleteUser($request)
    {
        //get user id
        $userId = $this->client->soapClient->getUserBy(
            array(
                'item' => array(
                    'key' =>'username',
                    'value' =>'Updated_' . $request['username'])
            )
        );
        $userId = ToolsAPI::classToArray($userId);
        $result = $this->client->soapClient->deleteUser($userId['id']);
        $this->assertTrue($result);
        try {
            $this->client->soapClient->getUserBy(
                array(
                    'item' => array(
                        'key' =>'username',
                        'value' =>'Updated_' . $request['username'])
                )
            );
        } catch (\SoapFault $e) {
            if ($e->faultcode != 'NOT_FOUND') {
                throw $e;
            }
        }
    }

    /**
     * Data provider for REST API tests
     *
     * @return array
     */
    public function requestsApi()
    {
        return ToolsAPI::requestsApi(__DIR__ . DIRECTORY_SEPARATOR . 'UserRequest');
    }
}
